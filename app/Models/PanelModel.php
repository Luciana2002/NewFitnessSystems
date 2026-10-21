<?php

namespace App\Models;

use CodeIgniter\Model;

class PanelModel extends Model
{
    protected $table = 'Persona';
    protected $primaryKey = 'id_persona';
    protected $allowedFields = [];

    /* ==========================================================
       ADMINISTRADOR
    ========================================================== */

    public function resumenAdmin(): array
    {
        $db = \Config\Database::connect();

        $hoy         = date('Y-m-d');
        $enSieteDias = date('Y-m-d', strtotime('+7 days'));
        $mes         = date('Y-m');

        $clientesActivos = (int) $db->query(
            "SELECT COUNT(*) AS total FROM Persona WHERE id_rol = 3 AND baja = 'N'"
        )->getRow()->total;

        $porVencer = (int) $db->query(
            "SELECT COUNT(*) AS total
               FROM Suscripcion su
               INNER JOIN Inscripcion i ON i.id_inscripcion = su.id_inscripcion
               INNER JOIN Persona p ON p.id_persona = i.id_persona
              WHERE su.id_estado <> 3
                AND p.baja = 'N'
                AND su.fecha_vencimiento BETWEEN ? AND ?",
            [$hoy, $enSieteDias]
        )->getRow()->total;

        $vencidas = (int) $db->query(
            "SELECT COUNT(*) AS total
               FROM Suscripcion su
               INNER JOIN Inscripcion i ON i.id_inscripcion = su.id_inscripcion
               INNER JOIN Persona p ON p.id_persona = i.id_persona
              WHERE su.id_estado <> 3
                AND p.baja = 'N'
                AND su.fecha_vencimiento < ?",
            [$hoy]
        )->getRow()->total;

        $pagoModel  = new PagoModel();
        $gastoModel = new GastoModel();

        $porcentaje = (int) (new ConfiguracionModel())->getValor('comision_profesor', 30);
        $porcentaje = max(0, min(100, $porcentaje));

        $ingresosMes = (float) $pagoModel->getTotalMes($mes);
        $liquidacion = $pagoModel->getLiquidacionProfesores($porcentaje / 100, $mes);

        $sueldos = 0.0;
        foreach ($liquidacion as $profesor) {
            $sueldos += (float) $profesor['comision'];
        }

        $otrosGastos = (float) $gastoModel->getTotalMes($mes, ['Sueldos']);

        $proximos = $db->query(
            "SELECT TOP 6
                    p.nombre,
                    p.apellido,
                    s.nombre_sistema,
                    su.fecha_vencimiento
               FROM Suscripcion su
               INNER JOIN Inscripcion i ON i.id_inscripcion = su.id_inscripcion
               INNER JOIN Persona p ON p.id_persona = i.id_persona
               INNER JOIN Sistema s ON s.id_sistema = i.id_sistema
              WHERE su.id_estado <> 3
                AND p.baja = 'N'
                AND su.fecha_vencimiento >= ?
              ORDER BY su.fecha_vencimiento ASC, p.apellido ASC",
            [$hoy]
        )->getResultArray();

        foreach ($proximos as &$fila) {
            $fecha          = $this->normalizarFecha($fila['fecha_vencimiento']);
            $fila['_fecha'] = $fecha;
            $fila['dias']   = (int) (new \DateTime($hoy))->diff(new \DateTime($fecha))->format('%r%a');
        }
        unset($fila);

        $actividad = $db->query(
            "SELECT TOP 6 fecha, nombre_usuario, accion, entidad, detalle
               FROM Bitacora
              ORDER BY fecha DESC, id_log DESC"
        )->getResultArray();

        foreach ($actividad as &$registro) {
            $registro['_fecha'] = $this->normalizarFechaHora($registro['fecha']);
        }
        unset($registro);

        return [
            'clientes_activos' => $clientesActivos,
            'por_vencer'       => $porVencer,
            'vencidas'         => $vencidas,
            'ingresos_mes'     => $ingresosMes,
            'sueldos'          => $sueldos,
            'otros_gastos'     => $otrosGastos,
            'gastos_mes'       => $sueldos + $otrosGastos,
            'balance_mes'      => $ingresosMes - $sueldos - $otrosGastos,
            'pagos_hoy'        => (float) $pagoModel->getTotalPorFecha($hoy),
            'pagos_hoy_count'  => count($pagoModel->getPagosPorFecha($hoy)),
            'proximos'         => $proximos,
            'actividad'        => $actividad,
            'mes'              => $mes,
        ];
    }

