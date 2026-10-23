<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\PagoModel;
use App\Models\AsistenciaModel;
use App\Models\RutinaModel;
use App\Models\HorarioModel;
use App\Models\ReservaModel;
use App\Models\SeguimientoFisicoModel;

class ClientePanelController extends BaseController
{
    private function validarCliente()
    {
        if (! session()->get('logged_in') || session()->get('id_rol') != 3) {
            return redirect()->to('/login');
        }

        return null;
    }

    private function idPersona(): int
    {
        return (int) session()->get('id_persona');
    }

    public function suscripcion()
    {
        $validacion = $this->validarCliente();
        if ($validacion) return $validacion;

        helper('membresia');

        $idPersona = $this->idPersona();

        $clienteModel = new ClienteModel();
        $pagoModel    = new PagoModel();
        $asistenciaModel = new AsistenciaModel();

        $data['suscripciones'] = $clienteModel->getSuscripcionesCliente($idPersona);
        $data['pagos']         = $pagoModel->getPagosCliente($idPersona);
        $data['asistencias']   = $asistenciaModel->getAsistenciasCliente($idPersona);
        $data['membresia']     = nfs_membresia_cliente($idPersona);
        $data['idPersona']     = $idPersona;

        return view('front/header')
             . view('front/navbar')
             . view('cliente/panel_suscripcion', $data)
             . view('front/footer');
    }

    public function comprobante($idPago)
    {
        $validacion = $this->validarCliente();
        if ($validacion) return $validacion;

        $idPersona = $this->idPersona();

        $db = \Config\Database::connect();

        $pago = $db->query(
            'SELECT pg.id_pago, pg.fecha_pago, pg.monto,
                    mp.descripcion AS medio_pago,
                    s.nombre_sistema,
                    per.nombre, per.apellido, per.dni, per.email, per.telefono,
                    u.nombre_usuario AS cobrado_por,
                    su.fecha_vencimiento,
                    sus.descripcion AS estado_suscripcion
               FROM Pago pg
               INNER JOIN Suscripcion su ON su.id_suscripcion = pg.id_suscripcion
               INNER JOIN Inscripcion i ON i.id_inscripcion = su.id_inscripcion
               INNER JOIN Persona per ON per.id_persona = i.id_persona
               INNER JOIN Sistema s ON s.id_sistema = i.id_sistema
               LEFT JOIN Medio_pago mp ON mp.id_medio_pago = pg.id_medio_pago
               LEFT JOIN Usuario u ON u.id_usuario = pg.cobrado_por
               LEFT JOIN Estado_suscripcion sus ON sus.id_estado = su.id_estado
              WHERE pg.id_pago = ? AND i.id_persona = ?',
            [$idPago, $idPersona]
        )->getRowArray();

        if (! $pago) {
            session()->setFlashdata('error', 'Comprobante no encontrado');
            return redirect()->to('/cliente/suscripcion');
        }

        $data['pago']    = $pago;
        $data['nro']     = str_pad((string) $pago['id_pago'], 8, '0', STR_PAD_LEFT);
        $data['empresa'] = 'New Fitness Systems';

        return view('cliente/comprobante', $data);
    }

