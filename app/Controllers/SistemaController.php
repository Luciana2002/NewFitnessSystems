<?php

namespace App\Controllers;

use App\Models\SistemaModel;

class SistemaController extends BaseController
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

        $sistemaModel = new SistemaModel();

        $data['sistemas'] = $sistemaModel->getSistemasConPrecio(false);

        return view('front/header')
             . view('front/navbar')
             . view('gimnasio/lista_sistemas', $data)
             . view('front/footer');
    }

    public function guardar()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $nombre = trim($this->request->getPost('nombre') ?? '');
        $precio = $this->request->getPost('precio');
        $color  = trim($this->request->getPost('color') ?? '');

        if ($nombre === '' || !is_numeric($precio) || (float) $precio < 0) {
            session()->setFlashdata('error', 'Datos inválidos para el sistema');
            return redirect()->to('/sistemas');
        }

        $db = \Config\Database::connect();

        $db->transBegin();

        $idSistema = $db->table('Sistema')->insert([
            'nombre_sistema' => $nombre,
            'baja'           => 'N',
            'color'          => preg_match('/^#[0-9a-fA-F]{6}$/', $color) ? $color : '#0b8f70'
        ]);

        if (!$idSistema) {
            $db->transRollback();
            session()->setFlashdata('error', 'No se pudo registrar el sistema');
            return redirect()->to('/sistemas');
        }

        $idSistema = $db->insertID();

        $db->table('Mensualidad')->insert([
            'precio'         => (float) $precio,
            'fecha_vigencia' => date('Y-m-d'),
            'id_sistema'     => $idSistema
        ]);

        $db->transCommit();

        session()->setFlashdata('success', 'Sistema agregado correctamente');
        return redirect()->to('/sistemas');
    }

    public function actualizar($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $nombre = trim($this->request->getPost('nombre') ?? '');
        $precio = $this->request->getPost('precio');
        $color  = trim($this->request->getPost('color') ?? '');

        if ($nombre === '' || !is_numeric($precio) || (float) $precio < 0) {
            session()->setFlashdata('error', 'Datos inválidos para el sistema');
            return redirect()->to('/sistemas');
        }

        $sistemaModel = new SistemaModel();

        $datos = [
            'nombre_sistema' => $nombre
        ];

        if (preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            $datos['color'] = $color;
        }

        $sistemaModel->update($id, $datos);

        $db = \Config\Database::connect();

        $mensualidad = $db->query(
            "SELECT TOP 1 id_mensualidad
             FROM Mensualidad
             WHERE id_sistema = ?
             ORDER BY fecha_vigencia DESC",
            [$id]
        )->getRowArray();

        if ($mensualidad) {
            $db->table('Mensualidad')
                ->where('id_mensualidad', $mensualidad['id_mensualidad'])
                ->update(['precio' => (float) $precio]);
        }

        session()->setFlashdata('success', 'Sistema actualizado correctamente');
        return redirect()->to('/sistemas');
    }

    public function baja($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $sistemaModel = new SistemaModel();
        $sistemaModel->update($id, ['baja' => 'S']);

        session()->setFlashdata('success', 'Sistema dado de baja');
        return redirect()->to('/sistemas');
    }

    public function alta($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $sistemaModel = new SistemaModel();
        $sistemaModel->update($id, ['baja' => 'N']);

        session()->setFlashdata('success', 'Sistema activado');
        return redirect()->to('/sistemas');
    }

    public function cambiarColor($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $color = trim((string) $this->request->getPost('color'));

        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            return $this->response->setJSON(['ok' => false, 'msg' => 'Color inválido']);
        }

        $sistemaModel = new SistemaModel();
        $sistemaModel->update($id, ['color' => $color]);

        return $this->response->setJSON(['ok' => true]);
    }
}
