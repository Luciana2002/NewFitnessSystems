<?php

namespace App\Models;

use CodeIgniter\Model;

class ReservaModel extends Model
{
    protected $table = 'Reserva';
    protected $primaryKey = 'id_reserva';

    protected $allowedFields = [
        'id_horario',
        'id_persona',
        'fecha',
        'estado',
        'id_usuario_inscripcion',
        'observacion'
    ];

    public function getReservasCliente($idPersona)
    {
        return $this->select('
                Reserva.id_reserva,
                Reserva.fecha,
                Reserva.estado,
                Reserva.fecha_creacion,
                Horario.id_horario,
                Horario.hora_inicio,
                Horario.hora_fin,
                Horario.dia_semana,
                Sistema.nombre_sistema,
                Sistema.color
            ')
            ->join('Horario', 'Horario.id_horario = Reserva.id_horario')
            ->join('Sistema', 'Sistema.id_sistema = Horario.id_sistema')
            ->where('Reserva.id_persona', $idPersona)
            ->orderBy('Reserva.fecha', 'DESC')
            ->orderBy('Horario.hora_inicio', 'ASC')
            ->findAll();
    }

    public function getReserva($idHorario, $idPersona, $fecha)
    {
        return $this->where('id_horario', $idHorario)
            ->where('id_persona', $idPersona)
            ->where('fecha', $fecha)
            ->whereIn('estado', ['RESERVADA', 'LISTA_ESPERA'])
            ->first();
    }

    public function contarReservados($idHorario, $fecha)
    {
        return $this->where('id_horario', $idHorario)
            ->where('fecha', $fecha)
            ->where('estado', 'RESERVADA')
            ->countAllResults();
    }

    public function getListaEspera($idHorario, $fecha)
    {
        return $this->where('id_horario', $idHorario)
            ->where('fecha', $fecha)
            ->where('estado', 'LISTA_ESPERA')
            ->orderBy('fecha_creacion', 'ASC')
            ->findAll();
    }

    public function getReservasHorario($idHorario, $fecha)
    {
        return $this->select('
                Reserva.id_reserva,
                Reserva.id_persona,
                Reserva.estado,
                Reserva.fecha_creacion,
                Persona.nombre,
                Persona.apellido,
                Persona.dni
            ')
            ->join('Persona', 'Persona.id_persona = Reserva.id_persona')
            ->where('Reserva.id_horario', $idHorario)
            ->where('Reserva.fecha', $fecha)
            ->whereIn('Reserva.estado', ['RESERVADA', 'LISTA_ESPERA'])
            ->orderBy('Reserva.estado', 'ASC')
            ->orderBy('Reserva.fecha_creacion', 'ASC')
            ->findAll();
    }
}
