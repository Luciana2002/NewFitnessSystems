<?php

namespace App\Controllers;

use App\Models\AsistenciaModel;
use App\Models\ClienteModel;
use App\Models\ConfiguracionModel;

class CheckinController extends BaseController
{
    private function validarAcceso()
    {
        if (! session()->get('logged_in') || ! in_array((int) session()->get('id_rol'), [1, 2])) {
            return redirect()->to('/usuario_logueado');
        }

        return null;
    }

    /**
     * Token público del cartel QR. Se crea una sola vez en Configuracion.
     */
    private function tokenQR(): string
    {
        $configuracion = new ConfiguracionModel();
        $token = trim((string) $configuracion->getValor('qr_asistencia_token', ''));

        if ($token === '') {
            $token = bin2hex(random_bytes(24));
            $configuracion->setValor('qr_asistencia_token', $token);
        }

        return $token;
    }

    public function index()
    {
        $validacion = $this->validarAcceso();
        if ($validacion) return $validacion;

        $asistenciaModel = new AsistenciaModel();
        $token = $this->tokenQR();

        $data['asistenciasHoy'] = $asistenciaModel->getAsistenciasDelDia(date('Y-m-d'));
        $data['qrToken']        = $token;
        $data['qrUrl']          = base_url('asistencia/marcar?token=' . urlencode($token));

        return view('front/header')
             . view('front/navbar')
             . view('gimnasio/checkin', $data)
             . view('front/footer');
    }

    /**
     * Check-in realizado por el propio socio al escanear el QR del gimnasio.
     */
    public function marcar()
    {
        if (! session()->get('logged_in') || (int) session()->get('id_rol') !== 3) {
            session()->set('redirect_after_login', (string) current_url());

            return redirect()->to('/login');
        }

        $tokenRecibido = trim((string) $this->request->getGet('token'));

        if ($tokenRecibido === '' || ! hash_equals($this->tokenQR(), $tokenRecibido)) {
            return view('cliente/asistencia_resultado', [
                'estado'  => 'invalido',
                'mensaje' => 'El código QR es inválido o quedó desactualizado. Pedí uno nuevo en recepción.',
                'nombre'  => trim((string) session()->get('nombre') . ' ' . (string) session()->get('apellido')),
            ]);
        }

        $resultado = (new AsistenciaModel())->registrarIngreso((int) session()->get('id_persona'));

        return view('cliente/asistencia_resultado', $resultado);
    }

    /**
     * Invalida los carteles impresos y genera un nuevo token. Solo admin.
     */
    public function regenerarToken()
    {
        if (! session()->get('logged_in') || (int) session()->get('id_rol') !== 1) {
            return redirect()->to('/usuario_logueado');
        }

        (new ConfiguracionModel())->setValor('qr_asistencia_token', bin2hex(random_bytes(24)));

        return redirect()->to('/checkin')->with('success', 'Código QR regenerado. Imprimí y colocá el nuevo cartel.');
    }

    public function procesar()
    {
        if (! session()->get('logged_in') || ! in_array((int) session()->get('id_rol'), [1, 2])) {
            return $this->response->setStatusCode(403)->setJSON([
                'ok'      => false,
                'tipo'    => 'error',
                'mensaje' => 'No autorizado',
            ]);
        }

        $codigo = trim((string) $this->request->getPost('codigo'));

        if ($codigo === '' || ! preg_match('/^NFS-(\d+)-(.+)$/', $codigo, $coincidencias)) {
            return $this->response->setJSON([
                'ok'      => false,
                'tipo'    => 'error',
                'mensaje' => 'Código QR inválido',
            ]);
        }

        $idPersona = (int) $coincidencias[1];
        $dni       = trim($coincidencias[2]);

        $clienteModel = new ClienteModel();
        $persona = $clienteModel->find($idPersona);

        if (! $persona || (string) $persona['dni'] !== $dni) {
            return $this->response->setJSON([
                'ok'      => false,
                'tipo'    => 'error',
                'mensaje' => 'Cliente no encontrado',
            ]);
        }

        $nombreCompleto = $persona['nombre'] . ' ' . $persona['apellido'];

        if ((int) $persona['id_rol'] !== 3 || $persona['baja'] === 'S') {
            return $this->response->setJSON([
                'ok'      => false,
                'tipo'    => 'rechazado',
                'nombre'  => $nombreCompleto,
                'mensaje' => 'Cliente inactivo o no habilitado',
            ]);
        }

        helper('membresia');

        $membresia    = nfs_membresia_cliente($idPersona);
        $asistenciaModel = new AsistenciaModel();
        $hoy          = date('Y-m-d');
        $hora         = date('H:i:s');

        if (! $membresia['activo']) {
            $asistenciaModel->insert([
                'id_persona'  => $idPersona,
                'fecha'       => $hoy,
                'hora'        => $hora,
                'resultado'   => 'VENCIDA',
                'id_usuario'  => session()->get('id_usuario'),
                'observacion' => 'Cuota vencida',
            ]);

            $detalle = $membresia['vencimiento']
                ? 'Venció el ' . date('d/m/Y', strtotime($membresia['vencimiento']))
                : 'Sin suscripción activa';

            return $this->response->setJSON([
                'ok'       => false,
                'tipo'     => 'vencida',
                'nombre'   => $nombreCompleto,
                'mensaje'  => 'CUOTA VENCIDA — ' . $detalle,
                'vence'    => $membresia['vencimiento'],
                'dias'     => $membresia['dias'],
            ]);
        }

        $yaHoy = $asistenciaModel->yaRegistroHoy($idPersona, $hoy);

        $asistenciaModel->insert([
            'id_persona'  => $idPersona,
            'fecha'       => $hoy,
            'hora'        => $hora,
            'resultado'   => 'OK',
            'id_usuario'  => session()->get('id_usuario'),
            'observacion' => $yaHoy ? 'Segundo ingreso del día' : null,
        ]);

        return $this->response->setJSON([
            'ok'         => true,
            'tipo'       => 'ok',
            'nombre'     => $nombreCompleto,
            'mensaje'    => $yaHoy ? 'Ingreso registrado (ya tenía un ingreso hoy)' : 'Ingreso registrado',
            'vence'      => $membresia['vencimiento'],
            'dias'       => $membresia['dias'],
            'duplicado'  => (bool) $yaHoy,
        ]);
    }
}
