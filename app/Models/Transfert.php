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
    protected int $typeOperationTransfert = 3;

    
    public function getBaremeFrais(float $montant): ?array
    {
        $bareme = $this->db->table('baremes_frais')
            ->where('type_operation_id', $this->typeOperationTransfert)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->groupStart()
                ->where('date_fin IS NULL')
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

    public function getCompteParNumero(string $numero): ?array
    {
        $compte = $this->db->table('comptes co')
            ->select('co.*')
            ->join('clients cl', 'cl.id = co.client_id')
            ->where('cl.numero_telephone', $numero)
            ->get()
            ->getRowArray();

        return $compte ?: null;
    }


    public function soldeSuffisant(array $compte, float $montantTotal): bool
    {
        return (float) $compte['solde'] >= $montantTotal;
    }


    public function genererReference(): string
    {
        return 'TRF' . date('YmdHis') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }

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
        $frais = $this->calculerFrais($bareme, $montant);
        $montantTotal = $montant + $frais; // débité chez l'émetteur

        if (!$this->soldeSuffisant($compteSource, $montantTotal)) {
            return ['success' => false, 'message' => 'Solde insuffisant (montant + frais).'];
        }

        $reference = $this->genererReference();
        $this->db->transStart();

        try {
            // Débit du compte source
            $this->db->table('comptes')
                ->where('id', $compteSource['id'])
                ->set('solde', 'solde - ' . $montantTotal, false)
                ->update();

            // Crédit du compte destination (montant net, sans les frais)
            $this->db->table('comptes')
                ->where('id', $compteDestination['id'])
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


    public function getHistoriqueTransferts(int $clientId, int $limite = 20): array
    {
        return $this->db->table('operations o')
            ->select('o.*, cs.client_id AS source_client, cd.client_id AS dest_client')
            ->join('comptes cs', 'cs.id = o.compte_source_id', 'left')
            ->join('comptes cd', 'cd.id = o.compte_destination_id', 'left')
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
}