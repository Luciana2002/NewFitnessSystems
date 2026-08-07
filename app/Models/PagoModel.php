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
                Medio_pago.descripcion AS medio_pago,
                Mensualidad.precio,
                Sistema.nombre_sistema,
                Persona.nombre,
                Persona.apellido,
                Persona.dni,
                Usuario.nombre_usuario AS cobrado_por
            ')
            ->join('Medio_pago', 'Medio_pago.id_medio_pago = Pago.id_medio_pago')
            ->join('Mensualidad', 'Mensualidad.id_mensualidad = Pago.id_mensualidad')
            ->join('Suscripcion', 'Suscripcion.id_suscripcion = Pago.id_suscripcion')
            ->join('Inscripcion', 'Inscripcion.id_inscripcion = Suscripcion.id_inscripcion')
            ->join('Sistema', 'Sistema.id_sistema = Inscripcion.id_sistema')
            ->join('Persona', 'Persona.id_persona = Inscripcion.id_persona')
            ->join('Usuario', 'Usuario.id_usuario = Pago.cobrado_por')
            ->orderBy('Pago.fecha_pago', 'DESC')
            ->findAll();
    }

    public function getPagosCliente($idPersona)
    {
        return $this->select('
                Pago.id_pago,
                Pago.fecha_pago,
                Pago.monto,
                Medio_pago.descripcion AS medio_pago,
                Mensualidad.precio,
                Sistema.nombre_sistema,
                Suscripcion.fecha_inicio,
                Suscripcion.fecha_vencimiento,
                Estado_suscripcion.descripcion AS estado_suscripcion,
                Usuario.nombre_usuario AS cobrado_por
            ')
            ->join('Medio_pago', 'Medio_pago.id_medio_pago = Pago.id_medio_pago')
            ->join('Mensualidad', 'Mensualidad.id_mensualidad = Pago.id_mensualidad')
            ->join('Suscripcion', 'Suscripcion.id_suscripcion = Pago.id_suscripcion')
            ->join('Inscripcion', 'Inscripcion.id_inscripcion = Suscripcion.id_inscripcion')
            ->join('Sistema', 'Sistema.id_sistema = Inscripcion.id_sistema')
            ->join('Estado_suscripcion', 'Estado_suscripcion.id_estado = Suscripcion.id_estado')
            ->join('Usuario', 'Usuario.id_usuario = Pago.cobrado_por')
            ->where('Inscripcion.id_persona', $idPersona)
            ->orderBy('Pago.fecha_pago', 'DESC')
            ->findAll();
    }

    public function getMediosPago()
    {
        return $this->db->table('Medio_pago')
            ->orderBy('descripcion', 'ASC')
            ->get()
            ->getResultArray();
    }
}
