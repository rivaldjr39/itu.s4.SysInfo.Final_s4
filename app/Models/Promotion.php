<?php

namespace App\Models;

use CodeIgniter\Model;

class Promotion extends Model
{
    protected $table            = 'promotions_transferts';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'type_operation_id',
        'pourcentage_reduction',
        'date_debut',
        'date_fin',
        'montant_min',
        'montant_max',
        'actif',
    ];

    protected $useTimestamps = false;

    /**
     * Récupère une promotion applicable pour un montant donné
     * (type_operation = TRANSFERT, actif, dans la période, dans la tranche de montant)
     */
    public function getPromotionApplicable(float $montant): ?array
    {
        $now = date('Y-m-d H:i:s');

        $promo = $this->db->table('promotions_transferts')
            ->where('type_operation_id', 3) // TRANSFERT
            ->where('actif', 1)
            ->where('date_debut <=', $now)
            ->groupStart()
                ->where('date_fin IS NULL', null, false)
                ->orWhere('date_fin >', $now)
            ->groupEnd()
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->orderBy('pourcentage_reduction', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        return $promo ?: null;
    }
}