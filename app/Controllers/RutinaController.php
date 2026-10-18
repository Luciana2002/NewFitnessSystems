<?php

namespace App\Controllers;

use App\Models\RutinaModel;
use App\Models\ClienteModel;

class RutinaController extends BaseController
{
    private function validarAcceso()
    {
        if (! session()->get('logged_in') || ! in_array((int) session()->get('id_rol'), [1, 2])) {
            return redirect()->to('/usuario_logueado');
        }

        return null;
    }

    private function esAdmin(): bool
    {
        return (int) session()->get('id_rol') === 1;
    }

    private function clientesDisponibles(): array
    {
        $clienteModel = new ClienteModel();
        $clientes = $clienteModel->getClientesConInfo();

        return array_values(array_filter($clientes, function ($cliente) {
            return $cliente['baja'] !== 'S';
        }));
    }

    /**
     * Devuelve la rutina si el usuario puede verla/editarla, o null (y deja flashdata) si no.
     */
    private function cargarRutinaConAcceso($id)
    {
        $rutinaModel = new RutinaModel();
        $rutina = $rutinaModel->find($id);

        if (! $rutina) {
            session()->setFlashdata('error', 'Rutina no encontrada');
            return null;
        }

        if (! $this->esAdmin() && (int) $rutina['id_profesor'] !== (int) session()->get('id_persona')) {
            session()->setFlashdata('error', 'No tenés acceso a esa rutina');
            return null;
        }

        return $rutina;
    }

    /**
     * Días (1..7) válidos y únicos recibidos por POST.
     */
    private function diasDelPost(): array
    {
        $diasPost = $this->request->getPost('dias');

        return is_array($diasPost)
            ? array_values(array_unique(array_filter(array_map('intval', $diasPost), function ($d) {
                return $d >= 1 && $d <= 7;
            })))
            : [];
    }

    /**
     * Inserta los ejercicios enviados por POST para una rutina.
     */
    private function insertarEjercicios($db, int $idRutina): void
    {
        $nombres       = $this->request->getPost('ejercicio_nombre') ?? [];
        $series        = $this->request->getPost('ejercicio_series') ?? [];
        $repeticiones  = $this->request->getPost('ejercicio_repeticiones') ?? [];
        $pesos         = $this->request->getPost('ejercicio_peso') ?? [];
        $descansos     = $this->request->getPost('ejercicio_descanso') ?? [];
        $observaciones = $this->request->getPost('ejercicio_observacion') ?? [];

        if (! is_array($nombres)) {
            $nombres = [];
        }

        foreach ($nombres as $indice => $nombreEjercicio) {
            $nombreEjercicio = trim((string) $nombreEjercicio);

            if ($nombreEjercicio === '') {
                continue;
            }

            $db->table('RutinaEjercicio')->insert([
                'id_rutina'    => $idRutina,
                'nombre'       => $nombreEjercicio,
                'series'       => trim((string) ($series[$indice] ?? '')) ?: null,
                'repeticiones' => trim((string) ($repeticiones[$indice] ?? '')) ?: null,
                'peso'         => trim((string) ($pesos[$indice] ?? '')) ?: null,
                'descanso'     => trim((string) ($descansos[$indice] ?? '')) ?: null,
                'orden'        => $indice,
                'observacion'  => trim((string) ($observaciones[$indice] ?? '')) ?: null,
            ]);
        }
    }

    public function index()
    {
        $validacion = $this->validarAcceso();
        if ($validacion) return $validacion;

        helper('membresia');

        $rutinaModel = new RutinaModel();

        $rutinas = $this->esAdmin()
            ? $this->todasLasRutinas($rutinaModel)
            : $rutinaModel->getRutinasProfesor((int) session()->get('id_persona'));

        foreach ($rutinas as &$rutina) {
            $rutina['dias']        = $rutinaModel->getDias((int) $rutina['id_rutina']);
            $rutina['dias_texto']  = nfs_dias_etiquetas_texto($rutina['dias']);
            $rutina['asignada_a']  = (int) $rutina['para_todos'] === 1
                ? 'Todos los clientes'
                : trim(($rutina['cliente_apellido'] ?? '') . ', ' . ($rutina['cliente_nombre'] ?? ''), ', ');
        }
        unset($rutina);

        $data['rutinas'] = $rutinas;

        return view('front/header')
             . view('front/navbar')
             . view('administrador/rutinas', $data)
             . view('front/footer');
    }

