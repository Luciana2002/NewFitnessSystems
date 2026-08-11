<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\ClienteModel;
use App\Models\PagoModel;
use App\Models\SistemaModel;
use App\Models\DatosPersonalesModel;

class ClienteController extends BaseController
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

    public function usuarios()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        return redirect()->to('/clientes');
    }

    public function nuevoCliente()
    {
        $validacion = $this->validarAcceso();
        if ($validacion) return $validacion;

        $nombre = trim($this->request->getPost('nombre') ?? '');
        $apellido = trim($this->request->getPost('apellido') ?? '');
        $email = trim($this->request->getPost('email') ?? '');
        $telefono = trim($this->request->getPost('telefono') ?? '');
        $dni = trim($this->request->getPost('dni') ?? '');
        $idSistema = (int) $this->request->getPost('id_sistema');
        $idMedioPago = (int) $this->request->getPost('id_medio_pago');
        $monto = $this->request->getPost('monto');

        if ($nombre === '' || $apellido === '' || $telefono === '' || $dni === '' || !$idSistema || !$idMedioPago) {
            session()->setFlashdata('error', 'Completá todos los campos obligatorios');
            return redirect()->to('/clientes');
        }

        $personaModel = new DatosPersonalesModel();

        $existeDni = $personaModel->where('dni', $dni)->first();

        if ($existeDni) {
            session()->setFlashdata('error', 'Ya existe una persona registrada con ese DNI');
            return redirect()->to('/clientes');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $personaModel->insert([
            'nombre'   => $nombre,
            'apellido' => $apellido,
            'email'    => $email,
            'telefono' => $telefono,
            'dni'      => $dni,
            'id_rol'   => 3,
            'baja'     => 'N'
        ]);

        $idPersona = $personaModel->getInsertID();

        $hoy = date('Y-m-d');
        $vencimiento = date('Y-m-d', strtotime('+1 month', strtotime($hoy)));

        $db->table('Inscripcion')->insert([
            'fecha_inscripcion' => $hoy,
            'id_persona'        => $idPersona,
            'id_sistema'        => $idSistema
        ]);

        $idInscripcion = $db->insertID();

        $db->table('Suscripcion')->insert([
            'fecha_inicio'      => $hoy,
            'fecha_vencimiento' => $vencimiento,
            'id_estado'         => 1,
            'id_inscripcion'    => $idInscripcion
        ]);

        $idSuscripcion = $db->insertID();

        $mensualidad = $db->query(
            "SELECT TOP 1 id_mensualidad, precio
             FROM Mensualidad
             WHERE id_sistema = ?
             ORDER BY fecha_vigencia DESC",
            [$idSistema]
        )->getRowArray();

        if (empty($monto) || (float) $monto <= 0) {
            $monto = $mensualidad['precio'] ?? 0;
        }

        $db->table('Pago')->insert([
            'fecha_pago'    => $hoy,
            'monto'         => (float) $monto,
            'id_medio_pago' => $idMedioPago,
            'id_mensualidad'=> $mensualidad['id_mensualidad'] ?? null,
            'cobrado_por'   => session()->get('id_usuario'),
            'id_suscripcion'=> $idSuscripcion
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'No se pudo registrar el cliente');
            return redirect()->to('/clientes');
        }

        session()->setFlashdata('success', 'Cliente registrado con su primer pago');
        return redirect()->to('/clientes');
    }

    public function nuevoProfesor()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $nombre   = trim($this->request->getPost('nombre') ?? '');
        $apellido = trim($this->request->getPost('apellido') ?? '');
        $email    = trim($this->request->getPost('email') ?? '');
        $telefono = trim($this->request->getPost('telefono') ?? '');
        $dni      = trim($this->request->getPost('dni') ?? '');
        $usuario  = trim($this->request->getPost('usuario') ?? '');
        $pass     = $this->request->getPost('pass');

        if ($nombre === '' || $apellido === '' || $telefono === '' || $dni === '' || $usuario === '' || empty($pass)) {
            session()->setFlashdata('error', 'Completá todos los campos obligatorios');
            return redirect()->to('/profesores');
        }

        $personaModel = new DatosPersonalesModel();
        $usuarioModel = new UsuarioModel();

        if ($personaModel->where('dni', $dni)->first()) {
            session()->setFlashdata('error', 'Ya existe una persona registrada con ese DNI');
            return redirect()->to('/profesores');
        }

        if ($usuarioModel->where('nombre_usuario', $usuario)->first()) {
            session()->setFlashdata('error', 'Ese nombre de usuario ya está en uso');
            return redirect()->to('/profesores');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $personaModel->insert([
            'nombre'   => $nombre,
            'apellido' => $apellido,
            'email'    => $email,
            'telefono' => $telefono,
            'dni'      => $dni,
            'id_rol'   => 2,
            'baja'     => 'N'
        ]);

        $idPersona = $personaModel->getInsertID();

        $usuarioModel->insert([
            'nombre_usuario' => $usuario,
            'contraseña'     => password_hash($pass, PASSWORD_DEFAULT),
            'id_persona'     => $idPersona
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'No se pudo registrar el profesor');
            return redirect()->to('/profesores');
        }

        session()->setFlashdata('success', 'Profesor registrado correctamente');
        return redirect()->to('/profesores');
    }

    public function index()
    {
        $validacion = $this->validarAcceso();
        if ($validacion) return $validacion;

        $clienteModel = new ClienteModel();
        $sistemaModel = new SistemaModel();
        $pagoModel = new PagoModel();

        $clientes = $clienteModel->getClientesConInfo();

        usort($clientes, function ($a, $b) {
            $tsA = !empty($a['ultimo_pago']) ? strtotime((string) $a['ultimo_pago']) : 0;
            $tsB = !empty($b['ultimo_pago']) ? strtotime((string) $b['ultimo_pago']) : 0;

            if ($tsA !== $tsB) {
                return $tsB <=> $tsA;
            }

            return strcmp($a['apellido'], $b['apellido'])
                ?: strcmp($a['nombre'], $b['nombre']);
        });

        $data['clientes'] = $clientes;
        $data['sistemasRegistro'] = $sistemaModel->getSistemasConPrecio();
        $data['mediosPago'] = $pagoModel->getMediosPago();

        return view('front/header')
             . view('front/navbar')
             . view('clientes/lista_clientes', $data)
             . view('front/footer');
    }

    public function reporte()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $clienteModel = new ClienteModel();

        $clientes = $clienteModel->getClientesConInfo();

        $total = count($clientes);
        $alDia = 0;
        $vencido = 0;
        $sinPagos = 0;
        $sinSuscripcion = 0;
        $sistemas = [];
        $vencidosPorSistema = [];

        foreach ($clientes as $cliente) {
            $sistemasCliente = $cliente['sistemas'] ?? [];
            $ultimoPago = $cliente['ultimo_pago'] ?? null;

            $sistemasActivos = array_filter($sistemasCliente, function ($s) {
                return ($s['estado_suscripcion'] ?? '') !== 'Cancelada';
            });

            foreach ($sistemasActivos as $sistema) {
                $nombreSistema = $sistema['nombre_sistema'] ?? 'Sin sistema';
                $sistemas[$nombreSistema] = ($sistemas[$nombreSistema] ?? 0) + 1;

                $tsVenc = !empty($sistema['fecha_vencimiento'])
                    ? strtotime((string) $sistema['fecha_vencimiento'])
                    : null;

                if ($tsVenc && $tsVenc < strtotime(date('Y-m-d'))) {
                    $vencidosPorSistema[$nombreSistema] = ($vencidosPorSistema[$nombreSistema] ?? 0) + 1;
                }
            }

            if (empty($sistemasActivos)) {
                $sinSuscripcion++;
            } elseif (empty($ultimoPago)) {
                $sinPagos++;
            } else {
                $hoyTs = strtotime(date('Y-m-d'));
                $vencidoCliente = false;

                foreach ($sistemasActivos as $sistema) {
                    $tsVenc = !empty($sistema['fecha_vencimiento'])
                        ? strtotime((string) $sistema['fecha_vencimiento'])
                        : null;

                    if ($tsVenc && $tsVenc < $hoyTs) {
                        $vencidoCliente = true;
                        break;
                    }
                }

                if ($vencidoCliente) {
                    $vencido++;
                } else {
                    $alDia++;
                }
            }
        }

        arsort($sistemas);

        // Recomendaciones de promos según la demanda
        $sistemaModel = new SistemaModel();
        $sistemasPrecios = [];

        foreach ($sistemaModel->getSistemasConPrecio(false) as $s) {
            $sistemasPrecios[$s['nombre_sistema']] = (float) ($s['precio'] ?? 0);
        }

        $recomendaciones = [];
        $topNombre = !empty($sistemas) ? array_key_first($sistemas) : null;

        if ($topNombre) {
            $recomendaciones[] = [
                'tipo'     => 'estrella',
                'sistema'  => $topNombre,
                'precio'   => $sistemasPrecios[$topNombre] ?? 0,
                'activos'  => $sistemas[$topNombre],
                'vencidos' => $vencidosPorSistema[$topNombre] ?? 0,
                'mensaje'  => "Es el sistema con más suscripciones activas, el fuerte del negocio. Aprovechalo para fidelizar (programa de beneficios, descuento por referidos) antes que para subir el precio."
            ];
        }

        foreach ($sistemas as $nombre => $activos) {
            if ($nombre === $topNombre) continue;

            $vencidos = $vencidosPorSistema[$nombre] ?? 0;
            $precio   = $sistemasPrecios[$nombre] ?? 0;
            $ratio    = $activos > 0 ? $vencidos / $activos : 0;

            if ($ratio >= 0.4) {
                $recomendaciones[] = [
                    'tipo'     => 'retencion',
                    'sistema'  => $nombre,
                    'precio'   => $precio,
                    'activos'  => $activos,
                    'vencidos' => $vencidos,
                    'mensaje'  => "Alta proporción de cuotas vencidas ({$vencidos} de {$activos}). Ofrecé una promo de recuperación: 2x1 en la cuota del mes o 25% de descuento por 30 días para recuperar clientes."
                ];
            } elseif ($ratio <= 0.15 && $activos >= 5) {
                $recomendaciones[] = [
                    'tipo'     => 'subir-precio',
                    'sistema'  => $nombre,
                    'precio'   => $precio,
                    'activos'  => $activos,
                    'vencidos' => $vencidos,
                    'mensaje'  => "Alta demanda y cuotas al día: hay margen para subir el precio de $" . number_format($precio, 0, ',', '.') . " sin perder clientes."
                ];
            } elseif ($activos < 3) {
                $recomendaciones[] = [
                    'tipo'     => 'captacion',
                    'sistema'  => $nombre,
                    'precio'   => $precio,
                    'activos'  => $activos,
                    'vencidos' => $vencidos,
                    'mensaje'  => "Poca demanda ({$activos} clientes). Lanzá una promo de captación: primer mes con 50% de descuento o una semana de prueba gratis."
                ];
            }

            if (count($recomendaciones) >= 6) break;
        }

        $data['total'] = $total;
        $data['alDia'] = $alDia;
        $data['vencido'] = $vencido;
        $data['sinPagos'] = $sinPagos;
        $data['sinSuscripcion'] = $sinSuscripcion;
        $data['sistemas'] = $sistemas;
        $data['recomendaciones'] = $recomendaciones;

        return view('front/header')
             . view('front/navbar')
             . view('administrador/reporte', $data)
             . view('front/footer');
    }

    public function detalle($id)
    {
        $validacion = $this->validarAcceso();
        if ($validacion) return $validacion;

        $clienteModel = new ClienteModel();
        $pagoModel = new PagoModel();

        $data['cliente'] = $clienteModel->getClienteDetalle($id);

        if (!$data['cliente']) {
            session()->setFlashdata('error', 'Cliente no encontrado');
            return redirect()->to('/clientes');
        }

        $esProfesor = (int) $data['cliente']['id_rol'] === 2;
        $data['esProfesor'] = $esProfesor;

        if ($esProfesor) {
            $data['suscripciones'] = [];
            $data['pagos'] = [];
        } else {
            $data['suscripciones'] = $clienteModel->getSuscripcionesCliente($id);
            $data['pagos'] = $pagoModel->getPagosCliente($id);
        }

        return view('front/header')
             . view('front/navbar')
             . view('clientes/detalle_cliente', $data)
             . view('front/footer');
    }

    public function editarUsuario($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $usuarioModel = new UsuarioModel();

        $data['usuario'] = $usuarioModel->getUsuarioCompleto($id);

        return view('front/header')
             . view('front/navbar')
             . view('usuario/editar_usuario', $data)
             . view('front/footer');
    }

    public function modificarUsuario($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $usuarioModel = new UsuarioModel();
        $personaModel = new DatosPersonalesModel();

        $usuario = $usuarioModel->find($id);

        if (!$usuario) {
            session()->setFlashdata('error', 'Usuario no encontrado');
            return redirect()->to('/usuarios');
        }

        $personaModel->update($usuario['id_persona'], [
            'nombre'   => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email'    => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'dni'      => $this->request->getPost('dni'),
            'id_rol'   => $this->request->getPost('id_rol')
        ]);

        $datosUsuario = [
            'nombre_usuario' => $this->request->getPost('nombre_usuario')
        ];

        $pass = $this->request->getPost('pass');

        if (!empty($pass)) {
            $datosUsuario['contraseña'] = password_hash($pass, PASSWORD_DEFAULT);
        }

        $usuarioModel->update($id, $datosUsuario);

        session()->setFlashdata('success', 'Usuario modificado correctamente');
        return redirect()->to('/usuarios');
    }

    public function editar($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $personaModel = new DatosPersonalesModel();
        $usuarioModel = new UsuarioModel();
        $clienteModel = new ClienteModel();
        $sistemaModel = new SistemaModel();

        $data['cliente'] = $personaModel->find($id);

        if (!$data['cliente']) {
            session()->setFlashdata('error', 'Persona no encontrada');
            return redirect()->to('/clientes');
        }

        $data['usuario'] = $usuarioModel->getUsuarioPorPersona($id);
        $data['sistemas'] = $clienteModel->getSuscripcionesCliente($id);
        $data['sistemasDisponibles'] = $sistemaModel->getSistemasConPrecio();

        return view('front/header')
             . view('front/navbar')
             . view('clientes/editar_clientes', $data)
             . view('front/footer');
    }

    public function actualizar($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $personaModel = new DatosPersonalesModel();

        $datosPersona = [
            'nombre'   => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email'    => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'dni'      => $this->request->getPost('dni'),
            'baja'     => $this->request->getPost('persona_activa') ? 'N' : 'S',
        ];

        if (session()->get('id_rol') == 1) {
            $datosPersona['id_rol'] = $this->request->getPost('id_rol');
        }

        $personaModel->update($id, $datosPersona);

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->getUsuarioPorPersona($id);

        if ($usuario) {
            $datosUsuario = [
                'nombre_usuario' => $this->request->getPost('nombre_usuario')
            ];

            $pass = $this->request->getPost('pass');

            if (!empty($pass)) {
                $datosUsuario['contraseña'] = password_hash($pass, PASSWORD_DEFAULT);
            }

            $usuarioModel->update($usuario['id_usuario'], $datosUsuario);
        }

        $db = \Config\Database::connect();

        $inscripciones = $db->table('Inscripcion')
            ->where('id_persona', $id)
            ->get()->getResultArray();

        $sistemasActivos = $this->request->getPost('sistema_activo') ?? [];

        if (!is_array($sistemasActivos)) {
            $sistemasActivos = [];
        }

        foreach ($inscripciones as $inscripcion) {
            $idInscripcion = $inscripcion['id_inscripcion'];

            $suscripcion = $db->table('Suscripcion')
                ->where('id_inscripcion', $idInscripcion)
                ->orderBy('fecha_inicio', 'DESC')
                ->get()->getRowArray();

            if (!$suscripcion) {
                continue;
            }

            $estadoActual = (int) $suscripcion['id_estado'];
            $estaMarcado = in_array((string) $idInscripcion, $sistemasActivos, true);

            if ($estaMarcado && $estadoActual == 3) {
                $db->table('Suscripcion')
                    ->where('id_suscripcion', $suscripcion['id_suscripcion'])
                    ->update([
                        'id_estado'         => 1,
                        'fecha_vencimiento' => date('Y-m-d', strtotime('+1 month'))
                    ]);
            } elseif (!$estaMarcado && $estadoActual != 3) {
                $db->table('Suscripcion')
                    ->where('id_suscripcion', $suscripcion['id_suscripcion'])
                    ->update(['id_estado' => 3]);
            }
        }

        $agregarSistema = (int) $this->request->getPost('agregar_sistema');

        if ($agregarSistema) {
            $sistemaModel = new SistemaModel();
            $sistemaValido = $sistemaModel->where('id_sistema', $agregarSistema)
                ->where('baja', 'N')
                ->first();

            $yaInscripto = $db->table('Inscripcion')
                ->where('id_persona', $id)
                ->where('id_sistema', $agregarSistema)
                ->get()->getRow();

            if ($sistemaValido && !$yaInscripto) {
                $hoy = date('Y-m-d');
                $vencimiento = date('Y-m-d', strtotime('+1 month', strtotime($hoy)));

                $db->table('Inscripcion')->insert([
                    'fecha_inscripcion' => $hoy,
                    'id_persona'        => $id,
                    'id_sistema'        => $agregarSistema
                ]);

                $idInscripcion = $db->insertID();

                $db->table('Suscripcion')->insert([
                    'fecha_inicio'      => $hoy,
                    'fecha_vencimiento' => $vencimiento,
                    'id_estado'         => 1,
                    'id_inscripcion'    => $idInscripcion
                ]);
            }
        }

        session()->setFlashdata('success', 'Cliente actualizado correctamente');
        return redirect()->to('/clientes');
    }

    public function editarProfesor($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $personaModel = new DatosPersonalesModel();
        $usuarioModel = new UsuarioModel();

        $data['cliente'] = $personaModel->find($id);

        if (!$data['cliente']) {
            session()->setFlashdata('error', 'Profesor no encontrado');
            return redirect()->to('/profesores');
        }

        $data['usuario'] = $usuarioModel->getUsuarioPorPersona($id);

        return view('front/header')
             . view('front/navbar')
             . view('administrador/editar_profesor', $data)
             . view('front/footer');
    }

    public function actualizarProfesor($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $personaModel = new DatosPersonalesModel();

        $datosPersona = [
            'nombre'   => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email'    => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'dni'      => $this->request->getPost('dni'),
            'baja'     => $this->request->getPost('persona_activa') ? 'N' : 'S',
        ];

        if (session()->get('id_rol') == 1) {
            $datosPersona['id_rol'] = $this->request->getPost('id_rol');
        }

        $personaModel->update($id, $datosPersona);

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->getUsuarioPorPersona($id);

        if ($usuario) {
            $datosUsuario = [
                'nombre_usuario' => $this->request->getPost('nombre_usuario')
            ];

            $pass = $this->request->getPost('pass');

            if (!empty($pass)) {
                $datosUsuario['contraseña'] = password_hash($pass, PASSWORD_DEFAULT);
            }

            $usuarioModel->update($usuario['id_usuario'], $datosUsuario);
        }

        session()->setFlashdata('success', 'Profesor actualizado correctamente');
        return redirect()->to('/profesores');
    }

    public function baja($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $personaModel = new DatosPersonalesModel();

        $personaModel->update($id, [
            'baja' => 'S'
        ]);

        session()->setFlashdata('success', 'Cliente dado de baja correctamente');
        return redirect()->to('/clientes');
    }

    public function alta($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $personaModel = new DatosPersonalesModel();

        $personaModel->update($id, [
            'baja' => 'N'
        ]);

        session()->setFlashdata('success', 'Cliente activado correctamente');
        return redirect()->to('/clientes');
    }

    public function bajaUsuario($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        if ($id == session()->get('id_usuario')) {
            session()->setFlashdata('error', 'No podés darte de baja a vos mismo');
            return redirect()->to('/usuarios');
        }

        $usuarioModel = new UsuarioModel();
        $personaModel = new DatosPersonalesModel();

        $usuario = $usuarioModel->find($id);

        if (!$usuario) {
            session()->setFlashdata('error', 'Usuario no encontrado');
            return redirect()->to('/usuarios');
        }

        $personaModel->update($usuario['id_persona'], [
            'baja' => 'S'
        ]);

        session()->setFlashdata('success', 'Usuario dado de baja correctamente');
        return redirect()->to('/usuarios');
    }

    public function altaUsuario($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $usuarioModel = new UsuarioModel();
        $personaModel = new DatosPersonalesModel();

        $usuario = $usuarioModel->find($id);

        if (!$usuario) {
            session()->setFlashdata('error', 'Usuario no encontrado');
            return redirect()->to('/usuarios');
        }

        $personaModel->update($usuario['id_persona'], [
            'baja' => 'N'
        ]);

        session()->setFlashdata('success', 'Usuario activado correctamente');
        return redirect()->to('/usuarios');
    }
}