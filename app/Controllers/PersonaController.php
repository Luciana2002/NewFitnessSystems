<?php

namespace App\Controllers;

use App\Models\PersonaModel;
use App\Models\UsuarioModel;
use App\Models\RolModel;

class PersonaController extends BaseController
{
    private function validarAdmin()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        if (session()->get('id_rol') != 1) {
            return redirect()->to('/usuario_logueado');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->validarAdmin()) {
            return $redirect;
        }

        $personaModel = new PersonaModel();

        $data['personas'] = $personaModel->getPersonasAll();

        return view('front/header')
             . view('front/navbar')
             . view('personas/lista_personas', $data)
             . view('front/footer');
    }

    public function clientes()
    {
        if ($redirect = $this->validarAdmin()) {
            return $redirect;
        }

        $personaModel = new PersonaModel();

        $data['clientes'] = $personaModel->getClientes();

        return view('front/header')
             . view('front/navbar')
             . view('personas/lista_personas', $data)
             . view('front/footer');
    }

    public function editar($id)
    {
        if ($redirect = $this->validarAdmin()) {
            return $redirect;
        }

        $personaModel = new PersonaModel();
        $rolModel = new RolModel();

        $data['persona'] = $personaModel->find($id);
        $data['roles'] = $rolModel->findAll();

        if (!$data['persona']) {
            session()->setFlashdata('error', 'Persona no encontrada');
            return redirect()->to('/personas');
        }

        return view('front/header')
             . view('front/navbar')
             . view('personas/editar_persona', $data)
             . view('front/footer');
    }

    public function modificar($id)
    {
        if ($redirect = $this->validarAdmin()) {
            return $redirect;
        }

        $personaModel = new PersonaModel();

        $persona = $personaModel->find($id);

        if (!$persona) {
            session()->setFlashdata('error', 'Persona no encontrada');
            return redirect()->to('/personas');
        }

        $personaModel->update($id, [
            'nombre'   => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email'    => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'dni'      => $this->request->getPost('dni'),
            'id_rol'   => $this->request->getPost('id_rol')
        ]);

        session()->setFlashdata('success', 'Persona modificada correctamente');

        return redirect()->to('/personas');
    }

    public function baja($id)
    {
        if ($redirect = $this->validarAdmin()) {
            return $redirect;
        }

        $personaModel = new PersonaModel();

        $persona = $personaModel->find($id);

        if (!$persona) {
            session()->setFlashdata('error', 'Persona no encontrada');
            return redirect()->to('/personas');
        }

        if ($id == session()->get('id_persona')) {
            session()->setFlashdata('error', 'No podés darte de baja a vos mismo');
            return redirect()->to('/personas');
        }

        $personaModel->update($id, [
            'baja' => 'S'
        ]);

        session()->setFlashdata('success', 'Persona dada de baja correctamente');

        return redirect()->to('/personas');
    }

    public function alta($id)
    {
        if ($redirect = $this->validarAdmin()) {
            return $redirect;
        }

        $personaModel = new PersonaModel();

        $persona = $personaModel->find($id);

        if (!$persona) {
            session()->setFlashdata('error', 'Persona no encontrada');
            return redirect()->to('/personas');
        }

        $personaModel->update($id, [
            'baja' => 'N'
        ]);

        session()->setFlashdata('success', 'Persona activada correctamente');

        return redirect()->to('/personas');
    }
}