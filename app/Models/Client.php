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

    protected function normaliserNumeroTelephone(string $numeroTelephone): string
    {
        return preg_replace('/\D+/', '', $numeroTelephone) ?? '';
    }

    public function findByNumeroTelephone(string $numeroTelephone): ?array
    {
        $numeroTelephone = $this->normaliserNumeroTelephone($numeroTelephone);

        if ($numeroTelephone === '') {
            return null;
        }

        foreach (['client', 'clients'] as $table) {
            try {
                $client = $this->db->table($table)
                    ->select('COALESCE(id, rowid) AS id, numero_telephone, nom, role, prefixe_id, date_premiere_connexion')
                    ->where('numero_telephone', $numeroTelephone)
                    ->limit(1)
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
