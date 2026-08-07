<?php

namespace App\Models;

use CodeIgniter\Model;

class HorarioModel extends Model
{
    protected $table = 'Horario';
    protected $primaryKey = 'id_horario';

    protected $allowedFields = [
        'hora_inicio',
        'hora_fin',
        'dia_semana',
        'id_sistema',
        'baja'
    ];

    public function getHorariosAll()
    {
        return $this->select('
                Horario.id_horario,
                Horario.id_sistema,
                Horario.hora_inicio,
                Horario.hora_fin,
                Horario.dia_semana,
                Horario.id_sistema,
                Horario.baja,
                Sistema.nombre_sistema
            ')
            ->join('Sistema', 'Sistema.id_sistema = Horario.id_sistema')
            ->findAll();
    }

    public function getHorariosActivos()
    {
        return $this->select('
                Horario.id_horario,
                Horario.hora_inicio,
                Horario.hora_fin,
                Horario.dia_semana,
                Horario.id_sistema,
                Horario.baja,
                Sistema.nombre_sistema
            ')
            ->join('Sistema', 'Sistema.id_sistema = Horario.id_sistema')
            ->where('Horario.baja', 'N')
            ->findAll();
    }
}