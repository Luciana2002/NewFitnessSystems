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
   USUARIOS
========================= */

$routes->get('/usuarios', 'ClienteController::usuarios');

$routes->get('/editar_usuario/(:num)', 'ClienteController::editarUsuario/$1');
$routes->post('/modificar_usuario/(:num)', 'ClienteController::modificarUsuario/$1');

$routes->get('/baja_usuario/(:num)', 'ClienteController::bajaUsuario/$1');
$routes->get('/alta_usuario/(:num)', 'ClienteController::altaUsuario/$1');

/* =========================
   CLIENTES
========================= */

$routes->get('/clientes', 'ClienteController::index');

$routes->get('/suscripciones', 'ClienteController::reporte');

$routes->get('/cliente_info/(:num)', 'ClienteController::detalle/$1');

$routes->get('/editar_cliente/(:num)', 'ClienteController::editar/$1');
$routes->post('/actualizar_cliente/(:num)', 'ClienteController::actualizar/$1');

$routes->get('/editar_profesor/(:num)', 'ClienteController::editarProfesor/$1');
$routes->post('/actualizar_profesor/(:num)', 'ClienteController::actualizarProfesor/$1');

$routes->get('/baja_cliente/(:num)', 'ClienteController::baja/$1');
$routes->get('/alta_cliente/(:num)', 'ClienteController::alta/$1');

$routes->post('/registrar_cliente', 'ClienteController::nuevoCliente');

/* =========================
   PANEL (profesores, sistemas, horarios, pagos)
========================= */

$routes->get('/profesores', 'PanelController::profesores');

$routes->get('/sistemas', 'PanelController::sistemas');
$routes->post('/actualizar_sistema/(:num)', 'PanelController::actualizarSistema/$1');
$routes->get('/baja_sistema/(:num)', 'PanelController::bajaSistema/$1');
$routes->get('/alta_sistema/(:num)', 'PanelController::altaSistema/$1');

$routes->get('/admin_horarios', 'PanelController::horarios');
$routes->post('/actualizar_horario/(:num)', 'PanelController::actualizarHorario/$1');
$routes->get('/baja_horario/(:num)', 'PanelController::bajaHorario/$1');
$routes->get('/alta_horario/(:num)', 'PanelController::altaHorario/$1');
$routes->get('/eliminar_horario/(:num)', 'PanelController::eliminarHorario/$1');
$routes->post('/cambiar_color_sistema/(:num)', 'PanelController::cambiarColorSistema/$1');

$routes->get('/pagos', 'PanelController::pagos');
$routes->post('/registrar_pago', 'PanelController::nuevoPago');
/* =========================
   PAGOS DE CLIENTES
========================= */

$routes->get('/pagos_cliente/(:num)', 'ClienteController::detalle/$1');