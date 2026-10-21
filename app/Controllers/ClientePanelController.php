<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\PagoModel;
use App\Models\AsistenciaModel;
use App\Models\RutinaModel;

class ClientePanelController extends BaseController
{
    private function validarCliente()
    {
        if (! session()->get('logged_in') || session()->get('id_rol') != 3) {
            return redirect()->to('/login');
        }

        return null;
    }

    private function idPersona(): int
    {
        return (int) session()->get('id_persona');
    }

    public function suscripcion()
    {
        $validacion = $this->validarCliente();
        if ($validacion) return $validacion;

        helper('membresia');

        $idPersona = $this->idPersona();

        $clienteModel = new ClienteModel();
        $pagoModel    = new PagoModel();
        $asistenciaModel = new AsistenciaModel();

        $data['suscripciones'] = $clienteModel->getSuscripcionesCliente($idPersona);
        $data['pagos']         = $pagoModel->getPagosCliente($idPersona);
        $data['asistencias']   = $asistenciaModel->getAsistenciasCliente($idPersona);
        $data['membresia']     = nfs_membresia_cliente($idPersona);
        $data['idPersona']     = $idPersona;

        return view('front/header')
             . view('front/navbar')
             . view('cliente/panel_suscripcion', $data)
             . view('front/footer');
    }

    public function comprobante($idPago)
    {
        $validacion = $this->validarCliente();
        if ($validacion) return $validacion;

        $idPersona = $this->idPersona();

        $db = \Config\Database::connect();

        $pago = $db->query(
            'SELECT pg.id_pago, pg.fecha_pago, pg.monto,
                    mp.descripcion AS medio_pago,
                    s.nombre_sistema,
                    per.nombre, per.apellido, per.dni, per.email, per.telefono,
                    u.nombre_usuario AS cobrado_por,
                    su.fecha_vencimiento,
                    sus.descripcion AS estado_suscripcion
               FROM Pago pg
               INNER JOIN Suscripcion su ON su.id_suscripcion = pg.id_suscripcion
               INNER JOIN Inscripcion i ON i.id_inscripcion = su.id_inscripcion
               INNER JOIN Persona per ON per.id_persona = i.id_persona
               INNER JOIN Sistema s ON s.id_sistema = i.id_sistema
               LEFT JOIN Medio_pago mp ON mp.id_medio_pago = pg.id_medio_pago
               LEFT JOIN Usuario u ON u.id_usuario = pg.cobrado_por
               LEFT JOIN Estado_suscripcion sus ON sus.id_estado = su.id_estado
              WHERE pg.id_pago = ? AND i.id_persona = ?',
            [$idPago, $idPersona]
        )->getRowArray();

        if (! $pago) {
            session()->setFlashdata('error', 'Comprobante no encontrado');
            return redirect()->to('/cliente/suscripcion');
        }

        $data['pago']    = $pago;
        $data['nro']     = str_pad((string) $pago['id_pago'], 8, '0', STR_PAD_LEFT);
        $data['empresa'] = 'New Fitness Systems';

        return view('cliente/comprobante', $data);
    }

    public
}
