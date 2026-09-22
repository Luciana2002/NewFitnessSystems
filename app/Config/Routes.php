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

$routes->get('/usuarios', 'UsuarioController::usuarios');

$routes->get('/editar_usuario/(:num)', 'UsuarioController::editarUsuario/$1');
$routes->post('/modificar_usuario/(:num)', 'UsuarioController::modificarUsuario/$1');

$routes->get('/baja_usuario/(:num)', 'UsuarioController::bajaUsuario/$1');
$routes->get('/alta_usuario/(:num)', 'UsuarioController::altaUsuario/$1');

/* =========================
   CLIENTES
========================= */

$routes->get('/clientes', 'ClienteController::index');

$routes->get('/suscripciones', 'ReporteController::index');

$routes->get('/suscripciones/cuotas', 'ReporteController::cuotas');
$routes->get('/suscripciones/liquidacion', 'ReporteController::liquidacion');
$routes->get('/suscripciones/ingresos', 'ReporteController::ingresos');
$routes->get('/suscripciones/ingresos/(:num)', 'ReporteController::ingresosData/$1');
$routes->get('/suscripciones/promos', 'ReporteController::promos');

$routes->get('/cliente_info/(:num)', 'ClienteController::detalle/$1');

$routes->get('/editar_cliente/(:num)', 'ClienteController::editar/$1');
$routes->post('/actualizar_cliente/(:num)', 'ClienteController::actualizar/$1');

$routes->get('/editar_profesor/(:num)', 'ProfesorController::editar/$1');
$routes->post('/actualizar_profesor/(:num)', 'ProfesorController::actualizar/$1');

$routes->get('/baja_cliente/(:num)', 'ClienteController::baja/$1');
$routes->get('/alta_cliente/(:num)', 'ClienteController::alta/$1');

$routes->post('/registrar_cliente', 'ClienteController::nuevoCliente');
$routes->post('/registrar_profesor', 'ProfesorController::nuevo');

/* =========================
   PANEL (sistemas, horarios, pagos)
========================= */

$routes->get('/profesores', 'ProfesorController::index');

$routes->get('/sistemas', 'SistemaController::index');
$routes->post('/guardar_sistema', 'SistemaController::guardar');
$routes->post('/actualizar_sistema/(:num)', 'SistemaController::actualizar/$1');
$routes->get('/baja_sistema/(:num)', 'SistemaController::baja/$1');
$routes->get('/alta_sistema/(:num)', 'SistemaController::alta/$1');

$routes->get('/admin_horarios', 'HorarioController::index');
$routes->post('/guardar_horario', 'HorarioController::guardar');
$routes->post('/actualizar_horario/(:num)', 'HorarioController::actualizar/$1');
$routes->get('/baja_horario/(:num)', 'HorarioController::baja/$1');
$routes->get('/alta_horario/(:num)', 'HorarioController::alta/$1');
$routes->get('/eliminar_horario/(:num)', 'HorarioController::eliminar/$1');
$routes->post('/cambiar_color_sistema/(:num)', 'SistemaController::cambiarColor/$1');

$routes->get('/pagos', 'PagoController::index');
$routes->post('/registrar_pago', 'PagoController::nuevo');
/* =========================
   PAGOS DE CLIENTES
========================= */

$routes->get('/pagos_cliente/(:num)', 'ClienteController::detalle/$1');