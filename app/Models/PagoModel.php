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

    public function getIngresosPorMes($meses = 6)
    {
        return $this->select('
                YEAR(Pago.fecha_pago) AS anio,
                MONTH(Pago.fecha_pago) AS mes,
                SUM(Pago.monto) AS total
            ')
            ->groupBy('YEAR(Pago.fecha_pago), MONTH(Pago.fecha_pago)')
            ->orderBy('anio', 'DESC')
            ->orderBy('mes', 'DESC')
            ->limit($meses)
            ->findAll();
    }

    public function getIngresosPorSistema()
    {
        return $this->select('
                Sistema.nombre_sistema,
                SUM(Pago.monto) AS total
            ')
            ->join('Suscripcion', 'Suscripcion.id_suscripcion = Pago.id_suscripcion')
            ->join('Inscripcion', 'Inscripcion.id_inscripcion = Suscripcion.id_inscripcion')
            ->join('Sistema', 'Sistema.id_sistema = Inscripcion.id_sistema')
            ->groupBy('Sistema.id_sistema, Sistema.nombre_sistema')
            ->orderBy('total', 'DESC')
            ->findAll();
    }

    public function getTotalGanado()
    {
        return $this->selectSum('monto', 'total')->first();
    }

    public function getLiquidacionProfesores($porcentaje = 0.30)
    {
        $resultado = $this->select('
                Persona.nombre,
                Persona.apellido,
                Usuario.nombre_usuario,
                COUNT(Pago.id_pago) AS cuotas,
                SUM(Pago.monto) AS cobrado
            ')
            ->join('Usuario', 'Usuario.id_usuario = Pago.cobrado_por')
            ->join('Persona', 'Persona.id_persona = Usuario.id_persona')
            ->where('Persona.id_rol', 2)
            ->where('Persona.baja', 'N')
            ->groupBy('Persona.id_persona, Persona.nombre, Persona.apellido, Usuario.nombre_usuario')
            ->orderBy('cobrado', 'DESC')
            ->findAll();

        foreach ($resultado as &$prof) {
            $cobrado = (float) $prof['cobrado'];
            $prof['comision'] = $cobrado * $porcentaje;
        }
        unset($prof);

        return $resultado;
    }
}
