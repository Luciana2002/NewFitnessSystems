<?php

namespace App\Models;

use CodeIgniter\Model;

class PlantillaMensajeModel extends Model
{
    protected $table = 'PlantillaMensaje';
    protected $primaryKey = 'id_plantilla';

    protected $allowedFields = [
        'titulo',
        'categoria',
        'mensaje',
        'baja'
    ];

    /**
     * Devuelve las plantillas activas ordenadas por categoría y título.
     */
    public function getPlantillas(): array
    {
        return $this->where('baja', 0)
            ->orderBy('categoria', 'ASC')
            ->orderBy('titulo', 'ASC')
            ->findAll();
    }
}
