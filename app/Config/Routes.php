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
   USUARIO
========================= */

$routes->get('/login', 'UsuarioController::login');
$routes->post('/enviarlogin', 'UsuarioController::auth');
$routes->get('/logout', 'UsuarioController::logout');

$routes->get('/registro', 'UsuarioController::registro');
$routes->post('/enviar-registro', 'UsuarioController::guardarRegistro');

$routes->get('/usuario_logueado', 'UsuarioController::usuarioLogueado');

/* =========================
   ADMINISTRADOR
========================= */

$routes->get('/usuarios', 'AdminUsuarioController::index');
$routes->get('/editar_usuario/(:num)', 'AdminUsuarioController::editar/$1');
$routes->post('/modificar_usuario/(:num)', 'AdminUsuarioController::modificar/$1');
$routes->get('/baja_usuario/(:num)', 'AdminUsuarioController::baja/$1');
$routes->get('/alta_usuario/(:num)', 'AdminUsuarioController::alta/$1');

/* =========================
   CLIENTES
========================= */

$routes->get('/clientes', 'ClienteController::index');

$routes->get('/editar_cliente/(:num)', 'ClienteController::editar/$1');
$routes->post('/actualizar_cliente/(:num)', 'ClienteController::actualizar/$1');

$routes->get('/baja_cliente/(:num)', 'ClienteController::baja/$1');
$routes->get('/alta_cliente/(:num)', 'ClienteController::alta/$1');