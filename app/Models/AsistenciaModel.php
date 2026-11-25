<?php

namespace App\Models;

use CodeIgniter\Model;

class AsistenciaModel extends Model
{
    protected $table = 'Asistencia';
    protected $primaryKey = 'id_asistencia';

    protected $allowedFields = [
        'id_persona',
        'fecha',
        'hora',
        'resultado',
        'id_usuario',
        'observacion'
    ];

    public function getAsistenciasCliente($idPersona)
    {
        return $this->select('
                Asistencia.id_asistencia,
                Asistencia.fecha,
                Asistencia.hora,
                Asistencia.resultado,
                Asistencia.observacion,
                Usuario.nombre_usuario AS registrado_por
            ')
            ->join('Usuario', 'Usuario.id_usuario = Asistencia.id_usuario', 'left')
            ->where('Asistencia.id_persona', $idPersona)
            ->where('Asistencia.resultado', 'OK')
            ->orderBy('Asistencia.fecha', 'DESC')
            ->orderBy('Asistencia.hora', 'DESC')
            ->findAll();
    }

    /**
     * Historial de asistencias de una persona, con resumen del mes actual.
     *
     * @return array{
     *     registros: array<int, array<string, mixed>>,
     *     resumen: array{cantidad_mes: int, ultima: ?string}
     * }
     */
    public function getAsistenciasPorPersona(int $idPersona, ?string $periodo = null): array
    {
        $consulta = $this->db->table('Asistencia')
            ->select('id_asistencia, fecha, hora, resultado, observacion')
            ->where('id_persona', $idPersona);

        if ($periodo !== null && preg_match('/^\d{4}-\d{2}$/', $periodo)) {
            $desde = $periodo . '-01';
            $hasta = date('Y-m-01', strtotime($desde . ' +1 month'));

            $consulta->where('fecha >=', $desde)->where('fecha <', $hasta);
        }

        $filas = $consulta->orderBy('fecha', 'DESC')
            ->orderBy('hora', 'DESC')
            ->get()
            ->getResultArray();

        $registros = [];

        foreach ($filas as $fila) {
            $registros[] = [
                'id_asistencia' => (int) $fila['id_asistencia'],
                'fecha'         => $this->fechaNormalizada($fila['fecha']),
                'hora'          => $this->horaNormalizada($fila['hora']),
                'resultado'     => (string) $fila['resultado'],
                'observacion'   => (string) ($fila['observacion'] ?? ''),
            ];
        }

        $inicioMes = date('Y-m-01');
        $finMes    = date('Y-m-01', strtotime($inicioMes . ' +1 month'));

        $totalMes = $this->db->table('Asistencia')
            ->select('COUNT(*) AS total')
            ->where('id_persona', $idPersona)
            ->where('resultado', 'OK')
            ->where('fecha >=', $inicioMes)
            ->where('fecha <', $finMes)
            ->get()
            ->getRowArray();

        $ultimaFila = $this->db->table('Asistencia')
            ->select('fecha, hora')
            ->where('id_persona', $idPersona)
            ->where('resultado', 'OK')
            ->orderBy('fecha', 'DESC')
            ->orderBy('hora', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $ultima = null;

        if ($ultimaFila) {
            $fecha = $this->fechaNormalizada($ultimaFila['fecha']);

            if ($fecha !== null) {
                $ultima = $fecha . ' ' . $this->horaNormalizada($ultimaFila['hora']);
            }
        }

        return [
            'registros' => $registros,
            'resumen'   => [
                'cantidad_mes' => (int) ($totalMes['total'] ?? 0),
                'ultima'       => $ultima,
            ],
        ];
    }

    private function horaNormalizada($valor): string
    {
        if ($valor instanceof \DateTimeInterface) {
            return $valor->format('H:i:s');
        }

        return substr((string) $valor, 0, 8);
    }

    public function getAsistenciasDelDia($fecha)
    {
        return $this->select('
                Asistencia.id_asistencia,
                Asistencia.fecha,
                Asistencia.hora,
                Asistencia.resultado,
                Persona.nombre,
                Persona.apellido,
                Persona.dni
            ')
            ->join('Persona', 'Persona.id_persona = Asistencia.id_persona', 'left')
            ->where('Asistencia.fecha', $fecha)
            ->orderBy('Asistencia.hora', 'DESC')
            ->findAll();
    }

    public function yaRegistroHoy($idPersona, $fecha)
    {
        return $this->where('id_persona', $idPersona)
            ->where('fecha', $fecha)
            ->where('resultado', 'OK')
            ->first();
    }

    /**
     * Devuelve la última asistencia (resultado OK) de cada persona,
     * indexada por id_persona. El valor es la fecha en formato Y-m-d.
     *
     * @return array<int, string>
     */
    public function getUltimasAsistencias(): array
    {
        $rows = $this->db->table('Asistencia')
            ->select('id_persona, MAX(fecha) AS ultima')
            ->where('resultado', 'OK')
            ->groupBy('id_persona')
            ->get()
            ->getResultArray();

        $mapa = [];

        foreach ($rows as $row) {
            $fecha = $row['ultima'];

            if ($fecha instanceof \DateTimeInterface) {
                $fecha = $fecha->format('Y-m-d');
            }

            $mapa[(int) $row['id_persona']] = (string) $fecha;
        }

        return $mapa;
    }

    /* ==================================================================
       REGISTRO DE ASISTENCIA (modalidad: el socio escanea el QR del gym)
    ================================================================== */

    /**
     * Valida la membresía y registra el ingreso de una persona.
     *
     * @return array{
     *     estado: string,
     *     mensaje: string,
     *     nombre: string,
     *     dni: string,
     *     fecha: string,
     *     hora: string,
     *     disciplinas: array<int, string>,
     *     vencimiento: ?string,
     *     dias: ?int
     * }
     */
    public function registrarIngreso(int $idPersona, ?int $idUsuario = null): array
    {
        $hoy  = date('Y-m-d');
        $hora = date('H:i:s');

        $persona = $this->db->query(
            'SELECT id_persona, nombre, apellido, dni FROM Persona WHERE id_persona = ?',
            [$idPersona]
        )->getRowArray();

        $base = [
            'estado'      => 'error',
            'mensaje'     => 'No se pudo identificar al socio.',
            'nombre'      => '',
            'dni'         => '',
            'fecha'       => $hoy,
            'hora'        => $hora,
            'disciplinas' => [],
            'vencimiento' => null,
            'dias'        => null,
        ];

        if (! $persona) {
            return $base;
        }

        $base['nombre'] = trim((string) $persona['nombre'] . ' ' . (string) $persona['apellido']);
        $base['dni']    = (string) $persona['dni'];

        $vigentes      = $this->suscripcionesVigentes($idPersona);
        $disciplinas   = array_column($vigentes, 'disciplina');
        $vencimiento   = $this->maximaFechaVencimiento($vigentes);
        $base['disciplinas'] = $disciplinas;
        $base['vencimiento'] = $vencimiento;
        $base['dias']        = $vencimiento ? $this->diasHasta($vencimiento) : null;

        /* Duplicado: ingreso OK de hoy dentro de las últimas 3 horas */
        $duplicado = $this->db->query(<<<'SQL'
            SELECT TOP 1 a.fecha, a.hora, a.observacion
              FROM Asistencia a
             WHERE a.id_persona = ?
               AND a.resultado = 'OK'
               AND a.fecha = ?
               AND a.fecha_registro >= DATEADD(HOUR, -3, GETDATE())
             ORDER BY a.fecha_registro DESC
            SQL, [$idPersona, $hoy])->getRowArray();

        if ($duplicado) {
            $base['estado']  = 'duplicado';
            $base['hora']    = (string) $duplicado['hora'];
            $base['mensaje'] = 'Ya se registró un ingreso en las últimas 3 horas. No se agregó uno nuevo.';

            return $base;
        }

        if ($vencimiento === null) {
            $ultimoVencimiento = $this->ultimoVencimiento($idPersona);
            $this->insert([
                'id_persona'  => $idPersona,
                'fecha'       => $hoy,
                'hora'        => $hora,
                'resultado'   => 'VENCIDA',
                'id_usuario'  => $idUsuario,
                'observacion' => mb_substr('Cuota vencida' . ($ultimoVencimiento ? ' al ' . date('d/m/Y', strtotime($ultimoVencimiento)) : ''), 0, 200),
            ]);

            $base['estado']      = 'vencida';
            $base['vencimiento'] = $ultimoVencimiento;
            $base['dias']        = $ultimoVencimiento ? $this->diasHasta($ultimoVencimiento) : null;
            $base['mensaje']     = $ultimoVencimiento
                ? 'Tu cuota está vencida (venció el ' . date('d/m/Y', strtotime($ultimoVencimiento)) . ').'
                : 'No tenés una suscripción activa.';

            return $base;
        }

        $detalle = 'Disciplinas: ' . implode(', ', $disciplinas) . ' | Vence: ' . date('d/m/Y', strtotime($vencimiento));

        $this->insert([
            'id_persona'  => $idPersona,
            'fecha'       => $hoy,
            'hora'        => $hora,
            'resultado'   => 'OK',
            'id_usuario'  => $idUsuario,
            'observacion' => mb_substr($detalle, 0, 200),
        ]);

        $base['estado']  = 'ok';
        $base['mensaje'] = 'Ingreso registrado correctamente.';

        return $base;
    }

    /**
     * Suscripciones vigentes (no canceladas y con vencimiento >= hoy).
     *
     * @return array<int, array{disciplina: string, vencimiento: string}>
     */
    private function suscripcionesVigentes(int $idPersona): array
    {
        $rows = $this->db->query(<<<'SQL'
            SELECT s.nombre_sistema AS disciplina, su.fecha_vencimiento
              FROM Suscripcion su
              INNER JOIN Inscripcion i ON i.id_inscripcion = su.id_inscripcion
              INNER JOIN Sistema s ON s.id_sistema = i.id_sistema
             WHERE i.id_persona = ?
               AND su.id_estado <> 3
               AND su.fecha_vencimiento >= CAST(GETDATE() AS date)
             ORDER BY su.fecha_vencimiento DESC, s.nombre_sistema
            SQL, [$idPersona])->getResultArray();

        $salida = [];
        $vistas = [];

        foreach ($rows as $row) {
            $disciplina = (string) $row['disciplina'];
            $fecha      = $this->fechaNormalizada($row['fecha_vencimiento']);

            if ($disciplina === '' || $fecha === null || isset($vistas[$disciplina])) {
                continue;
            }

            $vistas[$disciplina] = true;
            $salida[] = [
                'disciplina'  => $disciplina,
                'vencimiento' => $fecha,
            ];
        }

        return $salida;
    }

    private function ultimoVencimiento(int $idPersona): ?string
    {
        $row = $this->db->query(<<<'SQL'
            SELECT MAX(su.fecha_vencimiento) AS vencimiento
              FROM Suscripcion su
              INNER JOIN Inscripcion i ON i.id_inscripcion = su.id_inscripcion
             WHERE i.id_persona = ?
               AND su.id_estado <> 3
            SQL, [$idPersona])->getRowArray();

        return $row ? $this->fechaNormalizada($row['vencimiento']) : null;
    }

    /**
     * @param array<int, array{disciplina: string, vencimiento: string}> $suscripciones
     */
    private function maximaFechaVencimiento(array $suscripciones): ?string
    {
        $maxima = null;

        foreach ($suscripciones as $suscripcion) {
            if ($maxima === null || $suscripcion['vencimiento'] > $maxima) {
                $maxima = $suscripcion['vencimiento'];
            }
        }

        return $maxima;
    }

    private function diasHasta(string $fecha): int
    {
        $hoy = new \DateTime(date('Y-m-d'));

        return (int) $hoy->diff(new \DateTime($fecha))->format('%r%a');
    }

    private function fechaNormalizada($valor): ?string
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        if ($valor instanceof \DateTimeInterface) {
            return $valor->format('Y-m-d');
        }

        return substr((string) $valor, 0, 10);
    }

    /**
     * Filtro de fecha compartido por las métricas.
     * null/todo = historial completo, mes = mes actual, 30dias = últimos 30 días,
     * hoy = fecha actual.
     *
     * @return array{0: string, 1: array<int, string>}
     */
    private function filtroPeriodo(?string $periodo, string $alias = 'a'): array
    {
        $periodo = strtolower((string) $periodo);

        if ($periodo === 'mes') {
            return [" AND {$alias}.fecha >= ?", [date('Y-m-01')]];
        }

        if ($periodo === '30dias') {
            return [" AND {$alias}.fecha >= ?", [date('Y-m-d', strtotime('-29 days'))]];
        }

        if ($periodo === 'hoy') {
            return [" AND {$alias}.fecha = ?", [date('Y-m-d')]];
        }

        return ['', []];
    }

    /* ==================================================================
       MÉTRICAS PARA REPORTES DE CONCURRENCIA
    ================================================================== */

    /**
     * Ingresos OK agrupados por día de la semana (1=Lunes ... 7=Domingo).
     * Se rellenan los días sin asistencias con 0.
     *
     * @return array<int, int>
     */
    public function getAsistenciasPorDiaSemana(?string $periodo = null): array
    {
        [$filtro, $bindings] = $this->filtroPeriodo($periodo);

        $rows = $this->db->query(<<<SQL
            SELECT (DATEDIFF(DAY, '19000101', a.fecha) % 7) + 1 AS dia,
                   COUNT(*) AS total
              FROM Asistencia a
             WHERE a.resultado = 'OK'
                   {$filtro}
             GROUP BY (DATEDIFF(DAY, '19000101', a.fecha) % 7) + 1
            SQL, $bindings)->getResultArray();

        $resultado = array_fill_keys(range(1, 7), 0);

        foreach ($rows as $row) {
            $dia = (int) $row['dia'];

            if ($dia >= 1 && $dia <= 7) {
                $resultado[$dia] = (int) $row['total'];
            }
        }

        return $resultado;
    }

    /**
     * Evolución mensual de ingresos OK de los últimos $meses meses.
     *
     * @return array<int, array{anio: int, mes: int, etiqueta: string, total: int}>
     */
    public function getAsistenciasPorMes(int $meses = 12): array
    {
        $meses   = max(1, min($meses, 60));
        $inicio  = new \DateTime(date('Y-m-01', strtotime('-' . ($meses - 1) . ' months')));
        $actual  = new \DateTime(date('Y-m-01'));
        $hasta   = $inicio->format('Y-m-d');

        $rows = $this->db->query(<<<'SQL'
            SELECT YEAR(a.fecha) AS anio, MONTH(a.fecha) AS mes, COUNT(*) AS total
              FROM Asistencia a
             WHERE a.resultado = 'OK'
               AND a.fecha >= ?
             GROUP BY YEAR(a.fecha), MONTH(a.fecha)
             ORDER BY anio, mes
            SQL, [$hasta])->getResultArray();

        $totales = [];

        foreach ($rows as $row) {
            $clave = (int) $row['anio'] . '-' . (int) $row['mes'];
            $totales[$clave] = (int) $row['total'];
        }

        $cortos   = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $resultado = [];
        $cursor    = clone $inicio;

        while ($cursor <= $actual) {
            $anio = (int) $cursor->format('Y');
            $mes  = (int) $cursor->format('m');
            $clave = $anio . '-' . $mes;

            $resultado[] = [
                'anio'    => $anio,
                'mes'     => $mes,
                'etiqueta'=> $cortos[$mes - 1] . ' ' . substr((string) $anio, 2),
                'total'   => $totales[$clave] ?? 0,
            ];

            $cursor->modify('+1 month');
        }

        return $resultado;
    }

    /**
     * Ingresos OK registrados en un mes (YYYY-MM).
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

        $row = $this->db->query(<<<'SQL'
            SELECT COUNT(*) AS total
              FROM Asistencia
             WHERE resultado = 'OK'
               AND fecha >= ?
               AND fecha <= ?
            SQL, [$inicio, $fin])->getRowArray();

        return (int) ($row['total'] ?? 0);
    }

    /**
     * Ingresos OK agrupados por hora del día (0..23).
     *
     * @return array<int, int>
     */
    public function getAsistenciasPorFranjaHoraria(?string $periodo = null): array
    {
        [$filtro, $bindings] = $this->filtroPeriodo($periodo);

        $rows = $this->db->query(<<<SQL
            SELECT CAST(LEFT(a.hora, 2) AS int) AS hora,
                   COUNT(*) AS total
              FROM Asistencia a
             WHERE a.resultado = 'OK'
                   {$filtro}
             GROUP BY CAST(LEFT(a.hora, 2) AS int)
             ORDER BY hora
            SQL, $bindings)->getResultArray();

        $resultado = array_fill_keys(range(0, 23), 0);

        foreach ($rows as $row) {
            $hora = (int) $row['hora'];

            if ($hora >= 0 && $hora <= 23) {
                $resultado[$hora] = (int) $row['total'];
            }
        }

        return $resultado;
    }

    /**
     * Socios con cuota vigente que no asistieron desde hace más de $diasSinAsistir.
     *
     * @return array<int, array{
     *     id_persona: int,
     *     nombre: string,
     *     telefono: string,
     *     vencimiento: ?string,
     *     dias_sin_asistir: ?int,
     *     disciplinas: string
     * }>
     */
    public function getClientesInactivosConCuota(int $diasSinAsistir = 15): array
    {
        $diasSinAsistir = max(0, $diasSinAsistir);

        $rows = $this->db->query(<<<'SQL'
            SELECT p.id_persona,
                   p.nombre + ' ' + p.apellido AS nombre,
                   p.telefono,
                   MAX(su.fecha_vencimiento) AS vencimiento,
                   u.ultima,
                   CASE WHEN u.ultima IS NULL THEN NULL
                        ELSE DATEDIFF(DAY, u.ultima, GETDATE())
                   END AS dias_sin_asistir,
                   (
                       SELECT STRING_AGG(d.nombre_sistema, ', ') WITHIN GROUP (ORDER BY d.nombre_sistema)
                         FROM (
                              SELECT DISTINCT s2.nombre_sistema
                                FROM Suscripcion su2
                                INNER JOIN Inscripcion i2 ON i2.id_inscripcion = su2.id_inscripcion
                                INNER JOIN Sistema s2 ON s2.id_sistema = i2.id_sistema
                               WHERE i2.id_persona = p.id_persona
                                 AND su2.id_estado <> 3
                                 AND su2.fecha_vencimiento >= CAST(GETDATE() AS date)
                             ) d
                   ) AS disciplinas
              FROM Persona p
              INNER JOIN Inscripcion i ON i.id_persona = p.id_persona
              INNER JOIN Suscripcion su ON su.id_inscripcion = i.id_inscripcion
              LEFT JOIN (
                  SELECT id_persona, MAX(fecha) AS ultima
                    FROM Asistencia
                   WHERE resultado = 'OK'
                   GROUP BY id_persona
              ) u ON u.id_persona = p.id_persona
             WHERE p.id_rol = 3
               AND p.baja <> 'S'
               AND su.id_estado <> 3
               AND su.fecha_vencimiento >= CAST(GETDATE() AS date)
               AND (u.ultima IS NULL OR u.ultima < DATEADD(DAY, -?, CAST(GETDATE() AS date)))
             GROUP BY p.id_persona, p.nombre, p.apellido, p.telefono, u.ultima
             ORDER BY u.ultima ASC, p.apellido, p.nombre
            SQL, [$diasSinAsistir])->getResultArray();

        $resultado = [];

        foreach ($rows as $row) {
            $resultado[] = [
                'id_persona'       => (int) $row['id_persona'],
                'nombre'           => trim((string) $row['nombre']),
                'telefono'         => (string) ($row['telefono'] ?? ''),
                'vencimiento'      => $this->fechaNormalizada($row['vencimiento']),
                'dias_sin_asistir' => $row['dias_sin_asistir'] === null ? null : (int) $row['dias_sin_asistir'],
                'disciplinas'      => (string) ($row['disciplinas'] ?? ''),
            ];
        }

        return $resultado;
    }
}