    /**
     * Clases disponibles en los próximos 7 días y reservas del cliente.
     */
    public function reservas()
    {
        $validacion = $this->validarCliente();
        if ($validacion) return $validacion;

        helper('membresia');

        $idPersona = $this->idPersona();

        $horarioModel = new HorarioModel();
        $reservaModel = new ReservaModel();

        $horarios  = $horarioModel->getHorariosActivos();
        $diasSemana = nfs_dias_semana();

        $porDia = [];

        foreach ($horarios as $horario) {
            $porDia[$horario['dia_semana']][] = $horario;
        }

        $ocurrencias = [];
        $hoy = new \DateTime(date('Y-m-d'));

        for ($i = 0; $i < 7; $i++) {
            $fechaObj  = (clone $hoy)->modify("+$i day");
            $numeroDia = (int) $fechaObj->format('N');
            $nombreDia = $diasSemana[$numeroDia] ?? null;

            if ($nombreDia === null || ! isset($porDia[$nombreDia])) {
                continue;
            }

            foreach ($porDia[$nombreDia] as $horario) {
                $fecha = $fechaObj->format('Y-m-d');

                $ocupado  = $reservaModel->contarReservados((int) $horario['id_horario'], $fecha);
                $miReserva = $reservaModel->getReserva((int) $horario['id_horario'], $idPersona, $fecha);

                $cupo = ($horario['cupo_max'] === null || (int) $horario['cupo_max'] <= 0)
                    ? PHP_INT_MAX
                    : (int) $horario['cupo_max'];

                $ocurrencias[] = [
                    'id_horario'     => $horario['id_horario'],
                    'fecha'          => $fecha,
                    'fecha_mostrar'  => $fechaObj->format('d/m/Y'),
                    'dia_semana'     => $nombreDia,
                    'hora_inicio'    => $horario['hora_inicio'],
                    'hora_fin'       => $horario['hora_fin'],
                    'nombre_sistema' => $horario['nombre_sistema'],
                    'color'          => $horario['color'],
                    'cupo'           => $cupo,
                    'ocupado'        => $ocupado,
                    'libres'         => max(0, $cupo - $ocupado),
                    'completo'       => $ocupado >= $cupo,
                    'mi_reserva'     => $miReserva,
                    'es_hoy'         => $i === 0,
                ];
            }
        }

        usort($ocurrencias, function ($a, $b) {
            return strcmp($a['fecha'] . $a['hora_inicio'], $b['fecha'] . $b['hora_inicio']);
        });

        $data['ocurrencias'] = $ocurrencias;
        $data['misReservas'] = $reservaModel->getReservasCliente($idPersona);
        $data['membresia']   = nfs_membresia_cliente($idPersona);

        return view('front/header')
             . view('front/navbar')
             . view('cliente/reservas', $data)
             . view('front/footer');
    }

    /**
     * Alta de una reserva (o lista de espera si la clase está completa).
     */
    public function reservar()
    {
        $validacion = $this->validarCliente();
        if ($validacion) return $validacion;

        helper('membresia');

        $idPersona = $this->idPersona();
        $idHorario = (int) $this->request->getPost('id_horario');
        $fecha     = trim($this->request->getPost('fecha') ?? '');

        if (! $idHorario || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            session()->setFlashdata('error', 'Datos de reserva inválidos');
            return redirect()->to('/cliente/reservas');
        }

        $membresia = nfs_membresia_cliente($idPersona);

        if (! $membresia['activo']) {
            session()->setFlashdata('error', 'Tu cuota está vencida. Regularizá tu pago para reservar.');
            return redirect()->to('/cliente/reservas');
        }

        $horarioModel = new HorarioModel();
        $horario = $horarioModel->where('id_horario', $idHorario)->where('baja', 'N')->first();

        if (! $horario) {
            session()->setFlashdata('error', 'El horario seleccionado no existe');
            return redirect()->to('/cliente/reservas');
        }

        $reservaModel = new ReservaModel();

        if ($reservaModel->getReserva($idHorario, $idPersona, $fecha)) {
            session()->setFlashdata('error', 'Ya tenés una reserva para esa clase');
            return redirect()->to('/cliente/reservas');
        }

        $cupoMax  = (int) ($horario['cupo_max'] ?? 0);
        $ocupado  = $reservaModel->contarReservados($idHorario, $fecha);
        $sinCupo  = $cupoMax > 0 && $ocupado >= $cupoMax;
        $estado   = $sinCupo ? 'LISTA_ESPERA' : 'RESERVADA';

        $reservaModel->insert([
            'id_horario'             => $idHorario,
            'id_persona'             => $idPersona,
            'fecha'                  => $fecha,
            'estado'                 => $estado,
            'id_usuario_inscripcion' => session()->get('id_usuario'),
        ]);

        if ($sinCupo) {
            session()->setFlashdata('success', 'La clase está completa. Quedaste en lista de espera.');
        } else {
            session()->setFlashdata('success', '¡Reserva confirmada!');
        }

        return redirect()->to('/cliente/reservas');
    }