    /* ==========================================================
       PROFESOR
    ========================================================== */

    public function resumenProfesor(int $idProfesor): array
    {
        $db  = \Config\Database::connect();
        $hoy = date('Y-m-d');

        $clientes = (int) $db->query(
            "SELECT COUNT(DISTINCT id_cliente) AS total
               FROM Rutina
              WHERE id_profesor = ?
                AND baja = 'N'
                AND id_cliente IS NOT NULL",
            [$idProfesor]
        )->getRow()->total;

        $rutinas = (int) $db->query(
            "SELECT COUNT(*) AS total FROM Rutina WHERE id_profesor = ? AND baja = 'N'",
            [$idProfesor]
        )->getRow()->total;

        $asistenciasHoy = (int) $db->query(
            "SELECT COUNT(*) AS total FROM Asistencia WHERE fecha = ?",
            [$hoy]
        )->getRow()->total;

        $rutinasRecientes = array_slice(
            (new RutinaModel())->getRutinasProfesor($idProfesor),
            0,
            5
        );

        return [
            'clientes'        => $clientes,
            'rutinas'         => $rutinas,
            'asistencias_hoy' => $asistenciasHoy,
            'rutinas_recientes' => $rutinasRecientes,
        ];
    }

    /* ==========================================================
       CLIENTE
    ========================================================== */

    public function resumenCliente(int $idPersona): array
    {
        helper('membresia');

        $clienteModel = new ClienteModel();
        $pagoModel    = new PagoModel();
        $rutinaModel  = new RutinaModel();

        $membresia     = nfs_membresia_cliente($idPersona);
        $suscripciones = $clienteModel->getSuscripcionesCliente($idPersona);
        $sistemas      = $this->ultimasPorSistema($suscripciones);

        $pagos = $pagoModel->getPagosCliente($idPersona);

        $totalPagado = 0.0;
        foreach ($pagos as $pago) {
            $totalPagado += (float) $pago['monto'];
        }

        $rutinas    = $rutinaModel->getRutinasCliente($idPersona);
        $ultimoPago = $pagos[0] ?? null;

        return [
            'membresia'      => $membresia,
            'sistemas'       => $sistemas,
            'pagos_count'    => count($pagos),
            'total_pagado'   => $totalPagado,
            'ultimo_pago'    => $ultimoPago,
            'rutinas_count'  => count($rutinas),
        ];
    }

    /* ==========================================================
       UTILIDADES
    ========================================================== */

    private function ultimasPorSistema(array $filas): array
    {
        $porSistema = [];

        foreach ($filas as $fila) {
            $idSistema = (int) ($fila['id_sistema'] ?? 0);
            $fecha     = $this->normalizarFecha($fila['fecha_vencimiento'] ?? null);

            if (!isset($porSistema[$idSistema]) || $fecha > $porSistema[$idSistema]['_fecha']) {
                $fila['_fecha']       = $fecha;
                $porSistema[$idSistema] = $fila;
            }
        }

        return array_values($porSistema);
    }

    private function normalizarFecha($fecha): string
    {
        if ($fecha instanceof \DateTimeInterface) {
            return $fecha->format('Y-m-d');
        }

        $texto = (string) $fecha;

        return strlen($texto) >= 10 ? substr($texto, 0, 10) : $texto;
    }

    private function normalizarFechaHora($fecha): string
    {
        if ($fecha instanceof \DateTimeInterface) {
            return $fecha->format('Y-m-d H:i');
        }

        $timestamp = strtotime((string) $fecha);

        return $timestamp ? date('Y-m-d H:i', $timestamp) : (string) $fecha;
    }

    private function normalizarHora($hora): string
    {
        if ($hora instanceof \DateTimeInterface) {
            return $hora->format('H:i');
        }

        return substr((string) $hora, 0, 5);
    }
}
