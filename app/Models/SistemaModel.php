<?php

namespace App\Models;

use CodeIgniter\Model;

class SistemaModel extends Model
{
    protected $table = 'Sistema';
    protected $primaryKey = 'id_sistema';

    protected $allowedFields = [
        'nombre_sistema',
        'baja'
    ];

    public function getSistemasActivos()
    {
        return $this->where('baja', 'N')->findAll();
    }
}