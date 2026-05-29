<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

/* =========================
   TESTS
========================= */

$routes->get('/testdb', 'TestDB::index');

/* =========================
   HOME
========================= */

$routes->get('/', 'Home::index');
$routes->get('/horarios', 'Home::horarios');
$routes->get('/precios', 'Home::precios');
$routes->get('/nosotros', 'Home::nosotros');
$routes->get('/contacto', 'Home::contacto');

/* =========================
   LOGIN / REGISTRO / SESIÓN
========================= */

$routes->get('/login', 'UsuarioController::login');
$routes->post('/enviarlogin', 'UsuarioController::auth');
$routes->get('/logout', 'UsuarioController::logout');

$routes->get('/registro', 'UsuarioController::registro');
$routes->post('/enviar-registro', 'UsuarioController::guardarRegistro');

$routes->get('/usuario_logueado', 'UsuarioController::usuarioLogueado');

/* =========================
   PERSONAS
========================= */

$routes->get('/personas', 'PersonaController::index');

$routes->get('/personas/editar/(:num)', 'PersonaController::editar/$1');
$routes->post('/personas/modificar/(:num)', 'PersonaController::modificar/$1');

$routes->get('/personas/baja/(:num)', 'PersonaController::baja/$1');
$routes->get('/personas/alta/(:num)', 'PersonaController::alta/$1');

/* =========================
   CLIENTES
========================= */

//$routes->get('/clientes', 'PersonaController::clientes');