    /**
     * Cancela la reserva propia y asciende al primero de lista de espera.
     */
    public function cancelarReserva($idReserva)
    {
        $validacion = $this->validarCliente();
        if ($validacion) return $validacion;

        $idPersona   = $this->idPersona();
        $reservaModel = new ReservaModel();

        $reserva = $reservaModel
            ->where('id_reserva', $idReserva)
            ->where('id_persona', $idPersona)
            ->first();

        if (! $reserva) {
            session()->setFlashdata('error', 'Reserva no encontrada');
            return redirect()->to('/cliente/reservas');
        }

        $eraReservada = $reserva['estado'] === 'RESERVADA';

        $reservaModel->update($reserva['id_reserva'], ['estado' => 'CANCELADA']);

        if ($eraReservada) {
            $espera = $reservaModel->getListaEspera((int) $reserva['id_horario'], $reserva['fecha']);

            if (! empty($espera)) {
                $reservaModel->update($espera[0]['id_reserva'], ['estado' => 'RESERVADA']);
            }
        }

        session()->setFlashdata('success', 'Reserva cancelada');
        return redirect()->to('/cliente/reservas');
    }

    /**
     * Código QR personal para el check-in en la puerta.
     */
    public function qr()
    {
        $validacion = $this->validarCliente();
        if ($validacion) return $validacion;

        helper('membresia');

        $idPersona = $this->idPersona();

        $data['membresia'] = nfs_membresia_cliente($idPersona);
        $data['codigo']    = 'NFS-' . $idPersona . '-' . session()->get('dni');
        $data['cliente']   = session()->get('nombre') . ' ' . session()->get('apellido');

        return view('front/header')
             . view('front/navbar')
             . view('cliente/qr', $data)
             . view('front/footer');
    }

    /**
     * Plan semanal de entrenamiento asignado por el profesor.
     */
    public function rutinas()
    {
        $validacion = $this->validarCliente();
        if ($validacion) return $validacion;

        $idPersona = $this->idPersona();

        helper('membresia');

        $rutinaModel = new RutinaModel();

        $dias = nfs_dias_semana_etiquetas();
        $rutinasPorDia = [];

        foreach (array_keys($dias) as $dia) {
            $rutinas = $rutinaModel->getRutinasClienteDia($idPersona, $dia);

            foreach ($rutinas as &$rutina) {
                $rutina['ejercicios'] = $rutinaModel->getEjercicios((int) $rutina['id_rutina']);
            }
            unset($rutina);

            $rutinasPorDia[$dia] = $rutinas;
        }

        $data['dias']           = $dias;
        $data['diaActual']      = nfs_dia_semana_actual();
        $data['rutinasPorDia']  = $rutinasPorDia;

        return view('front/header')
             . view('front/navbar')
             . view('cliente/rutinas', $data)
             . view('front/footer');
    }

    /**
     * Historial de medidas corporales con gráficos de evolución.
     */
    public function seguimiento()
    {
        $validacion = $this->validarCliente();
        if ($validacion) return $validacion;

        $idPersona = $this->idPersona();

        $segModel  = new SeguimientoFisicoModel();
        $historial = $segModel->getHistorial($idPersona);

        $data['historial'] = $historial;
        $data['ultimo']    = ! empty($historial) ? end($historial) : null;

        $chart = [
            'fechas'  => [],
            'peso'    => [],
            'grasa'   => [],
            'cintura' => [],
            'pecho'   => [],
            'brazo'   => [],
            'muslo'   => [],
            'cadera'  => [],
        ];

        foreach ($historial as $fila) {
            $fecha = $fila['fecha'];

            if ($fecha instanceof \DateTimeInterface) {
                $fecha = $fecha->format('Y-m-d');
            }

            $chart['fechas'][]  = $fecha;
            $chart['peso'][]    = $fila['peso'] !== null ? (float) $fila['peso'] : null;
            $chart['grasa'][]   = $fila['grasa_corporal'] !== null ? (float) $fila['grasa_corporal'] : null;
            $chart['cintura'][] = $fila['cintura'] !== null ? (float) $fila['cintura'] : null;
            $chart['pecho'][]   = $fila['pecho'] !== null ? (float) $fila['pecho'] : null;
            $chart['brazo'][]   = $fila['brazo'] !== null ? (float) $fila['brazo'] : null;
            $chart['muslo'][]   = $fila['muslo'] !== null ? (float) $fila['muslo'] : null;
            $chart['cadera'][]  = $fila['cadera'] !== null ? (float) $fila['cadera'] : null;
        }

        $data['chart'] = $chart;

        return view('front/header')
             . view('front/navbar')
             . view('cliente/seguimiento', $data)
             . view('front/footer');
    }

