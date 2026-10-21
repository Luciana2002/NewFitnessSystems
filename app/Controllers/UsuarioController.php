<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\PersonaModel;
use App\Models\DatosPersonalesModel;
use App\Models\ClienteModel;
use App\Models\PanelModel;
use App\Models\PreferenciaUsuarioModel;
use CodeIgniter\HTTP\RedirectResponse;

class UsuarioController extends BaseController
{
    private function validarAdmin()
    {
        if (session()->get('id_rol') != 1) {
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

        nfs_log('EDICION', 'Usuario', $id, 'Usuario modificado: ' . trim($this->request->getPost('nombre') . ' ' . $this->request->getPost('apellido')));

        session()->setFlashdata('success', 'Usuario modificado correctamente');
        return redirect()->to('/usuarios');
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

        nfs_log('BAJA', 'Usuario', $id, 'Usuario dado de baja');

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

        nfs_log('ALTA', 'Usuario', $id, 'Usuario reactivado');

        session()->setFlashdata('success', 'Usuario activado correctamente');
        return redirect()->to('/usuarios');
    }

    public function login(): string
    {
        helper(['form', 'url']);

        return view('front/header')
             . view('front/navbar')
             . view('usuario/login')
             . view('front/footer');
    }

    public function auth()
    {
        $session = session();
        $model = new UsuarioModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('pass');

        $data = $model->getUsuarioPorEmail($email);

        if (!$data) {
            $session->setFlashdata('msg', 'No existe el correo o es incorrecto');
            return redirect()->to('/login');
        }

        if ($data['baja'] == 'S') {
            $session->setFlashdata('msg', 'El usuario se encuentra dado de baja');
            return redirect()->to('/login');
        }

        $algo = password_get_info($data['contraseña'])['algo'];

        if ($algo !== null) {
            $verifyPass = password_verify($password, $data['contraseña']);
        } else {
            $verifyPass = ($password === $data['contraseña']);
        }

        if (!$verifyPass) {
            $session->setFlashdata('msg', 'Contraseña incorrecta');
            return redirect()->to('/login');
        }

        $sessionData = [
            'id_usuario'     => $data['id_usuario'],
            'id_persona'     => $data['id_persona'],
            'nombre'         => $data['nombre'],
            'apellido'       => $data['apellido'],
            'email'          => $data['email'],
            'telefono'       => $data['telefono'],
            'dni'            => $data['dni'],
            'nombre_usuario' => $data['nombre_usuario'],
            'id_rol'         => $data['id_rol'],
            'rol'            => $data['rol'],
            'logged_in'      => true
        ];

        $session->set($sessionData);

        return redirect()->to('/usuario_logueado');
    }

    public function registro(): string
    {
        helper(['form', 'url']);

        return view('front/header')
             . view('front/navbar')
             . view('usuario/registro')
             . view('front/footer');
    }

    public function guardarRegistro()
    {
        helper(['form', 'url']);

        $validationRules = [
            'nombre'   => 'required|min_length[3]',
            'apellido' => 'required|min_length[3]|max_length[50]',
            'usuario'  => 'required|min_length[3]|is_unique[Usuario.nombre_usuario]',
            'email'    => 'required|min_length[4]|max_length[100]|valid_email|is_unique[Persona.email]',
            'telefono' => 'required',
            'dni'      => 'required|is_unique[Persona.dni]',
            'pass'     => 'required|min_length[4]|max_length[50]'
        ];

        if (!$this->validate($validationRules)) {
            return view('front/header')
                 . view('front/navbar')
                 . view('usuario/registro', [
                     'validation' => $this->validator
                 ])
                 . view('front/footer');
        }

        $personaModel = new PersonaModel();
        $usuarioModel = new UsuarioModel();

        $personaModel->insert([
            'nombre'   => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email'    => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'dni'      => $this->request->getPost('dni'),
            'id_rol'   => 3,
            'baja'     => 'N'
        ]);

        $idPersona = $personaModel->getInsertID();

        $usuarioModel->insert([
            'nombre_usuario' => $this->request->getPost('usuario'),
            'contraseña'     => password_hash($this->request->getPost('pass'), PASSWORD_DEFAULT),
            'id_persona'     => $idPersona
        ]);

        $idUsuarioNuevo = $usuarioModel->getInsertID();

        nfs_log('ALTA', 'Usuario', $idUsuarioNuevo, 'Registro de usuario: ' . trim($this->request->getPost('nombre') . ' ' . $this->request->getPost('apellido')) . ' (' . $this->request->getPost('usuario') . ')');

        session()->setFlashdata('success', 'Usuario registrado con éxito');
        return redirect()->to('/login');
    }

    public function usuarioLogueado(): string|RedirectResponse
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $panelModel = new PanelModel();
        $idRol      = (int) session()->get('id_rol');
        $idPersona  = (int) session()->get('id_persona');

        if ($idRol === 1) {
            $resumen = $panelModel->resumenAdmin();
        } elseif ($idRol === 2) {
            $resumen = $panelModel->resumenProfesor($idPersona);
        } else {
            $resumen = $panelModel->resumenCliente($idPersona);
        }

        return view('front/header')
             . view('front/navbar')
             . view('usuario/usuario_logueado', ['resumen' => $resumen])
             . view('front/footer');
    }

    public function perfil(): string|RedirectResponse
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        helper('form');

        $idPersona = (int) session()->get('id_persona');

        $persona = (new DatosPersonalesModel())->find($idPersona);

        if (!$persona) {
            return redirect()->to('/usuario_logueado');
        }

        $data = [
            'persona'  => $persona,
            'usuario'  => (new UsuarioModel())->getUsuarioPorPersona($idPersona),
            'sistemas' => [],
        ];

