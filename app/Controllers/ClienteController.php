<?php

namespace App\Controllers;

use App\Models\ClienteModel;

class ClienteController extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $model = new ClienteModel();
        $data['clientes'] = $model->getClientes();

        return view('front/header')
            . view('front/navbar')
            . view('clientes/lista_clientes', $data)
            . view('front/footer');
    }

    public function editar($id)
    {
        $model = new ClienteModel();
        $data['cliente'] = $model->find($id);

        return view('front/header')
            . view('front/navbar')
            . view('clientes/editar_clientes', $data)
            . view('front/footer');
    }

    public function actualizar($id)
    {
        $model = new ClienteModel();

        $model->update($id, [
            'nombre'   => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email'    => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'dni'      => $this->request->getPost('dni')
        ]);

        session()->setFlashdata('success', 'Cliente actualizado correctamente');
        return redirect()->to('/clientes');
    }

    public function baja($id)
    {
        $model = new ClienteModel();
        $model->update($id, ['baja' => 'S']);

        session()->setFlashdata('success', 'Cliente dado de baja');
        return redirect()->to('/clientes');
    }

    public function alta($id)
    {
        $model = new ClienteModel();
        $model->update($id, ['baja' => 'N']);

        session()->setFlashdata('success', 'Cliente activado');
        return redirect()->to('/clientes');
    }
}