    private function todasLasRutinas(RutinaModel $rutinaModel): array
    {
        return $rutinaModel
            ->select('
                Rutina.id_rutina,
                Rutina.nombre,
                Rutina.descripcion,
                Rutina.fecha,
                Rutina.para_todos,
                cli.nombre AS cliente_nombre,
                cli.apellido AS cliente_apellido,
                cli.dni AS cliente_dni,
                pro.nombre AS profesor_nombre,
                pro.apellido AS profesor_apellido,
                (SELECT COUNT(*) FROM RutinaEjercicio e WHERE e.id_rutina = Rutina.id_rutina) AS cantidad_ejercicios
            ')
            ->join('Persona cli', 'cli.id_persona = Rutina.id_cliente', 'left')
            ->join('Persona pro', 'pro.id_persona = Rutina.id_profesor', 'left')
            ->where('Rutina.baja', 'N')
            ->orderBy('Rutina.fecha', 'DESC')
            ->findAll();
    }

    public function nueva()
    {
        $validacion = $this->validarAcceso();
        if ($validacion) return $validacion;

        helper('membresia');

        $data['clientes'] = $this->clientesDisponibles();
        $data['dias']     = nfs_dias_semana_etiquetas();

        return view('front/header')
             . view('front/navbar')
             . view('administrador/rutina_form', $data)
             . view('front/footer');
    }

    public function editar($id)
    {
        $validacion = $this->validarAcceso();
        if ($validacion) return $validacion;

        helper('membresia');

        if (! $this->cargarRutinaConAcceso($id)) {
            return redirect()->to('/rutinas');
        }

        $rutinaModel = new RutinaModel();

        $data['rutina']     = $rutinaModel->getRutina($id);
        $data['clientes']   = $this->clientesDisponibles();
        $data['dias']       = nfs_dias_semana_etiquetas();
        $data['diasSel']    = $rutinaModel->getDias((int) $id);
        $data['ejercicios'] = $rutinaModel->getEjercicios((int) $id);
        $data['esEdicion']  = true;

        return view('front/header')
             . view('front/navbar')
             . view('administrador/rutina_form', $data)
             . view('front/footer');
    }

    public function guardar()
    {
        $validacion = $this->validarAcceso();
        if ($validacion) return $validacion;

        $nombre      = trim($this->request->getPost('nombre') ?? '');
        $descripcion = trim($this->request->getPost('descripcion') ?? '');
        $asignacion  = $this->request->getPost('asignacion') === 'cliente' ? 'cliente' : 'todos';
        $idCliente   = (int) $this->request->getPost('id_cliente');
        $dias        = $this->diasDelPost();

        if ($nombre === '') {
            session()->setFlashdata('error', 'Indicá el nombre de la rutina');
            return redirect()->to('/rutinas/nueva');
        }

        if (empty($dias)) {
            session()->setFlashdata('error', 'Seleccioná al menos un día de la semana');
            return redirect()->to('/rutinas/nueva');
        }

        $paraTodos = $asignacion === 'todos';

        if (! $paraTodos) {
            $clienteModel = new ClienteModel();
            $cliente = $clienteModel->find($idCliente);

            if (! $cliente || (int) $cliente['id_rol'] !== 3) {
                session()->setFlashdata('error', 'Cliente no encontrado');
                return redirect()->to('/rutinas/nueva');
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $rutinaModel = new RutinaModel();

        $rutinaModel->insert([
            'id_profesor' => (int) session()->get('id_persona'),
            'id_cliente'  => $paraTodos ? null : $idCliente,
            'para_todos'  => $paraTodos ? 1 : 0,
            'nombre'      => $nombre,
            'descripcion' => $descripcion ?: null,
            'fecha'       => date('Y-m-d'),
            'baja'        => 'N',
        ]);

        $idRutina = $rutinaModel->getInsertID();

        foreach ($dias as $dia) {
            $db->table('RutinaDia')->insert([
                'id_rutina'   => $idRutina,
                'dia_semana'  => $dia,
            ]);
        }

        $this->insertarEjercicios($db, $idRutina);

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'No se pudo guardar la rutina');
            return redirect()->to('/rutinas/nueva');
        }

        session()->setFlashdata('success', 'Rutina guardada correctamente');
        return redirect()->to('/rutinas/ver/' . $idRutina);
    }

    public function actualizar($id)
    {
        $validacion = $this->validarAcceso();
        if ($validacion) return $validacion;

        if (! $this->cargarRutinaConAcceso($id)) {
            return redirect()->to('/rutinas');
        }

        $nombre      = trim($this->request->getPost('nombre') ?? '');
        $descripcion = trim($this->request->getPost('descripcion') ?? '');
        $asignacion  = $this->request->getPost('asignacion') === 'cliente' ? 'cliente' : 'todos';
        $idCliente   = (int) $this->request->getPost('id_cliente');
        $dias        = $this->diasDelPost();

        if ($nombre === '') {
            session()->setFlashdata('error', 'Indicá el nombre de la rutina');
            return redirect()->to('/rutinas/editar/' . $id);
        }

        if (empty($dias)) {
            session()->setFlashdata('error', 'Seleccioná al menos un día de la semana');
            return redirect()->to('/rutinas/editar/' . $id);
        }

        $paraTodos = $asignacion === 'todos';

        if (! $paraTodos) {
            $clienteModel = new ClienteModel();
            $cliente = $clienteModel->find($idCliente);

            if (! $cliente || (int) $cliente['id_rol'] !== 3) {
                session()->setFlashdata('error', 'Cliente no encontrado');
                return redirect()->to('/rutinas/editar/' . $id);
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $rutinaModel = new RutinaModel();

        $rutinaModel->update($id, [
            'id_cliente'  => $paraTodos ? null : $idCliente,
            'para_todos'  => $paraTodos ? 1 : 0,
            'nombre'      => $nombre,
            'descripcion' => $descripcion ?: null,
        ]);

        $db->table('RutinaDia')->where('id_rutina', $id)->delete();
        foreach ($dias as $dia) {
            $db->table('RutinaDia')->insert([
                'id_rutina'   => $id,
                'dia_semana'  => $dia,
            ]);
        }

        $db->table('RutinaEjercicio')->where('id_rutina', $id)->delete();
        $this->insertarEjercicios($db, (int) $id);

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'No se pudo actualizar la rutina');
            return redirect()->to('/rutinas/editar/' . $id);
        }

        session()->setFlashdata('success', 'Rutina actualizada correctamente');
        return redirect()->to('/rutinas/ver/' . $id);
    }

    public function ver($id)
    {
        $validacion = $this->validarAcceso();
        if ($validacion) return $validacion;

        helper('membresia');

        $rutinaModel = new RutinaModel();
        $rutina = $rutinaModel->getRutina($id);

        if (! $rutina) {
            session()->setFlashdata('error', 'Rutina no encontrada');
            return redirect()->to('/rutinas');
        }

        if (! $this->esAdmin() && (int) $rutina['id_profesor'] !== (int) session()->get('id_persona')) {
            session()->setFlashdata('error', 'No tenés acceso a esa rutina');
            return redirect()->to('/rutinas');
        }

        $dias = $rutinaModel->getDias((int) $rutina['id_rutina']);

        $data['rutina']     = $rutina;
        $data['ejercicios'] = $rutinaModel->getEjercicios((int) $rutina['id_rutina']);
        $data['dias']       = $dias;
        $data['dias_texto'] = nfs_dias_etiquetas_texto($dias, false);

        return view('front/header')
             . view('front/navbar')
             . view('administrador/rutina_ver', $data)
             . view('front/footer');
    }

    public function eliminar($id)
    {
        $validacion = $this->validarAcceso();
        if ($validacion) return $validacion;

        $rutinaModel = new RutinaModel();
        $rutina = $rutinaModel->find($id);

        if (! $rutina) {
            session()->setFlashdata('error', 'Rutina no encontrada');
            return redirect()->to('/rutinas');
        }

        if (! $this->esAdmin() && (int) $rutina['id_profesor'] !== (int) session()->get('id_persona')) {
            session()->setFlashdata('error', 'No tenés acceso a esa rutina');
            return redirect()->to('/rutinas');
        }

        $rutinaModel->update($id, ['baja' => 'S']);

        session()->setFlashdata('success', 'Rutina eliminada');
        return redirect()->to('/rutinas');
    }
}
