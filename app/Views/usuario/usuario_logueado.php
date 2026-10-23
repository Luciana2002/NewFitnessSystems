<?php
$idRol   = (int) session()->get('id_rol');
$resumen = $resumen ?? [];

$nombre   = (string) session()->get('nombre');
$apellido = (string) session()->get('apellido');

$horaActual = (int) date('G');

if ($horaActual < 12) {
    $saludo = 'Buenos días';
} elseif ($horaActual < 20) {
    $saludo = 'Buenas tardes';
} else {
    $saludo = 'Buenas noches';
}

$diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
$meses = [
    1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
    5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
    9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
];

$fechaLarga = $diasSemana[(int) date('w')] . ' ' . (int) date('j') . ' de ' . $meses[(int) date('n')] . ' de ' . date('Y');

$panelNombre = match ($idRol) {
    1 => 'Panel de administración',
    2 => 'Panel del profesor',
    default => 'Mi panel',
};

$ctaUrl = match ($idRol) {
    1 => base_url('pagos/registrar'),
    2 => base_url('pagos/registrar'),
    default => base_url('cliente/suscripcion'),
};

$ctaTexto = match ($idRol) {
    1 => 'Registrar Pago',
    2 => 'Registrar Pago',
    default => 'Ver suscripción',
};

$ctaIcono = match ($idRol) {
    1 => 'bi-cash-coin',
    2 => 'bi-cash-coin',
    default => 'bi-card-list',
};

$success = session()->getFlashdata('success');
$error   = session()->getFlashdata('error');
?>

