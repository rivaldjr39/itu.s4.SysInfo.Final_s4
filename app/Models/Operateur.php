<?php

namespace App\Models;

use CodeIgniter\Model;

class Operateur extends Model
{
    protected $table      = 'operateurs';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nom',
        'notre_operateur',
        'date_creation',
    ];

    protected $useTimestamps = false;
}