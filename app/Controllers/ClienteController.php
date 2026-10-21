<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\ClienteModel;
use App\Models\PagoModel;
use App\Models\SistemaModel;
use App\Models\HorarioModel;
use App\Models\DatosPersonalesModel;

class ClienteController extends BaseController
{
    private function validarAdmin()
    {
        if (session()->get('id_rol') != 1) {
            return redirect()->to('/usuario_logueado');
        }

        return null;
    }

    private function validarAcceso()
    {
        $rol = session()->get('id_rol');

        if (!in_array($rol, [1, 2])) {
            return redirect()->to('/usuario_logueado');
        }

        return null;
    }

    public function nuevoCliente()
    {
        $validacion = $this->validarAcceso();
        if ($validacion) return $validacion;

        $nombre = trim($this->request->getPost('nombre') ?? '');
        $apellido = trim($this->request->getPost('apellido') ?? '');
        $email = trim($this->request->getPost('email') ?? '');
        $telefono = trim($this->request->getPost('telefono') ?? '');
        $dni = trim($this->request->getPost('dni') ?? '');
        $idSistema = (int) $this->request->getPost('id_sistema');
        $idHorario = (int) $this->request->getPost('id_horario');
        $idMedioPago = (int) $this->request->getPost('id_medio_pago');
        $monto = $this->request->getPost('monto');

        if ($nombre === '' || $apellido === '' || $telefono === '' || $dni === '' || !$idSistema || !$idMedioPago) {
            session()->setFlashdata('error', 'Completá todos los campos obligatorios');
            return redirect()->to('/clientes');
        }

        $sistemaModel = new SistemaModel();
        $horarioModel = new HorarioModel();

        $sistema = $sistemaModel->find($idSistema);

        if (!$sistema) {
            session()->setFlashdata('error', 'El sistema seleccionado no existe');
            return redirect()->to('/clientes');
        }

        $accesoLibre = !empty($sistema['acceso_libre']);

        if ($accesoLibre) {
            $idHorario = null;
        } else {
            if (!$idHorario) {
                session()->setFlashdata('error', 'Elegí un turno para el sistema seleccionado');
                return redirect()->to('/clientes');
            }

            $horario = $horarioModel->find($idHorario);

            if (!$horario || (int) $horario['id_sistema'] !== $idSistema) {
                session()->setFlashdata('error', 'El turno seleccionado no pertenece al sistema elegido');
                return redirect()->to('/clientes');
            }

            if ($horarioModel->estaLlenoTurno($idSistema, $horario['hora_inicio'], $horario['hora_fin'])) {
                session()->setFlashdata('error', 'El turno seleccionado alcanzó su cupo máximo de inscriptos');
                return redirect()->to('/clientes');
            }
        }

        $personaModel = new DatosPersonalesModel();

        $existeDni = $personaModel->where('dni', $dni)->first();

        if ($existeDni) {
            session()->setFlashdata('error', 'Ya existe una persona registrada con ese DNI');
            return redirect()->to('/clientes');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $personaModel->insert([
            'nombre'   => $nombre,
            'apellido' => $apellido,
            'email'    => $email,
            'telefono' => $telefono,
            'dni'      => $dni,
            'id_rol'   => 3,
            'baja'     => 'N'
        ]);

        $idPersona = $personaModel->getInsertID();

        $hoy = date('Y-m-d');
        $vencimiento = date('Y-m-d', strtotime('+1 month', strtotime($hoy)));

        $db->table('Inscripcion')->insert([
            'fecha_inscripcion' => $hoy,
            'id_persona'        => $idPersona,
            'id_sistema'        => $idSistema,
            'id_horario'        => $idHorario
        ]);

        $idInscripcion = $db->insertID();

        $db->table('Suscripcion')->insert([
            'fecha_inicio'      => $hoy,
            'fecha_vencimiento' => $vencimiento,
            'id_estado'         => 1,
            'id_inscripcion'    => $idInscripcion
        ]);

        $idSuscripcion = $db->insertID();

        $mensualidad = $db->query(
            "SELECT TOP 1 id_mensualidad, precio
             FROM Mensualidad
             WHERE id_sistema = ?
             ORDER BY fecha_vigencia DESC",
            [$idSistema]
        )->getRowArray();

        if (empty($monto) || (float) $monto <= 0) {
            $monto = $mensualidad['precio'] ?? 0;
        }

        $db->table('Pago')->insert([
            'fecha_pago'    => $hoy,
            'monto'         => (float) $monto,
            'id_medio_pago' => $idMedioPago,
            'id_mensualidad'=> $mensualidad['id_mensualidad'] ?? null,
            'cobrado_por'   => session()->get('id_usuario'),
            'id_suscripcion'=> $idSuscripcion
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'No se pudo registrar el cliente');
            return redirect()->to('/clientes');
        }

        nfs_log('ALTA', 'Cliente', $idPersona, "Cliente {$apellido}, {$nombre} (DNI {$dni})");

        session()->setFlashdata('success', 'Cliente registrado con su primer pago');
        return redirect()->to('/clientes');
    }

    public function index()
    {
        $validacion = $this->validarAcceso();
        if ($validacion) return $validacion;

        $clienteModel = new ClienteModel();
        $sistemaModel = new SistemaModel();
        $pagoModel = new PagoModel();

        $clientes = $clienteModel->getClientesConInfo();

        usort($clientes, function ($a, $b) {
            $tsA = !empty($a['ultimo_pago']) ? strtotime((string) $a['ultimo_pago']) : 0;
            $tsB = !empty($b['ultimo_pago']) ? strtotime((string) $b['ultimo_pago']) : 0;

            if ($tsA !== $tsB) {
                return $tsB <=> $tsA;
            }

            return strcmp($a['apellido'], $b['apellido'])
                ?: strcmp($a['nombre'], $b['nombre']);
        });

        $data['clientes'] = $clientes;
        $data['sistemasRegistro'] = $sistemaModel->getSistemasConPrecio();
        $data['mediosPago'] = $pagoModel->getMediosPago();
        $data['horariosPorSistema'] = $this->horariosPorSistema();
        $data['sistemasAccesoLibre'] = $this->sistemasAccesoLibre();

        return view('front/header')
             . view('front/navbar')
             . view('clientes/lista_clientes', $data)
             . view('front/footer');
    }

    /**
     * Ids de sistemas de acceso libre (no requieren turno).
     */
    private function sistemasAccesoLibre(): array
    {
        $sistemaModel = new SistemaModel();

        $ids = [];

        foreach ($sistemaModel->getSistemasConPrecio(true) as $sistema) {
            if (!empty($sistema['acceso_libre'])) {
                $ids[] = (int) $sistema['id_sistema'];
            }
        }

        return $ids;
    }

    /**
     * Turnos activos agrupados por sistema, con cupo e inscriptos.
     * Un turno es una hora (inicio/fin) dentro de un sistema y abarca todos sus días.
     * Estructura: [id_sistema => [ ['id_horario', 'label', 'cupo_max', 'inscriptos', 'lleno'], ... ]]
     */
    private function horariosPorSistema(): array
    {
        $horarioModel = new HorarioModel();
        $sistemaModel = new SistemaModel();

        $agrupados = [];

        foreach ($sistemaModel->getSistemasConPrecio(true) as $sistema) {
            $idSistema = (int) $sistema['id_sistema'];
            $agrupados[$idSistema] = [];

            if (!empty($sistema['acceso_libre'])) {
                continue;
            }

            foreach ($horarioModel->getTurnosPorSistema($idSistema) as $turno) {
                $cupo = $turno['cupo_max'];
                $inscriptos = (int) $turno['inscriptos'];

                $agrupados[$idSistema][] = [
                    'id_horario' => (int) $turno['id_horario'],
                    'label'      => nfs_turno_label($turno),
                    'cupo_max'   => $cupo,
                    'inscriptos' => $inscriptos,
                    'lleno'      => $cupo !== null && (int) $cupo > 0 && $inscriptos >= (int) $cupo,
                ];
            }
        }

        return $agrupados;
    }

    public function detalle($id)
    {
        $validacion = $this->validarAcceso();
        if ($validacion) return $validacion;

        $clienteModel = new ClienteModel();
        $pagoModel = new PagoModel();

        $data['cliente'] = $clienteModel->getClienteDetalle($id);

        if (!$data['cliente']) {
            session()->setFlashdata('error', 'Cliente no encontrado');
            return redirect()->to('/clientes');
        }

        $esProfesor = (int) $data['cliente']['id_rol'] === 2;
        $data['esProfesor'] = $esProfesor;

        if ($esProfesor) {
            $data['suscripciones'] = [];
            $data['pagos'] = [];
        } else {
            $data['suscripciones'] = $clienteModel->getSuscripcionesCliente($id);
            $data['pagos'] = $pagoModel->getPagosCliente($id);
        }

        return view('front/header')
             . view('front/navbar')
             . view('clientes/detalle_cliente', $data)
             . view('front/footer');
    }

    public function editar($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $personaModel = new DatosPersonalesModel();
        $usuarioModel = new UsuarioModel();
        $clienteModel = new ClienteModel();
        $sistemaModel = new SistemaModel();

        $data['cliente'] = $personaModel->find($id);

        if (!$data['cliente']) {
            session()->setFlashdata('error', 'Persona no encontrada');
            return redirect()->to('/clientes');
        }

        $data['usuario'] = $usuarioModel->getUsuarioPorPersona($id);
        $data['sistemas'] = $clienteModel->getSuscripcionesCliente($id);
        $data['sistemasDisponibles'] = $sistemaModel->getSistemasConPrecio();
        $data['horariosPorSistema'] = $this->horariosPorSistema();
        $data['sistemasAccesoLibre'] = $this->sistemasAccesoLibre();

        return view('front/header')
             . view('front/navbar')
             . view('clientes/editar_clientes', $data)
             . view('front/footer');
    }

    public function actualizar($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $personaModel = new DatosPersonalesModel();

        $datosPersona = [
            'nombre'   => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email'    => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'dni'      => $this->request->getPost('dni'),
            'baja'     => $this->request->getPost('persona_activa') ? 'N' : 'S',
        ];

        if (session()->get('id_rol') == 1) {
            $datosPersona['id_rol'] = $this->request->getPost('id_rol');
        }

        $personaModel->update($id, $datosPersona);

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->getUsuarioPorPersona($id);

        if ($usuario) {
            $datosUsuario = [
                'nombre_usuario' => $this->request->getPost('nombre_usuario')
            ];

            $pass = $this->request->getPost('pass');

            if (!empty($pass)) {
                $datosUsuario['contraseña'] = password_hash($pass, PASSWORD_DEFAULT);
            }

            $usuarioModel->update($usuario['id_usuario'], $datosUsuario);
        }

        $db = \Config\Database::connect();

        $inscripciones = $db->table('Inscripcion')
            ->where('id_persona', $id)
            ->get()->getResultArray();

        $sistemasActivos = $this->request->getPost('sistema_activo') ?? [];

        if (!is_array($sistemasActivos)) {
            $sistemasActivos = [];
        }

        foreach ($inscripciones as $inscripcion) {
            $idInscripcion = $inscripcion['id_inscripcion'];

            $suscripcion = $db->table('Suscripcion')
                ->where('id_inscripcion', $idInscripcion)
                ->orderBy('fecha_inicio', 'DESC')
                ->get()->getRowArray();

            if (!$suscripcion) {
                continue;
            }

            $estadoActual = (int) $suscripcion['id_estado'];
            $estaMarcado = in_array((string) $idInscripcion, $sistemasActivos, true);

            if ($estaMarcado && $estadoActual == 3) {
                $db->table('Suscripcion')
                    ->where('id_suscripcion', $suscripcion['id_suscripcion'])
                    ->update([
                        'id_estado'         => 1,
                        'fecha_vencimiento' => date('Y-m-d', strtotime('+1 month'))
                    ]);
            } elseif (!$estaMarcado && $estadoActual != 3) {
                $db->table('Suscripcion')
                    ->where('id_suscripcion', $suscripcion['id_suscripcion'])
                    ->update(['id_estado' => 3]);
            }
        }

        $agregarSistema = (int) $this->request->getPost('agregar_sistema');
        $agregarHorario = (int) $this->request->getPost('agregar_horario');

        if ($agregarSistema) {
            $sistemaModel = new SistemaModel();
            $horarioModel = new HorarioModel();

            $sistemaValido = $sistemaModel->where('id_sistema', $agregarSistema)
                ->where('baja', 'N')
                ->first();

            if (!$sistemaValido) {
                session()->setFlashdata('error', 'El sistema seleccionado no existe');
                return redirect()->to('/clientes');
            }

            $accesoLibre = !empty($sistemaValido['acceso_libre']);

            if ($accesoLibre) {
                $agregarHorario = null;
            } else {
                if (!$agregarHorario) {
                    session()->setFlashdata('error', 'Elegí un turno para el sistema seleccionado');
                    return redirect()->to('/clientes');
                }

                $horario = $horarioModel->find($agregarHorario);

                if (!$horario || (int) $horario['id_sistema'] !== $agregarSistema) {
                    session()->setFlashdata('error', 'El turno seleccionado no pertenece al sistema elegido');
                    return redirect()->to('/clientes');
                }
            }

            $yaInscripto = $db->table('Inscripcion')
                ->where('id_persona', $id)
                ->where('id_sistema', $agregarSistema)
                ->get()->getRow();

            if (!$yaInscripto) {
                if (!$accesoLibre && $horarioModel->estaLlenoTurno($agregarSistema, $horario['hora_inicio'], $horario['hora_fin'])) {
                    session()->setFlashdata('error', 'El turno seleccionado alcanzó su cupo máximo de inscriptos');
                    return redirect()->to('/clientes');
                }

                $hoy = date('Y-m-d');
                $vencimiento = date('Y-m-d', strtotime('+1 month', strtotime($hoy)));

                $db->table('Inscripcion')->insert([
                    'fecha_inscripcion' => $hoy,
                    'id_persona'        => $id,
                    'id_sistema'        => $agregarSistema,
                    'id_horario'        => $agregarHorario
                ]);

                $idInscripcion = $db->insertID();

                $db->table('Suscripcion')->insert([
                    'fecha_inicio'      => $hoy,
                    'fecha_vencimiento' => $vencimiento,
                    'id_estado'         => 1,
                    'id_inscripcion'    => $idInscripcion
                ]);
            }
        }

        nfs_log('EDICION', 'Cliente', $id, 'Cliente modificado: ' . trim($this->request->getPost('nombre') . ' ' . $this->request->getPost('apellido')));

        session()->setFlashdata('success', 'Cliente actualizado correctamente');
        return redirect()->to('/clientes');
    }

    public function baja($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $personaModel = new DatosPersonalesModel();

        $personaModel->update($id, [
            'baja' => 'S'
        ]);

        nfs_log('BAJA', 'Cliente', $id, 'Cliente dado de baja');

        session()->setFlashdata('success', 'Cliente dado de baja correctamente');
        return redirect()->to('/clientes');
    }

    public function alta($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $personaModel = new DatosPersonalesModel();

        $personaModel->update($id, [
            'baja' => 'N'
        ]);

        nfs_log('ALTA', 'Cliente', $id, 'Cliente reactivado');

        session()->setFlashdata('success', 'Cliente activado correctamente');
        return redirect()->to('/clientes');
    }
}