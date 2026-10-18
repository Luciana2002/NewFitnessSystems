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
$routes->get('/suscripciones/ingresos', 'ReporteController::ingresos');
$routes->get('/suscripciones/ingresos/(:num)', 'ReporteController::ingresosData/$1');
$routes->get('/suscripciones/promos', 'ReporteController::promos');
$routes->get('/suscripciones/caja', 'ReporteController::caja');
$routes->get('/suscripciones/balance', 'ReporteController::balance');
$routes->get('/suscripciones/bitacora', 'ReporteController::bitacora');
$routes->get('/suscripciones/exportar/(:segment)', 'ReporteController::exportar/$1');

$routes->post('/suscripciones/comision', 'ReporteController::actualizarComision');

/* =========================
   BACKUP
========================= */

$routes->get('/backup', 'BackupController::index');
$routes->get('/backup/descargar', 'BackupController::descargar');
$routes->post('/backup/importar', 'BackupController::importar');

/* =========================
   GASTOS
========================= */

$routes->post('/gastos/guardar', 'GastoController::guardar');
$routes->get('/gastos/eliminar/(:num)', 'GastoController::eliminar/$1');

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

/* =========================
   COMUNICACIONES
========================= */

$routes->get('/comunicaciones', 'ComunicacionController::index');
$routes->get('/comunicaciones/vencimientos', 'ComunicacionController::vencimientos');
$routes->get('/comunicaciones/abandono', 'ComunicacionController::abandono');
$routes->get('/comunicaciones/mensajes', 'ComunicacionController::mensajes');
$routes->post('/comunicaciones/enviar', 'ComunicacionController::enviar');
$routes->post('/comunicaciones/enviar-masivo', 'ComunicacionController::enviarMasivo');
$routes->post('/comunicaciones/plantilla/guardar', 'ComunicacionController::guardarPlantilla');
$routes->get('/comunicaciones/plantilla/eliminar/(:num)', 'ComunicacionController::eliminarPlantilla/$1');

/* =========================
   PANEL DEL CLIENTE
========================= */

$routes->get('/cliente/suscripcion', 'ClientePanelController::suscripcion');
$routes->get('/cliente/comprobante/(:num)', 'ClientePanelController::comprobante/$1');
$routes->get('/cliente/reservas', 'ClientePanelController::reservas');
$routes->post('/cliente/reservar', 'ClientePanelController::reservar');
$routes->get('/cliente/cancelar_reserva/(:num)', 'ClientePanelController::cancelarReserva/$1');
$routes->get('/cliente/qr', 'ClientePanelController::qr');
$routes->get('/cliente/rutinas', 'ClientePanelController::rutinas');
$routes->get('/cliente/seguimiento', 'ClientePanelController::seguimiento');
$routes->post('/cliente/guardar_seguimiento', 'ClientePanelController::guardarSeguimiento');

/* =========================
   CHECK-IN / ASISTENCIA
========================= */

$routes->get('/checkin', 'CheckinController::index');
$routes->post('/checkin/procesar', 'CheckinController::procesar');

/* =========================
   RUTINAS
========================= */

$routes->get('/rutinas', 'RutinaController::index');
$routes->get('/rutinas/nueva', 'RutinaController::nueva');
$routes->post('/rutinas/guardar', 'RutinaController::guardar');
$routes->get('/rutinas/ver/(:num)', 'RutinaController::ver/$1');
$routes->get('/rutinas/editar/(:num)', 'RutinaController::editar/$1');
$routes->post('/rutinas/actualizar/(:num)', 'RutinaController::actualizar/$1');
$routes->get('/rutinas/eliminar/(:num)', 'RutinaController::eliminar/$1');