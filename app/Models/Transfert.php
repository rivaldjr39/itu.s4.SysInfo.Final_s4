<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class Transfert extends Model
{
    protected $table      = 'operations';
    protected $primaryKey = 'id';

    // CodeIgniter utilise $allowedFields, pas $fillable (qui est du Laravel)
    protected $allowedFields = [
        'reference',
        'type_operation_id',
        'compte_source_id',
        'compte_destination_id',
        'montant',
        'frais',
        'montant_total',
        'bareme_frais_id',
        'statut',
        'date_operation',
    ];

    protected $useTimestamps = false;

    // ID du type d'opération "TRANSFERT" dans la table types_operations
    protected int $typeOperationTransfert = 3;
    protected array $statutCache = [];
    protected array $commissionCache = [];

    protected function normaliserNumeroTelephone(string $numero): string
    {
        return preg_replace('/\D+/', '', $numero) ?? '';
    }

    protected function getStatutId(string $libelle): int
    {
        if (isset($this->statutCache[$libelle])) {
            return $this->statutCache[$libelle];
        }

        $statut = $this->db->table('statut')
            ->where('libelle', $libelle)
            ->get()
            ->getRowArray();

        if (!$statut) {
            throw new Exception("Le statut '$libelle' n'existe pas dans la table statut.");
        }

        $this->statutCache[$libelle] = (int) $statut['id'];

        return $this->statutCache[$libelle];
    }

    protected function getCommissionInterOperateur(int $operateurId): float
    {
        if (isset($this->commissionCache[$operateurId])) {
            return $this->commissionCache[$operateurId];
        }

        $commission = $this->db->table('configurations_commissions')
            ->select('commission_pourcentage')
            ->where('operateur_id', $operateurId)
            ->where('type_operation_id', $this->typeOperationTransfert)
            ->where('autre_operateur', 1)
            ->groupStart()
                ->where('date_fin IS NULL', null, false)
                ->orWhere('date_fin >', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->orderBy('date_debut', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $this->commissionCache[$operateurId] = $commission ? (float) $commission['commission_pourcentage'] : 0.0;

        return $this->commissionCache[$operateurId];
    }

    public function calculerFraisTransfert(array $bareme, float $montant, int $operateurSourceId, int $operateurDestinationId): array
    {
        $fraisBase = $this->calculerFrais($bareme, $montant);
        $commissionSupplementaire = 0.0;

        if ($operateurSourceId !== $operateurDestinationId) {
            $commissionSupplementaire = round(
                $montant * $this->getCommissionInterOperateur($operateurDestinationId) / 100,
                2
            );
        }

        return [
            'frais_base' => $fraisBase,
            'commission_supplementaire' => $commissionSupplementaire,
            'frais_total' => round($fraisBase + $commissionSupplementaire, 2),
            'inter_operateur' => $operateurSourceId !== $operateurDestinationId,
        ];
    }

    // ------------------------------------------------------------
    // 1. Récupérer le barème de frais applicable à un montant donné
    // ------------------------------------------------------------
    public function getBaremeFrais(float $montant): ?array
    {
        $bareme = $this->db->table('baremes_frais')
            ->where('type_operation_id', $this->typeOperationTransfert)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->groupStart()
                ->where('date_fin IS NULL', null, false)
                ->orWhere('date_fin >', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->orderBy('date_debut', 'DESC')
            ->get()
            ->getRowArray();

        return $bareme ?: null;
    }

    // ------------------------------------------------------------
    // 1 bis. Récupérer le solde du client connecté
    // ------------------------------------------------------------
    public function getSolde(int $clientId): ?float
    {
        $compte = $this->db->table('comptes')
            ->select('COALESCE(solde, 0) AS solde')
            ->where('client_id', $clientId)
            ->limit(1)
            ->get()
            ->getRowArray();
        if (!$compte) {
            return null;
        }
        return (float) $compte['solde'];
    }

    // ------------------------------------------------------------
    // 2. Calculer le montant des frais à partir d'un barème
    // ------------------------------------------------------------
    public function calculerFrais(array $bareme, float $montant): float
    {
        $fraisFixe = (float) $bareme['frais_fixe'];
        $fraisPourcentage = (float) $bareme['frais_pourcentage'];

        return round($fraisFixe + ($montant * $fraisPourcentage / 100), 2);
    }

    // ------------------------------------------------------------
    // 3. Trouver un compte à partir d'un numéro de téléphone
    // ------------------------------------------------------------
    public function getCompteParNumero(string $numero): ?array
    {
        $numero = $this->normaliserNumeroTelephone($numero);

        if ($numero === '') {
            return null;
        }

        $compte = $this->db->table('comptes co')
            ->select('COALESCE(co.id, co.rowid) AS id, co.client_id, co.solde, co.date_creation, cl.prefixe_id, p.id_operateur AS operateur_id')
            ->join('client cl', 'COALESCE(cl.id, cl.rowid) = co.client_id')
            ->join('prefixes p', 'COALESCE(p.id, p.rowid) = cl.prefixe_id', 'left')
            ->where('cl.numero_telephone', $numero)
            ->limit(1)
            ->get()
            ->getRowArray();

        return $compte ?: null;
    }

    // ------------------------------------------------------------
    // 4. Vérifier que le compte source a un solde suffisant
    //    (montant transféré + frais, si les frais sont à la charge de l'émetteur)
    // ------------------------------------------------------------
    public function soldeSuffisant(array $compte, float $montantTotal): bool
    {
        return (float) $compte['solde'] >= $montantTotal;
    }

    // ------------------------------------------------------------
    // 5. Générer une référence unique pour l'opération
    // ------------------------------------------------------------
    public function genererReference(): string
    {
        return 'TRF' . date('YmdHis') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }

    // ------------------------------------------------------------
    // 6. FONCTION PRINCIPALE : effectuer un transfert
    //    Retourne ['success' => bool, 'message' => string, 'reference' => string|null]
    // ------------------------------------------------------------
    public function effectuerTransfert(string $numeroSource, string $numeroDestination, float $montant): array
    {
        $numeroSource = $this->normaliserNumeroTelephone($numeroSource);
        $numeroDestination = $this->normaliserNumeroTelephone($numeroDestination);

        if ($montant <= 0) {
            return ['success' => false, 'message' => 'Montant invalide.'];
        }

        if ($numeroSource === $numeroDestination) {
            return ['success' => false, 'message' => 'Impossible de transférer vers son propre numéro.'];
        }

        $compteSource = $this->getCompteParNumero($numeroSource);
        $compteDestination = $this->getCompteParNumero($numeroDestination);

        if (!$compteSource) {
            return ['success' => false, 'message' => "Le numéro émetteur $numeroSource n'existe pas."];
        }
        if (!$compteDestination) {
            return ['success' => false, 'message' => "Le numéro destinataire $numeroDestination n'existe pas."];
        }

        $bareme = $this->getBaremeFrais($montant);
        if (!$bareme) {
            return ['success' => false, 'message' => 'Aucun barème de frais trouvé pour ce montant.'];
        }

        $fraisDetails = $this->calculerFraisTransfert(
            $bareme,
            $montant,
            (int) ($compteSource['operateur_id'] ?? 0),
            (int) ($compteDestination['operateur_id'] ?? 0)
        );
        $montantTotal = $montant + $fraisDetails['frais_total'];

        if (!$this->soldeSuffisant($compteSource, $montantTotal)) {
            return ['success' => false, 'message' => 'Solde insuffisant (montant + frais).'];
        }

        $reference = $this->genererReference();

        $this->db->transStart();

        try {
            // Débit du compte source
            $this->db->table('comptes')
                ->where('client_id', $compteSource['client_id'])
                ->set('solde', 'solde - ' . $montantTotal, false)
                ->update();

            // Crédit du compte destination (montant net, sans les frais)
            $this->db->table('comptes')
                ->where('client_id', $compteDestination['client_id'])
                ->set('solde', 'solde + ' . $montant, false)
                ->update();

            // Enregistrement de l'opération
            $this->insert([
                'reference'              => $reference,
                'type_operation_id'      => $this->typeOperationTransfert,
                'compte_source_id'       => $compteSource['id'],
                'compte_destination_id'  => $compteDestination['id'],
                'montant'                => $montant,
                'frais'                  => $fraisDetails['frais_total'],
                'montant_total'          => $montantTotal,
                'bareme_frais_id'        => $bareme['id'],
                'statut'                 => $this->getStatutId('REUSSI'),
                'date_operation'         => date('Y-m-d H:i:s'),
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new Exception('Échec de la transaction SQL.');
            }

            return [
                'success'   => true,
                'message'   => 'Transfert effectué avec succès.',
                'reference' => $reference,
                'frais'     => $fraisDetails['frais_total'],
                'frais_base' => $fraisDetails['frais_base'],
                'commission_supplementaire' => $fraisDetails['commission_supplementaire'],
            ];
        } catch (Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Erreur lors du transfert : ' . $e->getMessage()];
        }
    }

    public function effectuerTransfertsMultiple(string $numeroSource, array $numerosDestinations, float $montantTotal): array
    {
        $numeroSource = $this->normaliserNumeroTelephone($numeroSource);

        $totalRecipients = count($numerosDestinations);

        if ($totalRecipients < 2) {
            return ['success' => false, 'message' => 'Veuillez spécifier au moins deux destinataires.'];
        }

        if ($montantTotal <= 0) {
            return ['success' => false, 'message' => 'Montant total invalide.'];
        }

        // Montant pour chaque destinataire (arrondi à l'ariary inférieur)
        $montantParPersonne = floor($montantTotal / $totalRecipients);

        if ($montantParPersonne <= 0) {
            return ['success' => false, 'message' => 'Le montant par bénéficiaire est trop faible.'];
        }

        // Vérifier le compte source
        $compteSource = $this->getCompteParNumero($numeroSource);
        if (!$compteSource) {
            return ['success' => false, 'message' => "Le numéro émetteur $numeroSource n'existe pas."];
        }

        // Vérifier que tous les destinataires existent, ne sont pas l'émetteur, et ont le même opérateur
        $comptesDestinations = [];
        $operateurSourceId = (int) ($compteSource['operateur_id'] ?? 0);
        foreach ($numerosDestinations as $numero) {
            $numero = $this->normaliserNumeroTelephone($numero);

            if ($numero === $numeroSource) {
                return ['success' => false, 'message' => 'Impossible de transférer vers son propre numéro (' . $numero . ').'];
            }

            $compte = $this->getCompteParNumero($numero);
            if (!$compte) {
                return ['success' => false, 'message' => "Le numéro destinataire $numero n'existe pas."];
            }

            $operateurDestId = (int) ($compte['operateur_id'] ?? 0);
            if ($operateurDestId !== $operateurSourceId) {
                return ['success' => false, 'message' => "Le numéro $numero n'est pas du même opérateur. Seuls les transferts vers le même opérateur sont autorisés."];
            }

            $comptesDestinations[] = $compte;
        }

        // Calculer les frais pour ce montant unitaire et estimer le débit total réel
        $bareme = $this->getBaremeFrais($montantParPersonne);
        if (!$bareme) {
            return ['success' => false, 'message' => 'Aucun barème de frais trouvé pour ce montant.'];
        }

        $detailsPrecalculs = [];
        $montantTotalADebiter = 0.0;
        foreach ($comptesDestinations as $compteDest) {
            $fraisDetails = $this->calculerFraisTransfert(
                $bareme,
                $montantParPersonne,
                (int) ($compteSource['operateur_id'] ?? 0),
                (int) ($compteDest['operateur_id'] ?? 0)
            );

            $detailsPrecalculs[] = $fraisDetails;
            $montantTotalADebiter += $montantParPersonne + $fraisDetails['frais_total'];
        }

        // Vérifier le solde
        if (!$this->soldeSuffisant($compteSource, $montantTotalADebiter)) {
            return ['success' => false, 'message' => 'Solde insuffisant pour effectuer tous les transferts.'];
        }

        $this->db->transStart();

        try {
            $references = [];
            $details = [];
            $fraisTotal = 0.0;

            // Débiter le compte source du montant total
            $this->db->table('comptes')
                ->where('client_id', $compteSource['client_id'])
                ->set('solde', 'solde - ' . $montantTotalADebiter, false)
                ->update();

            foreach ($comptesDestinations as $i => $compteDest) {
                $fraisDetails = $detailsPrecalculs[$i];

                // Créditer chaque destination
                $this->db->table('comptes')
                    ->where('client_id', $compteDest['client_id'])
                    ->set('solde', 'solde + ' . $montantParPersonne, false)
                    ->update();

                $reference = $this->genererReference();

                // Enregistrer l'opération
                $this->insert([
                    'reference'              => $reference,
                    'type_operation_id'      => $this->typeOperationTransfert,
                    'compte_source_id'       => $compteSource['id'],
                    'compte_destination_id'  => $compteDest['id'],
                    'montant'                => $montantParPersonne,
                    'frais'                  => $fraisDetails['frais_total'],
                    'montant_total'          => $montantParPersonne + $fraisDetails['frais_total'],
                    'bareme_frais_id'        => $bareme['id'],
                    'statut'                 => $this->getStatutId('REUSSI'),
                    'date_operation'         => date('Y-m-d H:i:s'),
                ]);

                $references[] = $reference;
                $fraisTotal += $fraisDetails['frais_total'];
                $details[] = [
                    'numero'     => $numerosDestinations[$i],
                    'montant'    => $montantParPersonne,
                    'frais'      => $fraisDetails['frais_total'],
                    'frais_base' => $fraisDetails['frais_base'],
                    'commission_supplementaire' => $fraisDetails['commission_supplementaire'],
                    'reference'  => $reference,
                ];
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new Exception('Échec de la transaction SQL.');
            }

            return [
                'success'    => true,
                'message'    => count($details) . ' transfert(s) effectué(s) avec succès.',
                'details'    => $details,
                'frais_total' => $fraisTotal,
                'montant_par_personne' => $montantParPersonne,
            ];
        } catch (Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Erreur lors des transferts : ' . $e->getMessage()];
        }
    }


    public function getHistoriqueTransferts(int $clientId, int $limite = 20): array
    {
        return $this->db->table('operations o')
            ->select('o.*, cs.client_id AS source_client, cd.client_id AS dest_client,
                      cls.numero_telephone AS numero_source, cld.numero_telephone AS numero_destination,
                      st.libelle AS statut_libelle')
            ->join('comptes cs', 'COALESCE(cs.id, cs.rowid) = o.compte_source_id', 'left')
            ->join('comptes cd', 'COALESCE(cd.id, cd.rowid) = o.compte_destination_id', 'left')
            ->join('client cls', 'COALESCE(cls.id, cls.rowid) = cs.client_id', 'left')
            ->join('client cld', 'COALESCE(cld.id, cld.rowid) = cd.client_id', 'left')
            ->join('statut st', 'st.id = o.statut', 'left')
            ->where('o.type_operation_id', $this->typeOperationTransfert)
            ->groupStart()
                ->where('cs.client_id', $clientId)
                ->orWhere('cd.client_id', $clientId)
            ->groupEnd()
            ->orderBy('o.date_operation', 'DESC')
            ->limit($limite)
            ->get()
            ->getResultArray();
    }


    public function getStatsFrais(?string $dateDebut = null, ?string $dateFin = null): array
    {
        $statutReussi = $this->getStatutId('REUSSI');

        $builderRetraits = $this->db->table('operations o')
            ->select("
                op.id AS operateur_id,
                op.nom AS operateur_nom,
                'RETRAIT' AS type_operation,
                COUNT(o.id) AS nb_operations,
                COALESCE(SUM(o.frais), 0) AS total_frais,
                COALESCE(AVG(o.frais), 0) AS moyenne_frais,
                COALESCE(SUM(o.montant), 0) AS total_montant
            ")
            ->join('comptes cs', 'COALESCE(cs.id, cs.rowid) = o.compte_source_id')
            ->join('client cls', 'COALESCE(cls.id, cls.rowid) = cs.client_id')
            ->join('prefixes ps', 'ps.id = cls.prefixe_id')
            ->join('operateurs op', 'op.id = ps.id_operateur')
            ->where('o.type_operation_id', 2)   // RETRAIT
            ->where('o.statut', $statutReussi);

        if ($dateDebut) {
            $builderRetraits->where('o.date_operation >=', $dateDebut);
        }
        if ($dateFin) {
            $builderRetraits->where('o.date_operation <=', $dateFin);
        }

        $retraits = $builderRetraits
            ->groupBy('op.id, op.nom')
            ->orderBy('op.nom', 'ASC')
            ->get()
            ->getResultArray();

        $builderTransferts = $this->db->table('operations o')
            ->select("
                op.id AS operateur_id,
                op.nom AS operateur_nom,
                CASE WHEN ps.id_operateur = pd.id_operateur THEN 'MEME_OPERATEUR' ELSE 'AUTRE_OPERATEUR' END AS type_operateur,
                COUNT(o.id) AS nb_operations,
                COALESCE(SUM(o.frais), 0) AS total_frais,
                COALESCE(AVG(o.frais), 0) AS moyenne_frais,
                COALESCE(SUM(o.montant), 0) AS total_montant
            ")
            ->join('comptes cs', 'COALESCE(cs.id, cs.rowid) = o.compte_source_id')
            ->join('client cls', 'COALESCE(cls.id, cls.rowid) = cs.client_id')
            ->join('prefixes ps', 'ps.id = cls.prefixe_id')
            ->join('operateurs op', 'op.id = ps.id_operateur')
            ->join('comptes cd', 'COALESCE(cd.id, cd.rowid) = o.compte_destination_id')
            ->join('client cld', 'COALESCE(cld.id, cld.rowid) = cd.client_id')
            ->join('prefixes pd', 'pd.id = cld.prefixe_id')
            ->where('o.type_operation_id', 3)   // TRANSFERT
            ->where('o.statut', $statutReussi);

        if ($dateDebut) {
            $builderTransferts->where('o.date_operation >=', $dateDebut);
        }
        if ($dateFin) {
            $builderTransferts->where('o.date_operation <=', $dateFin);
        }

        $transferts = $builderTransferts
            ->groupBy('op.id, op.nom, type_operateur')
            ->orderBy('op.nom', 'ASC')
            ->orderBy('type_operateur', 'ASC')
            ->get()
            ->getResultArray();

        // ---- 3. Fusionner les résultats par opérateur ----
        $operateurs = [];

        // Traiter les retraits
        foreach ($retraits as $r) {
            $opId = (int) $r['operateur_id'];
            $operateurs[$opId] = [
                'operateur_id'   => $opId,
                'operateur_nom'  => $r['operateur_nom'],
                'retrait_nb'      => (int) $r['nb_operations'],
                'retrait_frais'   => (float) $r['total_frais'],
                'retrait_moyenne' => (float) $r['moyenne_frais'],
                'retrait_montant' => (float) $r['total_montant'],
                'meme_nb'         => 0,
                'meme_frais'      => 0.0,
                'meme_moyenne'    => 0.0,
                'meme_montant'    => 0.0,
                'autre_nb'        => 0,
                'autre_frais'     => 0.0,
                'autre_moyenne'   => 0.0,
                'autre_montant'   => 0.0,
            ];
        }

        // Traiter les transferts
        foreach ($transferts as $t) {
            $opId = (int) $t['operateur_id'];
            if (!isset($operateurs[$opId])) {
                $operateurs[$opId] = [
                    'operateur_id'   => $opId,
                    'operateur_nom'  => $t['operateur_nom'],
                    'retrait_nb'      => 0,
                    'retrait_frais'   => 0.0,
                    'retrait_moyenne' => 0.0,
                    'retrait_montant' => 0.0,
                    'meme_nb'         => 0,
                    'meme_frais'      => 0.0,
                    'meme_moyenne'    => 0.0,
                    'meme_montant'    => 0.0,
                    'autre_nb'        => 0,
                    'autre_frais'     => 0.0,
                    'autre_moyenne'   => 0.0,
                    'autre_montant'   => 0.0,
                ];
            }

            $isMeme = ($t['type_operateur'] ?? '') === 'MEME_OPERATEUR';
            $key = $isMeme ? 'meme' : 'autre';
            $operateurs[$opId]["{$key}_nb"]      = (int) $t['nb_operations'];
            $operateurs[$opId]["{$key}_frais"]   = (float) $t['total_frais'];
            $operateurs[$opId]["{$key}_moyenne"] = (float) $t['moyenne_frais'];
            $operateurs[$opId]["{$key}_montant"] = (float) $t['total_montant'];
        }

        // ---- 4. Calculer les totaux généraux ----
        $totalFrais     = 0.0;
        $totalOperations = 0;
        $totalMontant   = 0.0;

        foreach ($operateurs as &$op) {
            $op['total_frais'] = $op['retrait_frais'] + $op['meme_frais'] + $op['autre_frais'];
            $op['total_operations'] = $op['retrait_nb'] + $op['meme_nb'] + $op['autre_nb'];
            $op['total_montant'] = $op['retrait_montant'] + $op['meme_montant'] + $op['autre_montant'];
            $totalFrais      += $op['total_frais'];
            $totalOperations += $op['total_operations'];
            $totalMontant    += $op['total_montant'];
        }
        unset($op);

        return [
            'operateurs'      => array_values($operateurs),
            'total_frais'     => $totalFrais,
            'total_operations'=> $totalOperations,
            'total_montant'   => $totalMontant,
            'date_debut'      => $dateDebut,
            'date_fin'        => $dateFin,
        ];
    }

    public function getHistoriqueGlobal(int $clientId, int $limite = 20): array
    {
        // Récupérer le ou les comptes du client
        $comptesClient = $this->db->table('comptes')
            ->where('client_id', $clientId)
            ->get()
            ->getResultArray();

        $compteIds = array_map(function($c) {
            return (int) ($c['id'] ?? $c['rowid']);
        }, $comptesClient);

        if (empty($compteIds)) {
            return [];
        }

        return $this->db->table('operations o')
            ->select('o.*, cs.client_id AS source_client, cd.client_id AS dest_client,
                      cls.numero_telephone AS numero_source, cld.numero_telephone AS numero_destination,
                      top.libelle AS type_operation_libelle, top.code AS type_operation_code,
                      st.libelle AS statut_libelle')
            ->join('comptes cs', 'COALESCE(cs.id, cs.rowid) = o.compte_source_id', 'left')
            ->join('comptes cd', 'COALESCE(cd.id, cd.rowid) = o.compte_destination_id', 'left')
            ->join('client cls', 'COALESCE(cls.id, cls.rowid) = cs.client_id', 'left')
            ->join('client cld', 'COALESCE(cld.id, cld.rowid) = cd.client_id', 'left')
            ->join('types_operations top', 'COALESCE(top.id, top.rowid) = o.type_operation_id', 'left')
            ->join('statut st', 'st.id = o.statut', 'left')
            ->whereIn('o.compte_source_id', $compteIds, false)
            ->orWhereIn('o.compte_destination_id', $compteIds, false)
            ->orderBy('o.date_operation', 'DESC')
            ->limit($limite)
            ->get()
            ->getResultArray();
    }
}