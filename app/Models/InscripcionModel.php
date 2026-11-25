<?php

namespace App\Models;

use CodeIgniter\Model;

class InscripcionModel extends Model
{
    protected $table         = 'Inscripcion';
    protected $primaryKey    = 'id_inscripcion';
    protected $allowedFields = [
        'fecha_inscripcion',
        'id_persona',
        'id_sistema',
        'id_horario',
    ];

    /**
     * Cantidad de inscripciones registradas en un mes (YYYY-MM).
     *
     * @param string $mes
     * @return int
     */
    public function getCantidadMes(string $mes): int
    {
        if (! preg_match('/^\d{4}-\d{2}$/', $mes)) {
            return 0;
        }

        $inicio = $mes . '-01';
        $fin    = date('Y-m-t', strtotime($inicio));

        return $this->where('fecha_inscripcion >=', $inicio)
                    ->where('fecha_inscripcion <=', $fin)
                    ->countAllResults();
    }
}
