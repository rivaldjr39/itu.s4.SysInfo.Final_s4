<?php
namespace App\Models;
use CodeIgniter\Model;
use Exception;
class Depot extends Model
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

    protected int $typeOperationDepot = 1;
    protected array $statutCache = [];

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

    public function getBaremeFrais(float $montant): ?array
    {
        $bareme = $this->db->table('baremes_frais')
            ->where('type_operation_id', $this->typeOperationDepot)
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

    public function calculerFrais(array $bareme, float $montant): float
    {
        $fraisFixe = (float) $bareme['frais_fixe'];
        $fraisPourcentage = (float) $bareme['frais_pourcentage'];

        return round($fraisFixe + ($montant * $fraisPourcentage / 100), 2);
    }

    public function getCompteParClientId(int $clientId): ?array
    {
        $compte = $this->db->table('comptes')
            ->select('COALESCE(id, rowid) AS id, client_id, solde, date_creation')
            ->where('client_id', $clientId)
            ->limit(1)
            ->get()
            ->getRowArray();

        return $compte ?: null;
    }

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

    public function genererReference(): string
    {
        return 'DPT' . date('YmdHis') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }

    public function effectuerDepot(int $clientId, float $montant): array
    {
        if ($montant <= 0) {
            return ['success' => false, 'message' => 'Montant invalide.'];
        }

        $compte = $this->getCompteParClientId($clientId);

        if (!$compte) {
            return ['success' => false, 'message' => 'Aucun compte n\'a été trouvé pour ce client.'];
        }

        $bareme = $this->getBaremeFrais($montant);

        if (!$bareme) {
            return ['success' => false, 'message' => 'Aucun barème de frais trouvé pour ce montant.'];
        }

        $frais = $this->calculerFrais($bareme, $montant);
        $montantTotal = $montant - $frais;
        $reference = $this->genererReference();

        $this->db->transStart();

        try {
            $this->db->table('comptes')
                ->where('id', $compte['id'])
                ->set('solde', 'solde + ' . $montant, false)
                ->update();

            $this->insert([
                'reference'             => $reference,
                'type_operation_id'     => $this->typeOperationDepot,
                'compte_source_id'      => null,
                'compte_destination_id' => $compte['id'],
                'montant'               => $montant,
                'frais'                 => $frais,
                'montant_total'         => $montantTotal,
                'bareme_frais_id'       => $bareme['id'],
                'statut'                => $this->getStatutId('REUSSI'),
                'date_operation'        => date('Y-m-d H:i:s'),
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new Exception('Échec de la transaction SQL.');
            }

            return [
                'success'   => true,
                'message'   => 'Dépôt effectué avec succès.',
                'reference' => $reference,
                'frais'     => $frais,
            ];
        } catch (Exception $e) {
            $this->db->transRollback();

            return ['success' => false, 'message' => 'Erreur lors du dépôt : ' . $e->getMessage()];
        }
    }
}