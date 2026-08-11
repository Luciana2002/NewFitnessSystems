<?php

namespace App\Controllers;

use App\Models\HorarioModel;
use App\Models\SistemaModel;

class Home extends BaseController
{
    public function index(): string
    {
        return view('front/header')
             . view('front/navbar')
             . view('front/home')
             . view('front/footer');
    }

    public function horarios(): string
    {
        $horarioModel = new HorarioModel();

        $data['horarios'] = $horarioModel->getHorariosActivos();

        return view('front/header')
             . view('front/navbar')
             . view('front/horarios', $data)
             . view('front/footer');
    }

    public function precios(): string
    {
        $sistemaModel = new SistemaModel();

        $data['precios'] = $sistemaModel->getSistemasConPrecio(true);

        return view('front/header')
             . view('front/navbar')
             . view('front/precios', $data)
             . view('front/footer');
    }

    public function nosotros(): string
    {
        return view('front/header')
             . view('front/navbar')
             . view('front/nosotros')
             . view('front/footer');
    }

    public function contacto(): string
    {
        return view('front/header')
             . view('front/navbar')
             . view('front/contacto')
             . view('front/footer');
    }
}