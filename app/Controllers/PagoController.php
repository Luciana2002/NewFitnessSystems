<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\PagoModel;

class PagoController extends BaseController
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

    public function index()
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

    public function nuevo()
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