        if ((int) session()->get('id_rol') === 3) {
            helper('membresia');
            $data['membresia'] = nfs_membresia_cliente($idPersona);
            $data['sistemas']  = (new ClienteModel())->getSuscripcionesCliente($idPersona);
        }

        return view('front/header')
             . view('front/navbar')
             . view('usuario/perfil', $data)
             . view('front/footer');
    }

    public function configuracion(): string|RedirectResponse
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        helper('form');

        $idUsuario = (int) session()->get('id_usuario');

        return view('front/header')
             . view('front/navbar')
             . view('usuario/configuracion', [
                 'preferencias' => (new PreferenciaUsuarioModel())->obtener($idUsuario),
             ])
             . view('front/footer');
    }

    public function actualizarPerfil()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $idPersona = (int) session()->get('id_persona');
        $idUsuario = (int) session()->get('id_usuario');

        $rules = [
            'nombre'   => 'required|min_length[3]|max_length[50]',
            'apellido' => 'required|min_length[3]|max_length[50]',
            'email'    => "required|valid_email|max_length[100]|is_unique[Persona.email,id_persona,{$idPersona}]",
            'telefono' => 'required|max_length[20]',
            'dni'      => "required|max_length[20]|is_unique[Persona.dni,id_persona,{$idPersona}]",
        ];

        $mensajes = [
            'nombre' => [
                'required'   => 'El nombre es obligatorio.',
                'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede superar los 50 caracteres.',
            ],
            'apellido' => [
                'required'   => 'El apellido es obligatorio.',
                'min_length' => 'El apellido debe tener al menos 3 caracteres.',
                'max_length' => 'El apellido no puede superar los 50 caracteres.',
            ],
            'email' => [
                'required'    => 'El correo electrónico es obligatorio.',
                'valid_email' => 'Ingresá un correo electrónico válido.',
                'max_length'  => 'El correo no puede superar los 100 caracteres.',
                'is_unique'   => 'Ese correo electrónico ya está en uso.',
            ],
            'telefono' => [
                'required'   => 'El teléfono es obligatorio.',
                'max_length' => 'El teléfono no puede superar los 20 caracteres.',
            ],
            'dni' => [
                'required'   => 'El DNI es obligatorio.',
                'max_length' => 'El DNI no puede superar los 20 caracteres.',
                'is_unique'  => 'Ese DNI ya está registrado.',
            ],
        ];

        if (!$this->validate($rules, $mensajes)) {
            return redirect()->to('/perfil')
                ->withInput()
                ->with('error', implode(' ', $this->validator->getErrors()));
        }

        $datos = [
            'nombre'   => trim((string) $this->request->getPost('nombre')),
            'apellido' => trim((string) $this->request->getPost('apellido')),
            'email'    => trim((string) $this->request->getPost('email')),
            'telefono' => trim((string) $this->request->getPost('telefono')),
            'dni'      => trim((string) $this->request->getPost('dni')),
        ];

        (new DatosPersonalesModel())->update($idPersona, $datos);

        session()->set([
            'nombre'   => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'email'    => $datos['email'],
            'telefono' => $datos['telefono'],
            'dni'      => $datos['dni'],
        ]);

        nfs_log('EDICION', 'Usuario', $idUsuario, 'Actualizó sus datos personales');

        return redirect()->to('/perfil')
            ->with('success', 'Tus datos se actualizaron correctamente.');
    }

    public function actualizarPreferencias()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $idUsuario = (int) session()->get('id_usuario');

        $modelo = new PreferenciaUsuarioModel();

        $tema = (string) $this->request->getPost('tema');

        $datos = [
            'tema'        => in_array($tema, PreferenciaUsuarioModel::TEMAS, true) ? $tema : 'dark',
            'notif_email' => $this->request->getPost('notif_email') ? 1 : 0,
            'notif_dias'  => max(1, min(30, (int) $this->request->getPost('notif_dias'))),
        ];

        $modelo->guardar($idUsuario, $datos);

        nfs_log('EDICION', 'Usuario', $idUsuario, 'Actualizó sus preferencias');

        return redirect()->to('/configuracion')
            ->with('success', 'Tus preferencias se guardaron correctamente.');
    }

    public function cambiarPassword()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $idUsuario = (int) session()->get('id_usuario');

        $actual  = (string) $this->request->getPost('actual');
        $nueva   = (string) $this->request->getPost('nueva');
        $repetir = (string) $this->request->getPost('repetir');

        $usuario = (new UsuarioModel())->find($idUsuario);

        if (!$usuario) {
            return redirect()->to('/perfil')
                ->with('error', 'No se encontró el usuario.');
        }

        $hash = (string) $usuario['contraseña'];
        $algo = password_get_info($hash)['algo'];

        $correcta = $algo !== null
            ? password_verify($actual, $hash)
            : ($actual === $hash);

        if (!$correcta) {
            return redirect()->to('/perfil')
                ->with('error', 'La contraseña actual no es correcta.');
        }

        if (strlen($nueva) < 6) {
            return redirect()->to('/perfil')
                ->with('error', 'La nueva contraseña debe tener al menos 6 caracteres.');
        }

        if ($nueva !== $repetir) {
            return redirect()->to('/perfil')
                ->with('error', 'Las contraseñas nuevas no coinciden.');
        }

        (new UsuarioModel())->update($idUsuario, [
            'contraseña' => password_hash($nueva, PASSWORD_DEFAULT),
        ]);

        nfs_log('EDICION', 'Usuario', $idUsuario, 'Cambió su contraseña');

        return redirect()->to('/perfil')
            ->with('success', 'Tu contraseña se actualizó correctamente.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}