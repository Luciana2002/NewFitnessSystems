<?php

namespace App\Controllers;

use App\Models\HorarioModel;
use App\Models\SistemaModel;

class HorarioController extends BaseController
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
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $horarioModel = new HorarioModel();
        $sistemaModel = new SistemaModel();

        $data['horarios'] = $horarioModel->getHorariosAll();
        $data['sistemas'] = $sistemaModel->getSistemasConPrecio(false);
        $data['sistemasActivos'] = $sistemaModel->getSistemasConPrecio(true);

        return view('front/header')
             . view('front/navbar')
             . view('gimnasio/lista_horarios', $data)
             . view('front/footer');
    }

    public function guardar()
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $diasValidos = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];

        $diasPost = $this->request->getPost('dias');
        $dias     = [];

        if (is_array($diasPost)) {
            foreach ($diasPost as $dia) {
                $dia = trim((string) $dia);

                if (in_array($dia, $diasValidos, true)) {
                    $dias[$dia] = $dia;
                }
            }
        } else {
            $diaUnico = trim((string) $diasPost);

            if (in_array($diaUnico, $diasValidos, true)) {
                $dias[$diaUnico] = $diaUnico;
            }
        }

        // Compatibilidad con el selector simple que usaba el modal anterior.
        $diaLegacy = trim((string) $this->request->getPost('dia_semana'));

        if (empty($dias) && in_array($diaLegacy, $diasValidos, true)) {
            $dias[$diaLegacy] = $diaLegacy;
        }

        $dias = array_values(array_filter($diasValidos, static function ($dia) use ($dias) {
            return isset($dias[$dia]);
        }));

        $normalizarHora = static function ($valor): ?string {
            $valor = trim((string) $valor);

            if (! preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $valor)) {
                return null;
            }

            $timestamp = strtotime($valor);

            return $timestamp ? date('H:i:s', $timestamp) : null;
        };

        $horaInicio = $normalizarHora((string) $this->request->getPost('hora_inicio'));
        $horaFin    = $normalizarHora((string) $this->request->getPost('hora_fin'));
        $idSistema  = (int) $this->request->getPost('id_sistema');
        $cupoMax    = $this->request->getPost('cupo_max');
        $cupoMax    = ($cupoMax === null || $cupoMax === '') ? null : max(0, (int) $cupoMax);

        if ($horaInicio === null || $horaFin === null || empty($dias) || ! $idSistema) {
            session()->setFlashdata('error', 'Datos inválidos para el horario');
            return redirect()->to('/admin_horarios');
        }

        $sistemaModel = new SistemaModel();

        if (! $sistemaModel->find($idSistema)) {
            session()->setFlashdata('error', 'El sistema seleccionado no existe');
            return redirect()->to('/admin_horarios');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $horarioModel = new HorarioModel();

        foreach ($dias as $dia) {
            $horarioModel->insert([
                'hora_inicio' => $horaInicio,
                'hora_fin'    => $horaFin,
                'dia_semana'  => $dia,
                'id_sistema'  => $idSistema,
                'cupo_max'    => $cupoMax,
                'baja'        => 'N',
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'No se pudieron guardar los horarios');
            return redirect()->to('/admin_horarios');
        }

        $cantidad = count($dias);
        $mensaje  = $cantidad === 1
            ? 'Horario agregado correctamente'
            : 'Horarios agregados correctamente (' . $cantidad . ' días)';

        session()->setFlashdata('success', $mensaje);
        return redirect()->to('/admin_horarios');
    }

    public function actualizar($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $horaInicio = $this->request->getPost('hora_inicio');
        $horaFin = $this->request->getPost('hora_fin');
        $diaSemana = $this->request->getPost('dia_semana');
        $idSistema = (int) $this->request->getPost('id_sistema');
        $cupoMax   = $this->request->getPost('cupo_max');
        $cupoMax   = ($cupoMax === null || $cupoMax === '') ? null : max(0, (int) $cupoMax);

        if ($horaInicio === null || $horaFin === null || $diaSemana === '' || !$idSistema) {
            session()->setFlashdata('error', 'Datos inválidos para el horario');
            return redirect()->to('/admin_horarios');
        }

        $horarioModel = new HorarioModel();

        $horarioModel->update($id, [
            'hora_inicio' => date('H:i:s', strtotime($horaInicio)),
            'hora_fin'    => date('H:i:s', strtotime($horaFin)),
            'dia_semana'  => $diaSemana,
            'id_sistema'  => $idSistema,
            'cupo_max'    => $cupoMax
        ]);

        session()->setFlashdata('success', 'Horario actualizado correctamente');
        return redirect()->to('/admin_horarios');
    }

    public function baja($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $horarioModel = new HorarioModel();
        $horarioModel->update($id, ['baja' => 'S']);

        session()->setFlashdata('success', 'Horario dado de baja');
        return redirect()->to('/admin_horarios');
    }

    public function alta($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $horarioModel = new HorarioModel();
        $horarioModel->update($id, ['baja' => 'N']);

        session()->setFlashdata('success', 'Horario activado');
        return redirect()->to('/admin_horarios');
    }

    public function eliminar($id)
    {
        $validacion = $this->validarAdmin();
        if ($validacion) return $validacion;

        $horarioModel = new HorarioModel();
        $horarioModel->delete($id);

        session()->setFlashdata('success', 'Horario eliminado correctamente');
        return redirect()->to('/admin_horarios');
    }
}
