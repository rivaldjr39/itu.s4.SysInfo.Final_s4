<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class Retrait extends Model
{
    protected $table      = 'operations';
    protected $primaryKey = 'id';

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

    // ID du type d'opération "RETRAIT" dans la table types_operations
    protected int $typeOperationRetrait = 2;

    // Cache local des id de la table statut (résolus par libellé, ex: REUSSI, ECHEC)
    protected array $statutCache = [];

    // ------------------------------------------------------------
    // 0. Résoudre l'id de la table statut à partir de son libellé
    // ------------------------------------------------------------
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

    // ------------------------------------------------------------
    // 1. Récupérer le barème de frais applicable à un montant donné
    // ------------------------------------------------------------
    public function getBaremeFrais(float $montant): ?array
    {
        $bareme = $this->db->table('baremes_frais')
            ->where('type_operation_id', $this->typeOperationRetrait)
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
            ->select('co.*')
            ->join('client cl', 'cl.id = co.client_id')
            ->where('cl.numero_telephone', $numero)
            ->get()
            ->getRowArray();

        return $compte ?: null;
    }

    // ------------------------------------------------------------
    // 4. Vérifier que le compte a un solde suffisant
    //    (montant retiré + frais, les frais sont toujours à la charge du client)
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
        return 'RET' . date('YmdHis') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }

    // ------------------------------------------------------------
    // 6. FONCTION PRINCIPALE : effectuer un retrait
    //    "automatique" : pas de destinataire, l'argent sort du système
    //    Retourne ['success' => bool, 'message' => string, 'reference' => string|null]
    // ------------------------------------------------------------
    public function effectuerRetrait(string $numeroClient, float $montant): array
    {
        if ($montant <= 0) {
            return ['success' => false, 'message' => 'Montant invalide.'];
        }

        $compte = $this->getCompteParNumero($numeroClient);

        if (!$compte) {
            return ['success' => false, 'message' => "Le numéro $numeroClient n'existe pas."];
        }

        $bareme = $this->getBaremeFrais($montant);
        if (!$bareme) {
            return ['success' => false, 'message' => 'Aucun barème de frais trouvé pour ce montant.'];
        }

        $frais = $this->calculerFrais($bareme, $montant);
        $montantTotal = $montant + $frais;

        if (!$this->soldeSuffisant($compte, $montantTotal)) {
            return ['success' => false, 'message' => 'Solde insuffisant (montant + frais).'];
        }

        $reference = $this->genererReference();

        $this->db->transStart();

        try {
            // Débit du compte (montant retiré + frais)
            $this->db->table('comptes')
                ->where('id', $compte['id'])
                ->set('solde', 'solde - ' . $montantTotal, false)
                ->update();

            // Enregistrement de l'opération : pas de compte_destination_id (retrait = sortie du système)
            $this->insert([
                'reference'              => $reference,
                'type_operation_id'      => $this->typeOperationRetrait,
                'compte_source_id'       => $compte['id'],
                'compte_destination_id'  => null,
                'montant'                => $montant,
                'frais'                  => $frais,
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
                'message'   => 'Retrait effectué avec succès.',
                'reference' => $reference,
                'frais'     => $frais,
            ];
        } catch (Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Erreur lors du retrait : ' . $e->getMessage()];
        }
    }

    // ------------------------------------------------------------
    // 7. Historique des retraits d'un client
    // ------------------------------------------------------------
    public function getHistoriqueRetraits(int $clientId, int $limite = 20): array
    {
        return $this->db->table('operations o')
            ->select('o.*, cs.client_id AS source_client, st.libelle AS statut_libelle')
            ->join('comptes cs', 'cs.id = o.compte_source_id', 'left')
            ->join('statut st', 'st.id = o.statut', 'left')
            ->where('o.type_operation_id', $this->typeOperationRetrait)
            ->where('cs.client_id', $clientId)
            ->orderBy('o.date_operation', 'DESC')
            ->limit($limite)
            ->get()
            ->getResultArray();
    }
}