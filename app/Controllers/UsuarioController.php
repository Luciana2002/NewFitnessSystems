<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\DatosPersonalesModel;

class UsuarioController extends BaseController
{
    public function login(): string
    {
        helper(['form', 'url']);

        return view('front/header')
             . view('front/navbar')
             . view('back/usuario/login')
             . view('front/footer');
    }

    public function auth()
    {
        $session = session();
        $model = new UsuarioModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('pass');

        $data = $model->getUsuarioPorEmail($email);

        if ($data) {

            /*
             * Si tus contraseñas están con password_hash(), usá esto:
             * $verifyPass = password_verify($password, $data['contraseña']);
             *
             * Como tus inserts tienen '1234' en texto plano, usamos comparación directa:
             */
            $verifyPass = ($password === $data['contraseña']);

            if ($verifyPass) {
                $sessionData = [
                    'id_usuario'    => $data['id_usuario'],
                    'id_persona'    => $data['id_persona'],
                    'nombre'        => $data['nombre'],
                    'apellido'      => $data['apellido'],
                    'email'         => $data['email'],
                    'telefono'      => $data['telefono'],
                    'dni'           => $data['dni'],
                    'nombre_usuario'=> $data['nombre_usuario'],
                    'id_rol'        => $data['id_rol'],
                    'rol'           => $data['rol'],
                    'logged_in'     => true
                ];

                $session->set($sessionData);

                return redirect()->to('/usuario_logueado');
            } else {
                $session->setFlashdata('msg', 'Contraseña incorrecta');
                return redirect()->to('/login');
            }

        } else {
            $session->setFlashdata('msg', 'No existe el correo o es incorrecto');
            return redirect()->to('/login');
        }
    }

    public function registro(): string
    {
        helper(['form', 'url']);

        return view('front/header')
             . view('front/navbar')
             . view('back/usuario/registro')
             . view('front/footer');
    }

    public function guardarRegistro()
    {
        helper(['form', 'url']);

        $validationRules = [
            'nombre'   => 'required|min_length[3]',
            'apellido' => 'required|min_length[3]|max_length[50]',
            'usuario'  => 'required|min_length[3]|is_unique[Usuario.nombre_usuario]',
            'email'    => 'required|min_length[4]|max_length[100]|valid_email',
            'telefono' => 'required',
            'dni'      => 'required|is_unique[Datos_personales.dni]',
            'pass'     => 'required|min_length[4]|max_length[50]'
        ];

        if (!$this->validate($validationRules)) {
            return view('front/header')
                 . view('front/navbar')
                 . view('back/usuario/registro', [
                     'validation' => $this->validator
                 ])
                 . view('front/footer');
        }

        $personaModel = new DatosPersonalesModel();
        $usuarioModel = new UsuarioModel();

        $personaModel->insert([
            'nombre'   => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email'    => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'dni'      => $this->request->getPost('dni')
        ]);

        $idPersona = $personaModel->getInsertID();

        $usuarioModel->insert([
            'nombre_usuario' => $this->request->getPost('usuario'),
            'contraseña'     => $this->request->getPost('pass'),
            'id_rol'         => 3,
            'id_persona'     => $idPersona
        ]);

        session()->setFlashdata('success', 'Usuario registrado con éxito');
        return redirect()->to('/login');
    }

    public function usuarioLogueado(): string
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        return view('front/header')
             . view('front/navbar')
             . view('back/usuario/usuario_logueado')
             . view('front/footer');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}