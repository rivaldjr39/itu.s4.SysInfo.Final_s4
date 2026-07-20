<?php

namespace App\Models;

use CodeIgniter\Model;

class Client extends Model
{
    protected $table      = 'client';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'numero_telephone',
        'nom',
        'role',
        'prefixe_id',
        'date_premiere_connexion',
    ];

    protected $useTimestamps = false;

    public function findByNumeroTelephone(string $numeroTelephone): ?array
    {
        foreach (['client', 'clients'] as $table) {
            try {
                $client = $this->db->table($table)
                    ->where('numero_telephone', $numeroTelephone)
                    ->get()
                    ->getRowArray();

                if ($client !== null) {
                    return $client;
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }
}
