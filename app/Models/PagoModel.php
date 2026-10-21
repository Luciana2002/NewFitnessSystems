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

    /**
     * Ingresos totales por sistema. Si se indica $mes (YYYY-MM) filtra por ese mes.
     */
    public function getIngresosPorSistema(?string $mes = null)
    {
        $builder = $this->select('
                Sistema.nombre_sistema,
                SUM(Pago.monto) AS total
            ')
            ->join('Suscripcion', 'Suscripcion.id_suscripcion = Pago.id_suscripcion')
            ->join('Inscripcion', 'Inscripcion.id_inscripcion = Suscripcion.id_inscripcion')
            ->join('Sistema', 'Sistema.id_sistema = Inscripcion.id_sistema')
            ->groupBy('Sistema.id_sistema, Sistema.nombre_sistema')
            ->orderBy('total', 'DESC');

        if ($mes !== null && $mes !== '') {
            $builder->where('CONVERT(VARCHAR(7), Pago.fecha_pago, 120)', $mes);
        }

        return $builder->findAll();
    }

    public function getTotalGanado()
    {
        return $this->selectSum('monto', 'total')->first();
    }

    /**
     * Pagos de un día con medio de pago, cobrador y cliente.
     *
     * @param string $fecha YYYY-MM-DD
     * @return array
     */
    public function getPagosPorFecha(string $fecha): array
    {
        $builder = $this->select('
                Pago.id_pago,
                Pago.fecha_pago,
                Pago.monto,
                Medio_pago.descripcion AS medio_pago,
                cob.nombre AS cobrador_nombre,
                cob.apellido AS cobrador_apellido,
                cob_u.nombre_usuario AS cobrador_usuario,
                cli.nombre AS cliente_nombre,
                cli.apellido AS cliente_apellido
            ')
            ->join('Medio_pago', 'Medio_pago.id_medio_pago = Pago.id_medio_pago', 'left')
            ->join('Usuario cob_u', 'cob_u.id_usuario = Pago.cobrado_por', 'left')
            ->join('Persona cob', 'cob.id_persona = cob_u.id_persona', 'left')
            ->join('Suscripcion', 'Suscripcion.id_suscripcion = Pago.id_suscripcion', 'left')
            ->join('Inscripcion', 'Inscripcion.id_inscripcion = Suscripcion.id_inscripcion', 'left')
            ->join('Persona cli', 'cli.id_persona = Inscripcion.id_persona', 'left')
            ->groupStart()
                ->where("CAST(Pago.fecha_pago AS DATE)", $fecha)
            ->groupEnd()
            ->orderBy('Pago.fecha_pago', 'ASC')
            ->orderBy('Pago.id_pago', 'ASC');

        return $builder->findAll();
    }

    /**
     * Total recaudado en un día.
     *
     * @param string $fecha YYYY-MM-DD
     * @return float
     */
    public function getTotalPorFecha(string $fecha): float
    {
        $row = $this->selectSum('Pago.monto', 'total')
            ->where("CAST(Pago.fecha_pago AS DATE)", $fecha)
            ->first();

        return (float) ($row['total'] ?? 0);
    }

    /**
     * Total recaudado en un mes (YYYY-MM).
     *
     * @param string $mes
     * @return float
     */
    public function getTotalMes(string $mes): float
    {
        $inicio = $mes . '-01';
        $fin    = date('Y-m-t', strtotime($inicio));

        $row = $this->selectSum('monto', 'total')
            ->where('fecha_pago >=', $inicio)
            ->where('fecha_pago <=', $fin)
            ->first();

        return (float) ($row['total'] ?? 0);
    }

    /**
     * Liquidación (sueldos) de los profesores.
     *
     * Cada profesor cobra un porcentaje de las cuotas que cobró. Si se pasa
     * $mes (YYYY-MM) sólo se consideran los pagos de ese mes.
     *
     * @param float       $porcentaje
     * @param string|null $mes
     * @return array
     */
    public function getLiquidacionProfesores($porcentaje = 0.30, ?string $mes = null)
    {
        $builder = $this->select('
                Persona.nombre,
                Persona.apellido,
                Usuario.nombre_usuario,
                COUNT(Pago.id_pago) AS cuotas,
                SUM(Pago.monto) AS cobrado
            ')
            ->join('Usuario', 'Usuario.id_usuario = Pago.cobrado_por')
            ->join('Persona', 'Persona.id_persona = Usuario.id_persona')
            ->where('Persona.id_rol', 2)
            ->where('Persona.baja', 'N');

        if ($mes !== null && $mes !== '') {
            $inicio = $mes . '-01';
            $fin    = date('Y-m-t', strtotime($inicio));

            $builder->where('Pago.fecha_pago >=', $inicio)
                ->where('Pago.fecha_pago <=', $fin);
        }

        $resultado = $builder
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
