<?php

namespace App\Controllers;

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
        return view('front/header')
             . view('front/navbar')
             . view('front/horarios')
             . view('front/footer');
    }

    public function precios(): string
    {
        return view('front/header')
             . view('front/navbar')
             . view('front/precios')
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