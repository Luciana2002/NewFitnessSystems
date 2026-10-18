<?php

namespace App\Controllers;

use App\Models\AsistenciaModel;
use App\Models\ClienteModel;
use App\Models\PlantillaMensajeModel;

class ComunicacionController extends BaseController
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
        return $this->vencimientos();
    }

    /* ==========================================================
       VENCIMIENTOS (vencen esta semana)
    ========================================================== */

    public function vencimientos()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        helper('email');

        $dias = (int) ($this->request->getGet('dias') ?: 7);
        $incluirVencidas = (bool) $this->request->getGet('incluir_vencidas');

        $data['clientes']        = $this->clientesVencimiento($dias, $incluirVencidas);
        $data['dias']            = $dias;
        $data['incluirVencidas'] = $incluirVencidas;
        $data['tabActiva']       = 'vencimientos';

        return view('front/header')
             . view('front/navbar')
             . view('comunicaciones/vencimientos', $data)
             . view('front/footer');
    }

    /**
     * Clientes cuya suscripcion activa vence dentro de los proximos $dias dias.
     * Si $incluirVencidas es true, tambien incluye las ya vencidas.
     *
     * @return array
     */
    private function clientesVencimiento(int $dias, bool $incluirVencidas): array
    {
        $clienteModel = new ClienteModel();
        $clientes = $clienteModel->getClientesConInfo();

        $hoy = new \DateTime(date('Y-m-d'));
        $resultado = [];

        foreach ($clientes as $cliente) {
            if ((int) $cliente['baja'] === 1) {
                continue;
            }

            $sistemasActivos = array_filter($cliente['sistemas'] ?? [], function ($s) {
                return ($s['estado_suscripcion'] ?? '') !== 'Cancelada'
                    && !empty($s['fecha_vencimiento']);
            });

            if (empty($sistemasActivos)) {
                continue;
            }

            $mejor = null;

            foreach ($sistemasActivos as $sistema) {
                $fv = $this->fechaYmd($sistema['fecha_vencimiento']);

                if ($mejor === null || $fv > $mejor['fecha']) {
                    $mejor = ['fecha' => $fv, 'sistema' => $sistema['nombre_sistema'] ?? ''];
                }
            }

            $venc = new \DateTime($mejor['fecha']);
            $diasRestantes = (int) $hoy->diff($venc)->format('%r%a');

            if ($diasRestantes > $dias) {
                continue;
            }

            if ($diasRestantes < 0 && !$incluirVencidas) {
                continue;
            }

            $resultado[] = [
                'id_persona' => $cliente['id_persona'],
                'nombre'     => $cliente['nombre'],
                'apellido'   => $cliente['apellido'],
                'telefono'   => $cliente['telefono'],
                'email'      => $cliente['email'],
                'vencimiento' => $mejor['fecha'],
                'sistema'    => $mejor['sistema'],
                'dias'       => $diasRestantes,
            ];
        }

        usort($resultado, fn ($a, $b) => $a['dias'] <=> $b['dias']);

        return $resultado;
    }

    /* ==========================================================
       ABANDONO (cuota al dia pero sin asistir hace X dias)
    ========================================================== */

    public function abandono()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        helper('membresia');
        helper('email');

        $dias = 15;

        $data['clientes']  = $this->clientesAbandono($dias);
        $data['dias']      = $dias;
        $data['tabActiva'] = 'abandono';

        return view('front/header')
             . view('front/navbar')
             . view('comunicaciones/abandono', $data)
             . view('front/footer');
    }

    /**
     * Clientes con cuota al dia que no asisten hace mas de $dias dias
     * (o que nunca registraron asistencia).
     *
     * @return array
     */
    private function clientesAbandono(int $dias): array
    {
        $clienteModel = new ClienteModel();
        $clientes = $clienteModel->getClientesConInfo();

        $asistenciaModel = new AsistenciaModel();
        $ultimas = $asistenciaModel->getUltimasAsistencias();

        $hoy = new \DateTime(date('Y-m-d'));
        $resultado = [];

        foreach ($clientes as $cliente) {
            if ((int) $cliente['baja'] === 1) {
                continue;
            }

            $membresia = nfs_membresia_cliente((int) $cliente['id_persona']);

            if (!$membresia['activo']) {
                continue;
            }

            $idPersona = (int) $cliente['id_persona'];
            $ultima = $ultimas[$idPersona] ?? null;

            if ($ultima === null) {
                $diasSinVenir = null;
            } else {
                $ultimaFecha = new \DateTime($ultima);
                $diasSinVenir = (int) $ultimaFecha->diff($hoy)->format('%r%a');
            }

            if ($diasSinVenir !== null && $diasSinVenir <= $dias) {
                continue;
            }

            $resultado[] = [
                'id_persona'   => $cliente['id_persona'],
                'nombre'       => $cliente['nombre'],
                'apellido'     => $cliente['apellido'],
                'telefono'     => $cliente['telefono'],
                'email'        => $cliente['email'],
                'ultima'       => $ultima,
                'dias'         => $diasSinVenir,
                'vencimiento'  => $membresia['vencimiento'],
            ];
        }

        usort($resultado, function ($a, $b) {
            $da = $a['dias'] === null ? PHP_INT_MAX : $a['dias'];
            $db = $b['dias'] === null ? PHP_INT_MAX : $b['dias'];
            return $db <=> $da;
        });

        return $resultado;
    }

    /* ==========================================================
       MENSAJERIA (plantillas + destinatarios)
    ========================================================== */

    public function mensajes()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        helper('membresia');
        helper('email');

        $plantillaModel = new PlantillaMensajeModel();
        $clienteModel   = new ClienteModel();

        $grupo = (string) ($this->request->getGet('grupo') ?: 'todos');
        $idCliente = (int) $this->request->getGet('id');

        $data['plantillas']    = $plantillaModel->getPlantillas();
        $data['clientes']      = $clienteModel->getClientesConInfo();
        $data['grupo']         = $grupo;
        $data['idCliente']     = $idCliente;
        $data['destinatarios'] = $this->destinatarios($grupo, $idCliente, $clienteModel);
        $data['emailOk']       = nfs_email_configurado();
        $data['tabActiva']     = 'mensajes';

        return view('front/header')
             . view('front/navbar')
             . view('comunicaciones/mensajes', $data)
             . view('front/footer');
    }

    /**
     * Lista de destinatarios segun el grupo elegido.
     *
     * @return array
     */
    private function destinatarios(string $grupo, int $idCliente, ClienteModel $clienteModel): array
    {
        switch ($grupo) {
            case 'vencen':
                $lista = $this->clientesVencimiento(7, true);
                break;

            case 'inactivos':
                $lista = $this->clientesAbandono(15);
                break;

            case 'individual':
                $lista = $idCliente > 0 ? $this->clienteIndividual($idCliente, $clienteModel) : [];
                break;

            case 'todos':
            default:
                $grupo = 'todos';
                $lista = [];
                foreach ($clienteModel->getClientesConInfo() as $cliente) {
                    if ((int) $cliente['baja'] === 1) {
                        continue;
                    }
                    $lista[] = $this->destinatarioBase($cliente);
                }
                break;
        }

        return $lista;
    }

    private function clienteIndividual(int $idPersona, ClienteModel $clienteModel): array
    {
        foreach ($clienteModel->getClientesConInfo() as $cliente) {
            if ((int) $cliente['id_persona'] === $idPersona) {
                $membresia = nfs_membresia_cliente($idPersona);
                $item = $this->destinatarioBase($cliente);
                $item['vencimiento'] = $membresia['vencimiento'];
                $item['dias'] = $membresia['dias'];
                return [$item];
            }
        }

        return [];
    }

    private function destinatarioBase(array $cliente): array
    {
        return [
            'id_persona'  => $cliente['id_persona'],
            'nombre'      => $cliente['nombre'],
            'apellido'    => $cliente['apellido'],
            'telefono'    => $cliente['telefono'],
            'email'       => $cliente['email'],
            'dias'        => null,
            'vencimiento' => null,
        ];
    }

    /* ==========================================================
       ENVIO DE EMAILS
    ========================================================== */

    /**
     * Envia un email a un solo cliente (desde las tablas de vencimientos/abandono).
     */
    public function enviar()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        helper('email');

        $idPersona = (int) $this->request->getPost('id_persona');
        $asunto    = trim((string) $this->request->getPost('asunto'));
        $mensaje   = trim((string) $this->request->getPost('mensaje'));
        $volver    = (string) $this->request->getPost('volver');

        $destino = $volver === 'abandono'
            ? 'comunicaciones/abandono'
            : 'comunicaciones/vencimientos';

        if ($asunto === '' || $mensaje === '') {
            return redirect()->to($destino)->with('error', 'El asunto y el mensaje son obligatorios.');
        }

        $clienteModel = new ClienteModel();
        $cliente = $clienteModel->getClienteDetalle($idPersona);
        $email = $cliente['email'] ?? '';

        if (empty($email)) {
            return redirect()->to($destino)->with('error', 'El cliente no tiene un correo cargado.');
        }

        $resultado = nfs_enviar_email($email, $asunto, $mensaje);

        if ($resultado['ok']) {
            return redirect()->to($destino)->with('success', 'Correo enviado a ' . $email . '.');
        }

        return redirect()->to($destino)->with('error', 'No se pudo enviar: ' . $resultado['error']);
    }

    /**
     * Envia el email a todos los destinatarios del grupo elegido,
     * personalizando los marcadores por cliente.
     */
    public function enviarMasivo()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        helper(['email', 'membresia']);

        $grupo     = (string) $this->request->getPost('grupo');
        $idCliente = (int) $this->request->getPost('id_cliente');
        $asunto    = trim((string) $this->request->getPost('asunto'));
        $mensaje   = trim((string) $this->request->getPost('mensaje'));

        $volver = 'comunicaciones/mensajes?grupo=' . ($grupo !== '' ? $grupo : 'todos');

        if ($asunto === '' || $mensaje === '') {
            return redirect()->to($volver)->with('error', 'El asunto y el mensaje son obligatorios.');
        }

        $clienteModel = new ClienteModel();
        $destinatarios = $this->destinatarios($grupo, $idCliente, $clienteModel);

        if (empty($destinatarios)) {
            return redirect()->to($volver)->with('error', 'No hay destinatarios para este grupo.');
        }

        $enviados = 0;
        $errores  = 0;
        $sinEmail = 0;
        $primerError = '';

        @set_time_limit(0);

        foreach ($destinatarios as $dest) {
            $email = trim((string) ($dest['email'] ?? ''));

            if ($email === '') {
                $sinEmail++;
                continue;
            }

            $texto = nfs_completar_plantilla($mensaje, [
                'nombre'      => $dest['nombre'] ?? '',
                'apellido'    => $dest['apellido'] ?? '',
                'vencimiento' => !empty($dest['vencimiento']) ? date('d/m/Y', strtotime($dest['vencimiento'])) : '',
                'dias'        => $dest['dias'] === null ? '' : (string) $dest['dias'],
            ]);

            $resultado = nfs_enviar_email($email, $asunto, $texto);

            if ($resultado['ok']) {
                $enviados++;
            } else {
                $errores++;
                if ($primerError === '') {
                    $primerError = $resultado['error'];
                }
            }
        }

        $resumen = 'Enviados: ' . $enviados . '.';
        if ($errores > 0)   $resumen .= ' Con errores: ' . $errores . '.';
        if ($sinEmail > 0)  $resumen .= ' Sin correo: ' . $sinEmail . '.';
        if ($errores > 0 && $primerError !== '') $resumen .= ' Detalle: ' . $primerError;

        $tipo = ($errores > 0 && $enviados === 0) ? 'error' : 'success';

        return redirect()->to($volver)->with($tipo, $resumen);
    }

    /* ==========================================================
       PLANTILLAS (guardar / eliminar)
    ========================================================== */

    public function guardarPlantilla()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $titulo  = trim((string) $this->request->getPost('titulo'));
        $mensaje = trim((string) $this->request->getPost('mensaje'));
        $categoria = trim((string) $this->request->getPost('categoria'));

        if ($titulo === '' || $mensaje === '') {
            return redirect()->to('/comunicaciones/mensajes')
                ->with('error', 'El titulo y el mensaje son obligatorios.');
        }

        $plantillaModel = new PlantillaMensajeModel();
        $plantillaModel->insert([
            'titulo'    => $titulo,
            'categoria' => $categoria !== '' ? $categoria : 'General',
            'mensaje'   => $mensaje,
            'baja'      => 0,
        ]);

        nfs_log('ALTA', 'Plantilla', $plantillaModel->getInsertID(), "Plantilla \"{$titulo}\" creada");

        return redirect()->to('/comunicaciones/mensajes')
            ->with('success', 'Plantilla guardada correctamente.');
    }

    public function eliminarPlantilla($id = null)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $id = (int) $id;

        if ($id > 0) {
            $plantillaModel = new PlantillaMensajeModel();
            $plantillaModel->update($id, ['baja' => 1]);

            nfs_log('BAJA', 'Plantilla', $id, 'Plantilla eliminada');
        }

        return redirect()->to('/comunicaciones/mensajes')
            ->with('success', 'Plantilla eliminada.');
    }

    private function fechaYmd($valor): string
    {
        if ($valor instanceof \DateTimeInterface) {
            return $valor->format('Y-m-d');
        }

        return (string) $valor;
    }
}