    /**
     * Alta/edición de una medición del seguimiento físico.
     */
    public function guardarSeguimiento()
    {
        $validacion = $this->validarCliente();
        if ($validacion) return $validacion;

        $idPersona = $this->idPersona();

        $fecha = trim($this->request->getPost('fecha') ?? '');

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            $fecha = date('Y-m-d');
        }

        $numero = function ($campo) {
            $valor = $this->request->getPost($campo);

            if ($valor === null || $valor === '') {
                return null;
            }

            return (float) str_replace(',', '.', (string) $valor);
        };

        $datos = [
            'id_persona'     => $idPersona,
            'fecha'          => $fecha,
            'peso'           => $numero('peso'),
            'grasa_corporal' => $numero('grasa_corporal'),
            'cintura'        => $numero('cintura'),
            'pecho'          => $numero('pecho'),
            'brazo'          => $numero('brazo'),
            'muslo'          => $numero('muslo'),
            'cadera'         => $numero('cadera'),
            'notas'          => trim($this->request->getPost('notas') ?? '') ?: null,
        ];

        $sinDatos = true;

        foreach (['peso', 'grasa_corporal', 'cintura', 'pecho', 'brazo', 'muslo', 'cadera'] as $campo) {
            if ($datos[$campo] !== null) {
                $sinDatos = false;
                break;
            }
        }

        if ($sinDatos) {
            session()->setFlashdata('error', 'Ingresá al menos una medida para guardar el registro');
            return redirect()->to('/cliente/seguimiento');
        }

        $segModel = new SeguimientoFisicoModel();

        $existente = $segModel->where('id_persona', $idPersona)->where('fecha', $fecha)->first();

        if ($existente) {
            $segModel->update($existente['id_seguimiento'], $datos);
        } else {
            $segModel->insert($datos);
        }

        session()->setFlashdata('success', 'Registro guardado correctamente');
        return redirect()->to('/cliente/seguimiento');
    }

    /**
     * Baja de una suscripción solicitada por el propio cliente.
     * Recibe el id de la inscripción y cancela sus suscripciones activas.
     */
    public function bajaSistema($idInscripcion)
    {
        $validacion = $this->validarCliente();
        if ($validacion) return $validacion;

        $idPersona     = $this->idPersona();
        $idInscripcion = (int) $idInscripcion;

        $db = \Config\Database::connect();

        $inscripcion = $db->table('Inscripcion')
            ->select('Inscripcion.id_inscripcion, Sistema.nombre_sistema')
            ->join('Sistema', 'Sistema.id_sistema = Inscripcion.id_sistema')
            ->where('Inscripcion.id_inscripcion', $idInscripcion)
            ->where('Inscripcion.id_persona', $idPersona)
            ->get()
            ->getRowArray();

        if (! $inscripcion) {
            session()->setFlashdata('error', 'No encontramos esa suscripción');
            return redirect()->to('/cliente/suscripcion');
        }

        $activas = $db->table('Suscripcion')
            ->where('id_inscripcion', $idInscripcion)
            ->where('id_estado', 1)
            ->countAllResults();

        if ($activas === 0) {
            session()->setFlashdata('error', 'Esa suscripción ya está dada de baja');
            return redirect()->to('/cliente/suscripcion');
        }

        $db->table('Suscripcion')
            ->where('id_inscripcion', $idInscripcion)
            ->where('id_estado', 1)
            ->update(['id_estado' => 3]);

        $motivo = trim((string) $this->request->getPost('motivo'));

        nfs_log(
            'BAJA',
            'Suscripcion',
            $idInscripcion,
            'Baja solicitada por el cliente: '
                . trim((string) session()->get('nombre') . ' ' . (string) session()->get('apellido'))
                . ' — ' . $inscripcion['nombre_sistema']
                . ($motivo !== '' ? ' | Motivo: ' . $motivo : '')
        );

        session()->setFlashdata(
            'success',
            'Listo, tu suscripción a ' . $inscripcion['nombre_sistema'] . ' fue dada de baja.'
        );

        return redirect()->to('/cliente/suscripcion');
    }
}
