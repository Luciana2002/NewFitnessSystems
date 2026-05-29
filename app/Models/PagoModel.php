<?php

namespace App\Models;

use CodeIgniter\Model;

class PagoModel extends Model
{
    protected $table = 'Pago';
    protected $primaryKey = 'id_pago';

    protected $allowedFields = [
        'fecha_pago',
        'monto',
        'id_medio_pago',
        'id_mensualidad',
        'cobrado_por',
        'id_suscripcion'
    ];

    public function getPagosAll()
    {
        return $this->select('
                Pago.id_pago,
                Pago.fecha_pago,
                Pago.monto,
                Pago.id_medio_pago,
                Pago.id_mensualidad,
                Pago.cobrado_por,
                Pago.id_suscripcion,

                Medio_pago.descripcion AS medio_pago,
                Mensualidad.precio AS precio_mensualidad,

                Persona.id_persona,
                Persona.nombre,
                Persona.apellido,
                Persona.dni,
                Persona.email,

                Sistema.id_sistema,
                Sistema.nombre_sistema,

                Usuario.nombre_usuario AS cobrado_por_usuario
            ')
            ->join('Medio_pago', 'Medio_pago.id_medio_pago = Pago.id_medio_pago')
            ->join('Mensualidad', 'Mensualidad.id_mensualidad = Pago.id_mensualidad')
            ->join('Usuario', 'Usuario.id_usuario = Pago.cobrado_por')
            ->join('Suscripcion', 'Suscripcion.id_suscripcion = Pago.id_suscripcion')
            ->join('Inscripcion', 'Inscripcion.id_inscripcion = Suscripcion.id_inscripcion')
            ->join('Persona', 'Persona.id_persona = Inscripcion.id_persona')
            ->join('Sistema', 'Sistema.id_sistema = Inscripcion.id_sistema')
            ->findAll();
    }

    public function getPagosCliente($idPersona)
    {
        return $this->select('
                Pago.id_pago,
                Pago.fecha_pago,
                Pago.monto,

                Medio_pago.descripcion AS medio_pago,
                Mensualidad.precio AS precio_mensualidad,

                Sistema.nombre_sistema
            ')
            ->join('Medio_pago', 'Medio_pago.id_medio_pago = Pago.id_medio_pago')
            ->join('Mensualidad', 'Mensualidad.id_mensualidad = Pago.id_mensualidad')
            ->join('Suscripcion', 'Suscripcion.id_suscripcion = Pago.id_suscripcion')
            ->join('Inscripcion', 'Inscripcion.id_inscripcion = Suscripcion.id_inscripcion')
            ->join('Sistema', 'Sistema.id_sistema = Inscripcion.id_sistema')
            ->where('Inscripcion.id_persona', $idPersona)
            ->findAll();
    }

    public function getPagoCompleto($idPago)
    {
        return $this->select('
                Pago.id_pago,
                Pago.fecha_pago,
                Pago.monto,
                Pago.id_medio_pago,
                Pago.id_mensualidad,
                Pago.cobrado_por,
                Pago.id_suscripcion,

                Medio_pago.descripcion AS medio_pago,
                Mensualidad.precio AS precio_mensualidad,

                Persona.id_persona,
                Persona.nombre,
                Persona.apellido,
                Persona.dni,
                Persona.email,

                Sistema.id_sistema,
                Sistema.nombre_sistema,

                Usuario.nombre_usuario AS cobrado_por_usuario
            ')
            ->join('Medio_pago', 'Medio_pago.id_medio_pago = Pago.id_medio_pago')
            ->join('Mensualidad', 'Mensualidad.id_mensualidad = Pago.id_mensualidad')
            ->join('Usuario', 'Usuario.id_usuario = Pago.cobrado_por')
            ->join('Suscripcion', 'Suscripcion.id_suscripcion = Pago.id_suscripcion')
            ->join('Inscripcion', 'Inscripcion.id_inscripcion = Suscripcion.id_inscripcion')
            ->join('Persona', 'Persona.id_persona = Inscripcion.id_persona')
            ->join('Sistema', 'Sistema.id_sistema = Inscripcion.id_sistema')
            ->where('Pago.id_pago', $idPago)
            ->first();
    }
}