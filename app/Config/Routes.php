<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
$routes->get('/horarios', 'Home::horarios');
$routes->get('/precios', 'Home::precios');
$routes->get('/nosotros', 'Home::nosotros');
$routes->get('/contacto', 'Home::contacto');

$routes->get('/login', 'UsuarioController::login');
$routes->post('/enviarlogin', 'UsuarioController::auth');
$routes->get('/logout', 'UsuarioController::logout');

$routes->get('/registro', 'UsuarioController::registro');
$routes->post('/enviar-registro', 'UsuarioController::guardarRegistro');

$routes->get('/usuario_logueado', 'UsuarioController::usuarioLogueado');

$routes->get('/testdb', 'TestDB::index');