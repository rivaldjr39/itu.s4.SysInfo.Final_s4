<?php

namespace App\Models;

use CodeIgniter\Model;

class Prefixe extends Model
{
    protected $table      = 'prefixes';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'prefixe',
        'id_operateur',
        'actif',
        'date_creation',
    ];

    protected $useTimestamps = false;
}