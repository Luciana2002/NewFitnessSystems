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

    public function usuarios()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        return redirect()->to('/clientes');
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
            'email'    => $email !== '' ? $email : null,
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

        $data['clientes'] = $clienteModel->getClientesConInfo();
        $data['sistemasRegistro'] = $sistemaModel->getSistemasConPrecio();
        $data['mediosPago'] = $pagoModel->getMediosPago();

        return view('front/header')
             . view('front/navbar')
             . view('clientes/lista_clientes', $data)
             . view('front/footer');
    }

    public function reporte()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $clienteModel = new ClienteModel();

        $clientes = $clienteModel->getClientesConInfo();

        $total = count($clientes);
        $alDia = 0;
        $vencido = 0;
        $sinPagos = 0;
        $sinSuscripcion = 0;
        $sistemas = [];

        foreach ($clientes as $cliente) {
            $sistemasCliente = $cliente['sistemas'] ?? [];
            $ultimoPago = $cliente['ultimo_pago'] ?? null;

            foreach ($sistemasCliente as $sistema) {
                $nombreSistema = $sistema['nombre_sistema'] ?? 'Sin sistema';
                $sistemas[$nombreSistema] = ($sistemas[$nombreSistema] ?? 0) + 1;
            }

            if (empty($sistemasCliente)) {
                $sinSuscripcion++;
            } elseif (empty($ultimoPago)) {
                $sinPagos++;
            } else {
                $tsPago = $ultimoPago instanceof DateTimeInterface
                    ? $ultimoPago->getTimestamp()
                    : strtotime((string) $ultimoPago);

                if ($tsPago && strtotime('+1 month', $tsPago) < time()) {
                    $vencido++;
                } else {
                    $alDia++;
                }
            }
        }

        arsort($sistemas);

        $data['total'] = $total;
        $data['alDia'] = $alDia;
        $data['vencido'] = $vencido;
        $data['sinPagos'] = $sinPagos;
        $data['sinSuscripcion'] = $sinSuscripcion;
        $data['sistemas'] = $sistemas;

        return view('front/header')
             . view('front/navbar')
             . view('clientes/reporte', $data)
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

        $data['suscripciones'] = $clienteModel->getSuscripcionesCliente($id);
        $data['pagos'] = $pagoModel->getPagosCliente($id);

        return view('front/header')
             . view('front/navbar')
             . view('clientes/detalle_cliente', $data)
             . view('front/footer');
    }

    public function editarUsuario($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $usuarioModel = new UsuarioModel();

        $data['usuario'] = $usuarioModel->getUsuarioCompleto($id);

        return view('front/header')
             . view('front/navbar')
             . view('back/usuario/editar_usuario', $data)
             . view('front/footer');
    }

    public function modificarUsuario($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $usuarioModel = new UsuarioModel();
        $personaModel = new DatosPersonalesModel();

        $usuario = $usuarioModel->find($id);

        if (!$usuario) {
            session()->setFlashdata('error', 'Usuario no encontrado');
            return redirect()->to('/usuarios');
        }

        $personaModel->update($usuario['id_persona'], [
            'nombre'   => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email'    => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'dni'      => $this->request->getPost('dni'),
            'id_rol'   => $this->request->getPost('id_rol')
        ]);

        $datosUsuario = [
            'nombre_usuario' => $this->request->getPost('nombre_usuario')
        ];

        $pass = $this->request->getPost('pass');

        if (!empty($pass)) {
            $datosUsuario['contraseña'] = password_hash($pass, PASSWORD_DEFAULT);
        }

        $usuarioModel->update($id, $datosUsuario);

        session()->setFlashdata('success', 'Usuario modificado correctamente');
        return redirect()->to('/usuarios');
    }

    public function editar($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $personaModel = new DatosPersonalesModel();
        $usuarioModel = new UsuarioModel();

        $data['cliente'] = $personaModel->find($id);

        if (!$data['cliente']) {
            session()->setFlashdata('error', 'Persona no encontrada');
            return redirect()->to('/clientes');
        }

        $data['usuario'] = $usuarioModel->getUsuarioPorPersona($id);

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

        $personaModel->update($id, [
            'nombre'   => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email'    => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'dni'      => $this->request->getPost('dni'),
            'id_rol'   => $this->request->getPost('id_rol')
        ]);

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

    public function bajaUsuario($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        if ($id == session()->get('id_usuario')) {
            session()->setFlashdata('error', 'No podés darte de baja a vos mismo');
            return redirect()->to('/usuarios');
        }

        $usuarioModel = new UsuarioModel();
        $personaModel = new DatosPersonalesModel();

        $usuario = $usuarioModel->find($id);

        if (!$usuario) {
            session()->setFlashdata('error', 'Usuario no encontrado');
            return redirect()->to('/usuarios');
        }

        $personaModel->update($usuario['id_persona'], [
            'baja' => 'S'
        ]);

        session()->setFlashdata('success', 'Usuario dado de baja correctamente');
        return redirect()->to('/usuarios');
    }

    public function altaUsuario($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $usuarioModel = new UsuarioModel();
        $personaModel = new DatosPersonalesModel();

        $usuario = $usuarioModel->find($id);

        if (!$usuario) {
            session()->setFlashdata('error', 'Usuario no encontrado');
            return redirect()->to('/usuarios');
        }

        $personaModel->update($usuario['id_persona'], [
            'baja' => 'N'
        ]);

        session()->setFlashdata('success', 'Usuario activado correctamente');
        return redirect()->to('/usuarios');
    }
}