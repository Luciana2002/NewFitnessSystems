<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\DatosPersonalesModel;
use App\Models\UsuarioModel;

class ProfesorController extends BaseController
{
    private function validarAdmin()
    {
        if (session()->get('id_rol') != 1) {
            return redirect()->to('/usuario_logueado');
        }

        return null;
    }

    public function index()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $clienteModel = new ClienteModel();

        $data['profesores'] = $clienteModel->getProfesoresConInfo();

        return view('front/header')
             . view('front/navbar')
             . view('administrador/lista_profesores', $data)
             . view('front/footer');
    }

    public function nuevo()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $nombre   = trim($this->request->getPost('nombre') ?? '');
        $apellido = trim($this->request->getPost('apellido') ?? '');
        $email    = trim($this->request->getPost('email') ?? '');
        $telefono = trim($this->request->getPost('telefono') ?? '');
        $dni      = trim($this->request->getPost('dni') ?? '');
        $usuario  = trim($this->request->getPost('usuario') ?? '');
        $pass     = $this->request->getPost('pass');

        if ($nombre === '' || $apellido === '' || $telefono === '' || $dni === '' || $usuario === '' || empty($pass)) {
            session()->setFlashdata('error', 'Completá todos los campos obligatorios');
            return redirect()->to('/profesores');
        }

        $personaModel = new DatosPersonalesModel();
        $usuarioModel = new UsuarioModel();

        if ($personaModel->where('dni', $dni)->first()) {
            session()->setFlashdata('error', 'Ya existe una persona registrada con ese DNI');
            return redirect()->to('/profesores');
        }

        if ($usuarioModel->where('nombre_usuario', $usuario)->first()) {
            session()->setFlashdata('error', 'Ese nombre de usuario ya está en uso');
            return redirect()->to('/profesores');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $personaModel->insert([
            'nombre'   => $nombre,
            'apellido' => $apellido,
            'email'    => $email,
            'telefono' => $telefono,
            'dni'      => $dni,
            'id_rol'   => 2,
            'baja'     => 'N'
        ]);

        $idPersona = $personaModel->getInsertID();

        $usuarioModel->insert([
            'nombre_usuario' => $usuario,
            'contraseña'     => password_hash($pass, PASSWORD_DEFAULT),
            'id_persona'     => $idPersona
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'No se pudo registrar el profesor');
            return redirect()->to('/profesores');
        }

        session()->setFlashdata('success', 'Profesor registrado correctamente');
        return redirect()->to('/profesores');
    }

    public function editar($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $personaModel = new DatosPersonalesModel();
        $usuarioModel = new UsuarioModel();

        $data['cliente'] = $personaModel->find($id);

        if (!$data['cliente']) {
            session()->setFlashdata('error', 'Profesor no encontrado');
            return redirect()->to('/profesores');
        }

        $data['usuario'] = $usuarioModel->getUsuarioPorPersona($id);

        return view('front/header')
             . view('front/navbar')
             . view('administrador/editar_profesor', $data)
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

        session()->setFlashdata('success', 'Profesor actualizado correctamente');
        return redirect()->to('/profesores');
    }
}
