<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\SistemaModel;
use App\Models\HorarioModel;
use App\Models\PagoModel;

class PanelController extends BaseController
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

    /* ==================== PROFESORES ==================== */

    public function profesores()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $clienteModel = new ClienteModel();

        $data['profesores'] = $clienteModel->getProfesoresConInfo();

        return view('front/header')
             . view('front/navbar')
             . view('clientes/lista_profesores', $data)
             . view('front/footer');
    }

    /* ==================== SISTEMAS ==================== */

    public function sistemas()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $sistemaModel = new SistemaModel();

        $data['sistemas'] = $sistemaModel->getSistemasConPrecio(false);

        return view('front/header')
             . view('front/navbar')
             . view('back/administrador/lista_sistemas', $data)
             . view('front/footer');
    }

    public function actualizarSistema($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $nombre = trim($this->request->getPost('nombre') ?? '');
        $precio = $this->request->getPost('precio');

        if ($nombre === '' || !is_numeric($precio) || (float) $precio < 0) {
            session()->setFlashdata('error', 'Datos inválidos para el sistema');
            return redirect()->to('/sistemas');
        }

        $sistemaModel = new SistemaModel();

        $sistemaModel->update($id, [
            'nombre_sistema' => $nombre
        ]);

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

    public function bajaSistema($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $sistemaModel = new SistemaModel();
        $sistemaModel->update($id, ['baja' => 'S']);

        session()->setFlashdata('success', 'Sistema dado de baja');
        return redirect()->to('/sistemas');
    }

    public function altaSistema($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $sistemaModel = new SistemaModel();
        $sistemaModel->update($id, ['baja' => 'N']);

        session()->setFlashdata('success', 'Sistema activado');
        return redirect()->to('/sistemas');
    }

    /* ==================== HORARIOS ==================== */

    public function horarios()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $horarioModel = new HorarioModel();
        $sistemaModel = new SistemaModel();

        $data['horarios'] = $horarioModel->getHorariosAll();
        $data['sistemas'] = $sistemaModel->getSistemasConPrecio(false);

        return view('front/header')
             . view('front/navbar')
             . view('back/administrador/lista_horarios', $data)
             . view('front/footer');
    }

    public function actualizarHorario($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $horaInicio = $this->request->getPost('hora_inicio');
        $horaFin = $this->request->getPost('hora_fin');
        $diaSemana = $this->request->getPost('dia_semana');
        $idSistema = (int) $this->request->getPost('id_sistema');

        if ($horaInicio === null || $horaFin === null || $diaSemana === '' || !$idSistema) {
            session()->setFlashdata('error', 'Datos inválidos para el horario');
            return redirect()->to('/admin_horarios');
        }

        $horarioModel = new HorarioModel();

        $horarioModel->update($id, [
            'hora_inicio' => date('H:i:s', strtotime($horaInicio)),
            'hora_fin'    => date('H:i:s', strtotime($horaFin)),
            'dia_semana'  => $diaSemana,
            'id_sistema'  => $idSistema
        ]);

        session()->setFlashdata('success', 'Horario actualizado correctamente');
        return redirect()->to('/admin_horarios');
    }

    public function bajaHorario($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $horarioModel = new HorarioModel();
        $horarioModel->update($id, ['baja' => 'S']);

        session()->setFlashdata('success', 'Horario dado de baja');
        return redirect()->to('/admin_horarios');
    }

    public function altaHorario($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $horarioModel = new HorarioModel();
        $horarioModel->update($id, ['baja' => 'N']);

        session()->setFlashdata('success', 'Horario activado');
        return redirect()->to('/admin_horarios');
    }

    /* ==================== PAGOS ==================== */

    public function pagos()
    {
        $validacion = $this->validarAcceso();
        if ($validacion) return $validacion;

        $pagoModel = new PagoModel();

        $data['pagos'] = $pagoModel->getPagosAll();

        return view('front/header')
             . view('front/navbar')
             . view('back/pagos/lista_pagos', $data)
             . view('front/footer');
    }
}