<main class="dashboard-page home-page">

    <section class="home-shell">

        <header class="home-head">
            <div class="home-head-text">
                <span class="home-eyebrow"><?= esc($fechaLarga) ?></span>
                <h1 class="home-title"><?= esc($saludo . ', ' . trim($nombre . ' ' . $apellido)) ?></h1>
                <p class="home-sub">
                    <?= esc($panelNombre) ?> — revisá de un vistazo lo que está pasando en el gimnasio hoy.
                </p>
            </div>

            <a class="home-cta" href="<?= esc($ctaUrl) ?>">
                <i class="bi <?= esc($ctaIcono) ?>"></i>
                <?= esc($ctaTexto) ?>
            </a>
        </header>

        <?php if ($success): ?>
            <div class="toast-custom success" style="opacity:1;"><?= esc($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="toast-custom error" style="opacity:1;"><?= esc($error) ?></div>
        <?php endif; ?>

        <?php if (! empty($accesos) && $idRol !== 1): ?>
            <div class="quick-head">
                <h2>Accesos rápidos</h2>
            </div>

            <div class="quick-grid" style="grid-template-columns:repeat(<?= min(6, count($accesos)) ?>, minmax(0, 1fr));">
                <?php foreach ($accesos as $acceso): ?>
                    <a class="quick-link" href="<?= base_url($acceso[0]) ?>">
                        <i class="bi <?= esc($acceso[1]) ?>"></i>
                        <?= esc($acceso[2]) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php /* ========================= ADMINISTRADOR ========================= */ ?>
        <?php if ($idRol === 1): ?>
            <?php $accesos = [
                ['clientes',      'bi-people',        'Clientes'],
                ['pagos',         'bi-cash-coin',     'Pagos'],
                ['rutinas',       'bi-list-check',    'Rutinas'],
                ['avisos',        'bi-megaphone',     'Avisos'],
                ['suscripciones', 'bi-bar-chart-line','Reportes'],
                ['backup',        'bi-database-down', 'Backup'],
            ]; ?>

            <div class="quick-head">
                <h2>Accesos rápidos</h2>
            </div>

            <div class="quick-grid" style="grid-template-columns:repeat(<?= min(6, count($accesos)) ?>, minmax(0, 1fr));">
                <?php foreach ($accesos as $acceso): ?>
                    <a class="quick-link" href="<?= base_url($acceso[0]) ?>">
                        <i class="bi <?= esc($acceso[1]) ?>"></i>
                        <?= esc($acceso[2]) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php
            $mesNum    = (int) date('n');
            $mesNombre = $meses[$mesNum] ?? date('F');
            $balanceMes = (float) ($resumen['balance_mes'] ?? 0);
            $ingresosMes = (float) ($resumen['ingresos_mes'] ?? 0);
            $pagosHoy    = (float) ($resumen['pagos_hoy'] ?? 0);
            $pagosHoyCnt = (int) ($resumen['pagos_hoy_count'] ?? 0);
            $clientesAct = (int) ($resumen['clientes_activos'] ?? 0);
            ?>

            <div class="kpi-grid">
                <article class="kpi-card is-ok">
                    <div class="kpi-top">
                        <span class="kpi-icon"><i class="bi bi-people"></i></span>
                        <span class="kpi-label" style="color:#10c796;">CLIENTES ACTIVOS</span>
                    </div>
                    <div class="kpi-value"><?= $clientesAct ?></div>
                    <p class="kpi-hint">Personas con cuenta habilitada</p>
                    <a class="kpi-link" href="<?= base_url('clientes') ?>">VER CLIENTES <i class="bi bi-arrow-right"></i></a>
                </article>

                <article class="kpi-card is-ok">
                    <div class="kpi-top">
                        <span class="kpi-icon"><i class="bi bi-receipt"></i></span>
                        <span class="kpi-label" style="color:#10c796;">PAGOS REGISTRADOS HOY</span>
                    </div>
                    <div class="kpi-value"><?= $pagosHoyCnt ?></div>
                    <p class="kpi-hint">Recaudado hoy: <?= esc(formatear_monto($pagosHoy)) ?></p>
                    <a class="kpi-link" href="<?= base_url('pagos') ?>">VER PAGOS <i class="bi bi-arrow-right"></i></a>
                </article>

                <article class="kpi-card is-ok">
                    <div class="kpi-top">
                        <span class="kpi-icon"><i class="bi bi-cash-stack"></i></span>
                        <span class="kpi-label" style="color:#10c796;">RECAUDADO DEL MES</span>
                    </div>
                    <div class="kpi-value"><?= esc(formatear_monto($ingresosMes)) ?></div>
                    <p class="kpi-hint">Pagos de <?= esc(ucfirst($mesNombre)) ?> · hoy <?= esc(formatear_monto($pagosHoy)) ?></p>
                    <a class="kpi-link" href="<?= base_url('pagos') ?>">VER PAGOS <i class="bi bi-arrow-right"></i></a>
                </article>

                <article class="kpi-card is-ok">
                    <div class="kpi-top">
                        <span class="kpi-icon"><i class="bi bi-graph-up-arrow"></i></span>
                        <span class="kpi-label" style="color:#10c796;">BALANCE DEL MES</span>
                    </div>
                    <div class="kpi-value"><?= esc(formatear_monto($balanceMes)) ?></div>
                    <p class="kpi-hint">Ingresos - gastos (incluye sueldos)</p>
                    <a class="kpi-link" href="<?= base_url('suscripciones/balance') ?>">VER BALANCE <i class="bi bi-arrow-right"></i></a>
                </article>
            </div>



            <div class="panel-grid">

                <section class="panel-card">
                    <div class="panel-card-head">
                        <h2><i class="bi bi-hourglass-bottom"></i> Próximos vencimientos</h2>
                        <a href="<?= base_url('avisos/vencimientos') ?>">Ver todos</a>
                    </div>

                    <?php $proximos = $resumen['proximos'] ?? []; ?>

                    <?php if (empty($proximos)): ?>
                        <p class="panel-empty">No hay cuotas por vencer en este momento.</p>
                    <?php else: ?>
                        <ul class="panel-list">
                            <?php foreach ($proximos as $fila): ?>
                                <?php
                                $dias  = (int) ($fila['dias'] ?? 0);
                                $pills = $dias < 0
                                    ? ['is-danger', 'Vencida hace ' . abs($dias) . ' días']
                                    : ($dias === 0
                                        ? ['is-danger', 'Vence hoy']
                                        : ['is-warn', 'Vence en ' . $dias . ' día' . ($dias === 1 ? '' : 's')]);
                                ?>
                                <li class="list-row">
                                    <div class="list-main">
                                        <strong><?= esc(trim($fila['nombre'] . ' ' . $fila['apellido'])) ?></strong>
                                        <span><?= esc($fila['nombre_sistema']) ?></span>
                                    </div>
                                    <div class="list-side">
                                        <span class="list-date"><?= esc(formatear_fecha($fila['_fecha'] ?? $fila['fecha_vencimiento'])) ?></span>
                                        <span class="status-pill <?= $pills[0] ?>"><?= esc($pills[1]) ?></span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>

                <section class="panel-card">
                    <div class="panel-card-head">
                        <h2><i class="bi bi-clock-history"></i> Actividad reciente</h2>
                        <a href="<?= base_url('suscripciones/bitacora') ?>">Bitácora</a>
                    </div>

                    <?php $actividad = $resumen['actividad'] ?? []; ?>

                    <?php if (empty($actividad)): ?>
                        <p class="panel-empty">Todavía no hay movimientos registrados.</p>
                    <?php else: ?>
                        <ul class="panel-list">
                            <?php foreach ($actividad as $registro): ?>
                                <li class="list-row">
                                    <div class="list-main">
                                        <strong><?= esc($registro['nombre_usuario']) ?></strong>
                                        <span><?= esc($registro['accion'] . ' — ' . $registro['entidad'] . (isset($registro['id_entidad']) && $registro['id_entidad'] ? ' #' . $registro['id_entidad'] : '')) ?></span>
                                    </div>
                                    <div class="list-side">
                                        <span class="list-date"><?= esc($registro['_fecha'] ?? formatear_fecha($registro['fecha'])) ?></span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>

            </div>

        <?php /* ========================= PROFESOR ========================= */ ?>
        <?php elseif ($idRol === 2): ?>
            <?php $accesos = [
                ['rutinas/nueva',       'bi-plus-circle',       'Nueva rutina'],
                ['rutinas',             'bi-list-check',        'Mis rutinas'],
                ['clientes',            'bi-people',            'Clientes'],
                ['pagos',               'bi-cash-coin',         'Pagos'],
                ['perfil',              'bi-person',            'Mi perfil'],
            ]; ?>

            <div class="quick-head">
                <h2>Accesos rápidos</h2>
            </div>

            <div class="quick-grid" style="grid-template-columns:repeat(<?= min(5, count($accesos)) ?>, minmax(0, 1fr));">
                <?php foreach ($accesos as $acceso): ?>
                    <a class="quick-link" href="<?= base_url($acceso[0]) ?>">
                        <i class="bi <?= esc($acceso[1]) ?>"></i>
                        <?= esc($acceso[2]) ?>
                    </a>
                <?php endforeach; ?>
            </div>



            <div class="panel-grid">

                <section class="panel-card">
                    <div class="panel-card-head">
                        <h2><i class="bi bi-journal-text"></i> Mis rutinas recientes</h2>
                        <a href="<?= base_url('rutinas') ?>">Ver todas</a>
                    </div>

                    <?php $recientes = $resumen['rutinas_recientes'] ?? []; ?>

                    <?php if (empty($recientes)): ?>
                        <p class="panel-empty">Todavía no creaste rutinas.</p>
                    <?php else: ?>
                        <ul class="panel-list">
                            <?php foreach ($recientes as $rutina): ?>
                                <?php $cliente = trim(($rutina['cliente_nombre'] ?? '') . ' ' . ($rutina['cliente_apellido'] ?? '')); ?>
                                <li class="list-row">
                                    <div class="list-main">
                                        <strong><?= esc($rutina['nombre']) ?></strong>
                                        <span><?= $cliente !== '' ? esc($cliente) : 'Sin cliente asignado' ?></span>
                                    </div>
                                    <div class="list-side">
                                        <span class="list-date"><?= esc(formatear_fecha($rutina['fecha'] ?? null)) ?></span>
                                        <a class="mini-link" href="<?= base_url('rutinas/ver/' . (int) $rutina['id_rutina']) ?>">Ver</a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>

                <section class="panel-card">
                    <div class="panel-card-head">
                        <h2><i class="bi bi-cash-coin"></i> Pagos recientes</h2>
                        <a href="<?= base_url('pagos') ?>">Ver todos</a>
                    </div>

                    <?php
                    $pagosModelProf = new \App\Models\PagoModel();
                    $pagosRecientesProf = array_slice($pagosModelProf->getPagosAll(), 0, 6);
                    ?>

                    <?php if (empty($pagosRecientesProf)): ?>
                        <p class="panel-empty">No hay pagos registrados.</p>
                    <?php else: ?>
                        <ul class="panel-list">
                            <?php foreach ($pagosRecientesProf as $pago): ?>
                                <?php $clientePago = trim(($pago['cliente_nombre'] ?? '') . ' ' . ($pago['cliente_apellido'] ?? '')); ?>
                                <li class="list-row">
                                    <div class="list-main">
                                        <strong><?= $clientePago !== '' ? esc($clientePago) : 'Cliente' ?></strong>
                                        <span><?= esc($pago['nombre_sistema'] ?? '') ?> · <?= esc($pago['medio_pago'] ?? '') ?></span>
                                    </div>
                                    <div class="list-side">
                                        <span class="list-date"><?= esc(formatear_fecha($pago['fecha_pago'] ?? null)) ?></span>
                                        <span class="status-pill is-ok"><?= esc(formatear_monto($pago['monto'] ?? 0)) ?></span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>
            </div>

        <?php /* ========================= CLIENTE ========================= */ ?>
        <?php else: ?>
            <?php
            $membresia = $resumen['membresia'] ?? [
                'estado' => 'sin_suscripcion', 'activo' => false, 'dias' => null, 'vencimiento' => null,
            ];

            $estadoMembresia = $membresia['estado'] ?? 'sin_suscripcion';

            [$claseMembresia, $textoMembresia] = match ($estadoMembresia) {
                'activa'     => ['is-ok', 'Cuota al día'],
                'vencida'    => ['is-danger', 'Cuota vencida'],
                default      => ['is-muted', 'Sin suscripción'],
            };
            ?>

            <section class="membership-card <?= $claseMembresia === 'is-ok' ? '' : $claseMembresia ?>">
                <div class="membership-info">
                    <span class="home-eyebrow">Mi membresía</span>
                    <h2><?= esc($textoMembresia) ?></h2>

                    <?php if ($estadoMembresia === 'activa'): ?>
                        <p>Vence el <?= esc(formatear_fecha($membresia['vencimiento'])) ?> — te quedan
                            <strong><?= (int) $membresia['dias'] ?> día<?= (int) $membresia['dias'] === 1 ? '' : 's' ?></strong>.
                        </p>
                    <?php elseif ($estadoMembresia === 'vencida'): ?>
                        <p>Tu cuota venció el <?= esc(formatear_fecha($membresia['vencimiento'])) ?>. Regularizá tu pago para seguir entrenando.</p>
                    <?php else: ?>
                        <p>Todavía no tenés ninguna suscripción activa. Consultanos por nuestros planes.</p>
                    <?php endif; ?>
                </div>

                <div class="membership-actions">
                    <a class="home-cta" href="<?= base_url('cliente/suscripcion') ?>">
                        <i class="bi bi-card-list"></i> Ver mi suscripción
                    </a>
                    <a class="home-cta ghost" href="<?= base_url('cliente/reservas') ?>">
                        <i class="bi bi-calendar-check"></i> Reservar clases
                    </a>
                </div>
            </section>

            <div class="kpi-grid">
                <article class="kpi-card <?= $claseMembresia ?>">
                    <div class="kpi-top">
                        <span class="kpi-icon"><i class="bi bi-hourglass-split"></i></span>
                        <span class="kpi-label">Días de cuota</span>
                    </div>
                    <div class="kpi-value">
                        <?= $membresia['dias'] === null ? '—' : (int) $membresia['dias'] ?>
                    </div>
                    <p class="kpi-hint">
                        <?= $membresia['vencimiento'] ? 'Vence el ' . esc(formatear_fecha($membresia['vencimiento'])) : 'Sin vencimiento registrado.' ?>
                    </p>
                </article>

                <article class="kpi-card is-info">
                    <div class="kpi-top">
                        <span class="kpi-icon"><i class="bi bi-list-check"></i></span>
                        <span class="kpi-label">Mis rutinas</span>
                    </div>
                    <div class="kpi-value"><?= (int) ($resumen['rutinas_count'] ?? 0) ?></div>
                    <p class="kpi-hint">Planes de entrenamiento asignados por tu profesor.</p>
                    <a class="kpi-link" href="<?= base_url('cliente/rutinas') ?>">Ver rutinas <i class="bi bi-arrow-right"></i></a>
                </article>

                <article class="kpi-card is-muted">
                    <div class="kpi-top">
                        <span class="kpi-icon"><i class="bi bi-receipt"></i></span>
                        <span class="kpi-label">Pagos registrados</span>
                    </div>
                    <div class="kpi-value"><?= (int) ($resumen['pagos_count'] ?? 0) ?></div>
                    <p class="kpi-hint">Historial de cuotas abonadas.</p>
                    <a class="kpi-link" href="<?= base_url('cliente/suscripcion') ?>">Ver historial <i class="bi bi-arrow-right"></i></a>
                </article>

                <article class="kpi-card is-ok">
                    <div class="kpi-top">
                        <span class="kpi-icon"><i class="bi bi-cash-coin"></i></span>
                        <span class="kpi-label">Total abonado</span>
                    </div>
                    <div class="kpi-value"><?= esc(formatear_monto($resumen['total_pagado'] ?? 0)) ?></div>
                    <p class="kpi-hint">Suma de todos tus pagos.</p>
                </article>
            </div>

            <div class="panel-grid">

                <section class="panel-card">
                    <div class="panel-card-head">
                        <h2><i class="bi bi-grid-3x3-gap"></i> Mis sistemas</h2>
                        <a href="<?= base_url('cliente/suscripcion') ?>">Administrar</a>
                    </div>

                    <?php $sistemas = $resumen['sistemas'] ?? []; ?>

                    <?php if (empty($sistemas)): ?>
                        <p class="panel-empty">No estás inscripto a ningún sistema todavía.</p>
                    <?php else: ?>
                        <ul class="panel-list">
                            <?php foreach ($sistemas as $sistema): ?>
                                <?php
                                $estadoSistema = (string) ($sistema['estado_suscripcion'] ?? '');
                                $pilClass  = 'is-muted';
                                $pilTexto  = $estadoSistema !== '' ? $estadoSistema : 'Sin estado';

                                if (mb_stripos($estadoSistema, 'activa') !== false) {
                                    $pilClass = 'is-ok';
                                } elseif (mb_stripos($estadoSistema, 'vencid') !== false || mb_stripos($estadoSistema, 'cancel') !== false) {
                                    $pilClass = 'is-danger';
                                }
                                ?>
                                <li class="list-row">
                                    <div class="list-main">
                                        <strong>
                                            <?php if (! empty($sistema['color'])): ?>
                                                <span class="color-dot" style="background:<?= esc($sistema['color']) ?>;"></span>
                                            <?php endif; ?>
                                            <?= esc($sistema['nombre_sistema'] ?? 'Sistema') ?>
                                        </strong>
                                        <span>
                                            Vence el <?= esc(formatear_fecha($sistema['_fecha'] ?? ($sistema['fecha_vencimiento'] ?? null))) ?>
                                        </span>
                                    </div>
                                    <div class="list-side">
                                        <span class="status-pill <?= $pilClass ?>"><?= esc($pilTexto) ?></span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>

                <section class="panel-card">
                    <div class="panel-card-head">
                        <h2><i class="bi bi-receipt-cutoff"></i> Mi último pago</h2>
                        <a href="<?= base_url('cliente/suscripcion') ?>">Ver todos</a>
                    </div>

                    <?php $ultimoPago = $resumen['ultimo_pago'] ?? null; ?>

                    <?php if (! $ultimoPago): ?>
                        <p class="panel-empty">Todavía no registrás pagos.</p>
                    <?php else: ?>
                        <ul class="panel-list">
                            <li class="list-row">
                                <div class="list-main">
                                    <strong><?= esc(formatear_monto($ultimoPago['monto'] ?? 0)) ?></strong>
                                    <span><?= esc($ultimoPago['nombre_sistema'] ?? '') ?> — <?= esc($ultimoPago['medio_pago'] ?? '') ?></span>
                                </div>
                                <div class="list-side">
                                    <span class="list-date"><?= esc(formatear_fecha($ultimoPago['fecha_pago'] ?? null)) ?></span>
                                    <a class="mini-link" href="<?= base_url('cliente/comprobante/' . (int) $ultimoPago['id_pago']) ?>">Comprobante</a>
                                </div>
                            </li>
                        </ul>
                    <?php endif; ?>
                </section>

            </div>

            <?php $accesos = [
                ['cliente/suscripcion',   'bi-card-list',        'Mi suscripción'],
                ['cliente/reservas',      'bi-calendar-check',   'Reservar clases'],
                ['cliente/qr',            'bi-qr-code',          'Mi código QR'],
                ['cliente/rutinas',       'bi-list-check',       'Mis rutinas'],
                ['cliente/seguimiento',   'bi-activity',         'Seguimiento físico'],
                ['perfil',                'bi-person',           'Mi perfil'],
            ]; ?>
        <?php endif; ?>

    </section>

</main>

<script>
    const toasts = document.querySelectorAll('.toast-custom');

    toasts.forEach((toast, index) => {
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        }, 100);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-20px)';
            setTimeout(() => toast.remove(), 400);
        }, 2800 + (index * 200));
    });
</script>
