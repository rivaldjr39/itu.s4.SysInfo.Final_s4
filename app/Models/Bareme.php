<?php

namespace App\Models;

use CodeIgniter\Model;

class Bareme extends Model
{
    protected $table            = 'baremes_frais';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'type_operation_id',
        'montant_min',
        'montant_max',
        'frais_fixe',
        'frais_pourcentage',
        'date_debut',
        'date_fin',
    ];

    protected $useTimestamps = false;
}