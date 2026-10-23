<?php

namespace App\Models;

use CodeIgniter\Model;

class SeguimientoFisicoModel extends Model
{
    protected $table = 'SeguimientoFisico';
    protected $primaryKey = 'id_seguimiento';

    protected $allowedFields = [
        'id_persona',
        'fecha',
        'peso',
        'grasa_corporal',
        'cintura',
        'pecho',
        'brazo',
        'muslo',
        'cadera',
        'notas'
    ];

    public function getHistorial($idPersona)
    {
        return $this->where('id_persona', $idPersona)
            ->orderBy('fecha', 'ASC')
            ->findAll();
    }

    public function getUltimo($idPersona)
    {
        return $this->where('id_persona', $idPersona)
            ->orderBy('fecha', 'DESC')
            ->first();
    }
}
