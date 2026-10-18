<?php

namespace App\Controllers;

use App\Models\GastoModel;

class GastoController extends BaseController
{
    private function validarAdmin()
    {
        if (session()->get('id_rol') != 1) {
            return redirect()->to('/usuario_logueado');
        }

        return null;
    }

    public function guardar()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $fecha     = trim((string) $this->request->getPost('fecha'));
        $categoria = trim((string) $this->request->getPost('categoria'));
        $monto     = $this->request->getPost('monto');
        $detalle   = trim((string) $this->request->getPost('detalle'));
        $mes       = trim((string) $this->request->getPost('mes'));

        $volver = '/suscripciones/balance' . ($mes !== '' ? '?mes=' . rawurlencode($mes) : '');

        if ($fecha === '' || $categoria === '' || !is_numeric($monto) || (float) $monto <= 0) {
            session()->setFlashdata('error', 'Completá fecha, categoría y un monto válido.');
            return redirect()->to($volver);
        }

        $gastoModel = new GastoModel();

        $gastoModel->insert([
            'fecha'      => $fecha,
            'categoria'  => $categoria,
            'monto'      => (float) $monto,
            'detalle'    => $detalle !== '' ? $detalle : null,
            'id_usuario' => session()->get('id_usuario'),
            'baja'       => 'N',
        ]);

        $idGasto = $gastoModel->getInsertID();

        nfs_log('ALTA', 'Gasto', $idGasto, sprintf(
            '%s por $%s%s (%s)',
            $categoria,
            number_format((float) $monto, 0, ',', '.'),
            $detalle !== '' ? ' - ' . $detalle : '',
            $fecha
        ));

        session()->setFlashdata('success', 'Gasto registrado correctamente.');
        return redirect()->to($volver);
    }

    public function eliminar($id = null)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $id  = (int) $id;
        $mes = trim((string) $this->request->getGet('mes'));

        $volver = '/suscripciones/balance' . ($mes !== '' ? '?mes=' . rawurlencode($mes) : '');

        if ($id > 0) {
            $gastoModel = new GastoModel();
            $gasto = $gastoModel->find($id);

            if ($gasto) {
                $gastoModel->update($id, ['baja' => 'S']);

                nfs_log('BAJA', 'Gasto', $id, sprintf(
                    '%s por $%s (%s)',
                    $gasto['categoria'],
                    number_format((float) $gasto['monto'], 0, ',', '.'),
                    $gasto['fecha']
                ));
            }
        }

        session()->setFlashdata('success', 'Gasto eliminado.');
        return redirect()->to($volver);
    }
}
