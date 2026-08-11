<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\PersonaModel;
use App\Models\DatosPersonalesModel;
use CodeIgniter\HTTP\RedirectResponse;

class UsuarioController extends BaseController
{
    private function validarAdmin()
    {
        if (session()->get('id_rol') != 1) {
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

    public function editarUsuario($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $usuarioModel = new UsuarioModel();

        $data['usuario'] = $usuarioModel->getUsuarioCompleto($id);

        return view('front/header')
             . view('front/navbar')
             . view('usuario/editar_usuario', $data)
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

    public function login(): string
    {
        helper(['form', 'url']);

        return view('front/header')
             . view('front/navbar')
             . view('usuario/login')
             . view('front/footer');
    }

    public function auth()
    {
        $session = session();
        $model = new UsuarioModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('pass');

        $data = $model->getUsuarioPorEmail($email);

        if (!$data) {
            $session->setFlashdata('msg', 'No existe el correo o es incorrecto');
            return redirect()->to('/login');
        }

        if ($data['baja'] == 'S') {
            $session->setFlashdata('msg', 'El usuario se encuentra dado de baja');
            return redirect()->to('/login');
        }

        $algo = password_get_info($data['contraseña'])['algo'];

        if ($algo !== null) {
            $verifyPass = password_verify($password, $data['contraseña']);
        } else {
            $verifyPass = ($password === $data['contraseña']);
        }

        if (!$verifyPass) {
            $session->setFlashdata('msg', 'Contraseña incorrecta');
            return redirect()->to('/login');
        }

        $sessionData = [
            'id_usuario'     => $data['id_usuario'],
            'id_persona'     => $data['id_persona'],
            'nombre'         => $data['nombre'],
            'apellido'       => $data['apellido'],
            'email'          => $data['email'],
            'telefono'       => $data['telefono'],
            'dni'            => $data['dni'],
            'nombre_usuario' => $data['nombre_usuario'],
            'id_rol'         => $data['id_rol'],
            'rol'            => $data['rol'],
            'logged_in'      => true
        ];

        $session->set($sessionData);

        return redirect()->to('/usuario_logueado');
    }

    public function registro(): string
    {
        helper(['form', 'url']);

        return view('front/header')
             . view('front/navbar')
             . view('usuario/registro')
             . view('front/footer');
    }

    public function guardarRegistro()
    {
        helper(['form', 'url']);

        $validationRules = [
            'nombre'   => 'required|min_length[3]',
            'apellido' => 'required|min_length[3]|max_length[50]',
            'usuario'  => 'required|min_length[3]|is_unique[Usuario.nombre_usuario]',
            'email'    => 'required|min_length[4]|max_length[100]|valid_email|is_unique[Persona.email]',
            'telefono' => 'required',
            'dni'      => 'required|is_unique[Persona.dni]',
            'pass'     => 'required|min_length[4]|max_length[50]'
        ];

        if (!$this->validate($validationRules)) {
            return view('front/header')
                 . view('front/navbar')
                 . view('usuario/registro', [
                     'validation' => $this->validator
                 ])
                 . view('front/footer');
        }

        $personaModel = new PersonaModel();
        $usuarioModel = new UsuarioModel();

        $personaModel->insert([
            'nombre'   => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email'    => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'dni'      => $this->request->getPost('dni'),
            'id_rol'   => 3,
            'baja'     => 'N'
        ]);

        $idPersona = $personaModel->getInsertID();

        $usuarioModel->insert([
            'nombre_usuario' => $this->request->getPost('usuario'),
            'contraseña'     => password_hash($this->request->getPost('pass'), PASSWORD_DEFAULT),
            'id_persona'     => $idPersona
        ]);

        session()->setFlashdata('success', 'Usuario registrado con éxito');
        return redirect()->to('/login');
    }

    public function usuarioLogueado(): string|RedirectResponse
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        return view('front/header')
             . view('front/navbar')
             . view('usuario/usuario_logueado')
             . view('front/footer');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}