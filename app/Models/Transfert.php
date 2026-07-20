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
        $compte = $this->db->table('comptes co')
            ->select('COALESCE(co.id, co.rowid) AS id, co.client_id, co.solde, co.date_creation')
            ->join('client cl', 'COALESCE(cl.id, cl.rowid) = co.client_id')
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

        $frais = $this->calculerFrais($bareme, $montant);
        $montantTotal = $montant + $frais;

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
                'frais'                  => $frais,
                'montant_total'          => $montantTotal,
                'bareme_frais_id'        => $bareme['id'],
                'statut'                 => 'REUSSI',
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
                'frais'     => $frais,
            ];
        } catch (Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Erreur lors du transfert : ' . $e->getMessage()];
        }
    }

    // ------------------------------------------------------------
    // 7. Historique des transferts d'un client (émis + reçus)
    // ------------------------------------------------------------
    public function getHistoriqueTransferts(int $clientId, int $limite = 20): array
    {
        return $this->db->table('operations o')
            ->select('o.*, cs.client_id AS source_client, cd.client_id AS dest_client,
                      cls.numero_telephone AS numero_source, cld.numero_telephone AS numero_destination')
            ->join('comptes cs', 'COALESCE(cs.id, cs.rowid) = o.compte_source_id', 'left')
            ->join('comptes cd', 'COALESCE(cd.id, cd.rowid) = o.compte_destination_id', 'left')
            ->join('client cls', 'COALESCE(cls.id, cls.rowid) = cs.client_id', 'left')
            ->join('client cld', 'COALESCE(cld.id, cld.rowid) = cd.client_id', 'left')
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

    // ------------------------------------------------------------
    // 8. Historique global d'un client (transferts + retraits)
    // ------------------------------------------------------------
    public function getHistoriqueGlobal(int $clientId, int $limite = 20): array
    {
        return $this->db->table('operations o')
            ->select('o.*, cs.client_id AS source_client, cd.client_id AS dest_client,
                      cls.numero_telephone AS numero_source, cld.numero_telephone AS numero_destination,
                      top.libelle AS type_operation_libelle, top.code AS type_operation_code')
            ->join('comptes cs', 'COALESCE(cs.id, cs.rowid) = o.compte_source_id', 'left')
            ->join('comptes cd', 'COALESCE(cd.id, cd.rowid) = o.compte_destination_id', 'left')
            ->join('client cls', 'COALESCE(cls.id, cls.rowid) = cs.client_id', 'left')
            ->join('client cld', 'COALESCE(cld.id, cld.rowid) = cd.client_id', 'left')
            ->join('types_operations top', 'COALESCE(top.id, top.rowid) = o.type_operation_id', 'left')
            ->groupStart()
                ->where('cs.client_id', $clientId)
                ->orWhere('cd.client_id', $clientId)
            ->groupEnd()
            ->orderBy('o.date_operation', 'DESC')
            ->limit($limite)
            ->get()
            ->getResultArray();
    }
}