<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\DatosPersonalesModel;

class AdminUsuarioController extends BaseController
{
    public function index()
    {
        if (session()->get('id_rol') != 1) {
            return redirect()->to('/usuario_logueado');
        }

        $usuarioModel = new UsuarioModel();

        $data['usuarios'] = $usuarioModel->getUsuariosAll();

        return view('front/header')
             . view('front/navbar')
             . view('back/usuario/lista_usuarios', $data)
             . view('front/footer');
    }

    public function editar($id)
    {
        if (session()->get('id_rol') != 1) {
            return redirect()->to('/usuario_logueado');
        }

        $usuarioModel = new UsuarioModel();

        $data['usuario'] = $usuarioModel->getUsuarioCompleto($id);

        return view('front/header')
             . view('front/navbar')
             . view('back/usuario/editar_usuario', $data)
             . view('front/footer');
    }

    public function modificar($id)
    {
        if (session()->get('id_rol') != 1) {
            return redirect()->to('/usuario_logueado');
        }

        $usuarioModel = new UsuarioModel();
        $personaModel = new DatosPersonalesModel();

        $usuario = $usuarioModel->find($id);

        $personaModel->update($usuario['id_persona'], [
            'nombre'   => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email'    => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'dni'      => $this->request->getPost('dni')
        ]);

        $datosUsuario = [
            'nombre_usuario' => $this->request->getPost('nombre_usuario'),
            'id_rol'         => $this->request->getPost('id_rol')
        ];

        $pass = $this->request->getPost('pass');

        if (!empty($pass)) {
            $datosUsuario['contraseña'] = password_hash($pass, PASSWORD_DEFAULT);
        }

        $usuarioModel->update($id, $datosUsuario);

        session()->setFlashdata('success', 'Usuario modificado correctamente');
        return redirect()->to('/usuarios');
    }

    public function baja($id)
    {
        if (session()->get('id_rol') != 1) {
            return redirect()->to('/usuario_logueado');
        }

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

    public function alta($id)
    {
        if (session()->get('id_rol') != 1) {
            return redirect()->to('/usuario_logueado');
        }

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