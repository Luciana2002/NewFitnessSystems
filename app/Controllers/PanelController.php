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
             . view('administrador/lista_profesores', $data)
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
             . view('gimnasio/lista_sistemas', $data)
             . view('front/footer');
    }

    public function actualizarSistema($id)
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
             . view('gimnasio/lista_horarios', $data)
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

    public function eliminarHorario($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $horarioModel = new HorarioModel();
        $horarioModel->delete($id);

        session()->setFlashdata('success', 'Horario eliminado correctamente');
        return redirect()->to('/admin_horarios');
    }

    public function cambiarColorSistema($id)
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

    /* ==================== PAGOS ==================== */

    public function pagos()
    {
        $validacion = $this->validarAcceso();
        if ($validacion) return $validacion;

        $pagoModel = new PagoModel();
        $clienteModel = new ClienteModel();

        $clientes = $clienteModel->getClientesConInfo();

        foreach ($clientes as &$cliente) {
            $cliente['sistemas'] = array_values(array_filter($cliente['sistemas'], function ($s) {
                return ($s['estado_suscripcion'] ?? '') !== 'Cancelada';
            }));
        }
        unset($cliente);

        $clientes = array_values(array_filter($clientes, function ($c) {
            return !empty($c['sistemas']);
        }));

        $data['pagos'] = $pagoModel->getPagosAll();
        $data['clientes'] = $clientes;
        $data['mediosPago'] = $pagoModel->getMediosPago();

        return view('front/header')
             . view('front/navbar')
             . view('administrador/lista_pagos', $data)
             . view('front/footer');
    }

    public function nuevoPago()
    {
        $validacion = $this->validarAcceso();
        if ($validacion) return $validacion;

        $idPersona = (int) $this->request->getPost('id_persona');
        $idSistema = (int) $this->request->getPost('id_sistema');
        $idMedioPago = (int) $this->request->getPost('id_medio_pago');
        $monto = $this->request->getPost('monto');
        $fechaPago = $this->request->getPost('fecha_pago');

        if (!$idPersona || !$idSistema || !$idMedioPago || empty($monto) || (float) $monto <= 0) {
            session()->setFlashdata('error', 'Completá todos los campos obligatorios');
            return redirect()->to('/pagos');
        }

        $db = \Config\Database::connect();

        $persona = $db->table('Persona')
            ->where('id_persona', $idPersona)
            ->get()->getRowArray();

        if (!$persona || (int) $persona['id_rol'] !== 3 || $persona['baja'] === 'S') {
            session()->setFlashdata('error', 'Cliente inválido');
            return redirect()->to('/pagos');
        }

        $inscripcion = $db->table('Inscripcion')
            ->where('id_persona', $idPersona)
            ->where('id_sistema', $idSistema)
            ->get()->getRowArray();

        if (!$inscripcion) {
            session()->setFlashdata('error', 'El cliente no está inscripto en ese sistema');
            return redirect()->to('/pagos');
        }

        $suscripcion = $db->table('Suscripcion')
            ->where('id_inscripcion', $inscripcion['id_inscripcion'])
            ->orderBy('fecha_inicio', 'DESC')
            ->get()->getRowArray();

        $fechaPago = $fechaPago ?: date('Y-m-d');
        $nuevoVencimiento = date('Y-m-d', strtotime('+1 month', strtotime($fechaPago)));

        if ($suscripcion) {
            $idSuscripcion = $suscripcion['id_suscripcion'];

            $db->table('Suscripcion')
                ->where('id_suscripcion', $idSuscripcion)
                ->update([
                    'id_estado'         => 1,
                    'fecha_inicio'      => $fechaPago,
                    'fecha_vencimiento' => $nuevoVencimiento
                ]);
        } else {
            $db->table('Suscripcion')->insert([
                'fecha_inicio'      => $fechaPago,
                'fecha_vencimiento' => $nuevoVencimiento,
                'id_estado'         => 1,
                'id_inscripcion'    => $inscripcion['id_inscripcion']
            ]);

            $idSuscripcion = $db->insertID();
        }

        $mensualidad = $db->query(
            "SELECT TOP 1 id_mensualidad, precio
             FROM Mensualidad
             WHERE id_sistema = ?
             ORDER BY fecha_vigencia DESC",
            [$idSistema]
        )->getRowArray();

        $db->table('Pago')->insert([
            'fecha_pago'     => $fechaPago,
            'monto'          => (float) $monto,
            'id_medio_pago'  => $idMedioPago,
            'id_mensualidad' => $mensualidad['id_mensualidad'] ?? null,
            'cobrado_por'    => session()->get('id_usuario'),
            'id_suscripcion' => $idSuscripcion
        ]);

        session()->setFlashdata('success', 'Pago registrado correctamente');
        return redirect()->to('/pagos');
    }
}
