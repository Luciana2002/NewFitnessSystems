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

        $horaInicio = $this->request->getPost('hora_inicio');
        $horaFin    = $this->request->getPost('hora_fin');
        $diaSemana  = trim($this->request->getPost('dia_semana') ?? '');
        $idSistema  = (int) $this->request->getPost('id_sistema');

        $diasValidos = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];

        if ($horaInicio === null || $horaFin === null || !in_array($diaSemana, $diasValidos, true) || !$idSistema) {
            session()->setFlashdata('error', 'Datos inválidos para el horario');
            return redirect()->to('/admin_horarios');
        }

        $sistemaModel = new SistemaModel();

        if (!$sistemaModel->find($idSistema)) {
            session()->setFlashdata('error', 'El sistema seleccionado no existe');
            return redirect()->to('/admin_horarios');
        }

        $horarioModel = new HorarioModel();

        $horarioModel->insert([
            'hora_inicio' => date('H:i:s', strtotime($horaInicio)),
            'hora_fin'    => date('H:i:s', strtotime($horaFin)),
            'dia_semana'  => $diaSemana,
            'id_sistema'  => $idSistema,
            'baja'        => 'N'
        ]);

        session()->setFlashdata('success', 'Horario agregado correctamente');
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
