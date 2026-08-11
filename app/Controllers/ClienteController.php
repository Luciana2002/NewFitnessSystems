<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\ClienteModel;
use App\Models\PagoModel;
use App\Models\SistemaModel;
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
        $idMedioPago = (int) $this->request->getPost('id_medio_pago');
        $monto = $this->request->getPost('monto');

        if ($nombre === '' || $apellido === '' || $telefono === '' || $dni === '' || !$idSistema || !$idMedioPago) {
            session()->setFlashdata('error', 'Completá todos los campos obligatorios');
            return redirect()->to('/clientes');
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
            'id_sistema'        => $idSistema
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

        return view('front/header')
             . view('front/navbar')
             . view('clientes/lista_clientes', $data)
             . view('front/footer');
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

        if ($agregarSistema) {
            $sistemaModel = new SistemaModel();
            $sistemaValido = $sistemaModel->where('id_sistema', $agregarSistema)
                ->where('baja', 'N')
                ->first();

            $yaInscripto = $db->table('Inscripcion')
                ->where('id_persona', $id)
                ->where('id_sistema', $agregarSistema)
                ->get()->getRow();

            if ($sistemaValido && !$yaInscripto) {
                $hoy = date('Y-m-d');
                $vencimiento = date('Y-m-d', strtotime('+1 month', strtotime($hoy)));

                $db->table('Inscripcion')->insert([
                    'fecha_inscripcion' => $hoy,
                    'id_persona'        => $id,
                    'id_sistema'        => $agregarSistema
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

        session()->setFlashdata('success', 'Cliente activado correctamente');
        return redirect()->to('/clientes');
    }
}