<?= view('reportes/_print_estilos') ?>

<style>
    .reporte-seccion {
        margin-bottom: 45px;
    }

    .reporte-titulo {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        margin: 0 0 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #262626;
    }

    .reporte-titulo h2 {
        margin: 0;
        font-size: 22px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #0b8f70;
    }

    .btn-acceso {
        display: block;
        padding: 22px 26px;
        border-radius: 12px;
        background: #1b1b1b;
        border: 1px solid #262626;
        border-left: 5px solid #20c997;
        color: #fff;
        text-decoration: none;
        transition: transform .15s ease, border-color .15s ease;
    }

    .btn-acceso:hover {
        transform: translateY(-3px);
        color: #fff;
    }

    .btn-acceso h5 {
        margin: 0 0 6px;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 16px;
    }

    .btn-acceso p {
        margin: 0;
        color: #aaa;
        font-size: 14px;
    }

    input[type="date"],
    input[type="month"] {
        color-scheme: dark;
    }
</style>

<main class="dashboard-page">

    <button type="button" id="sidebarOpen" class="sidebar-handle">
        <span>›</span>
    </button>

    <button type="button" id="sidebarClose" class="sidebar-close">
        ✕
    </button>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <?= view('layout/sidebar') ?>

    <section class="dashboard-content" style="padding:50px 70px;">

        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:35px;">
            <div>
                <h1 style="margin:0;">Reportes del Negocio</h1>
            </div>
            <button type="button" class="no-print" onclick="window.print()"
                    style="background:#0b8f70; border:none; color:#fff; border-radius:8px; padding:10px 18px; text-transform:uppercase; letter-spacing:1px; font-size:13px;">
                Imprimir / PDF
            </button>
        </div>

        <!-- ===================== CIERRE DE CAJA DIARIO ===================== -->
        <div class="reporte-seccion">
            <div class="reporte-titulo">
                <h2>Cierre de Caja Diario</h2>
                <a class="no-print" href="<?= base_url('suscripciones/exportar/caja?fecha=' . $fecha) ?>"
                   style="background:#262626; border:1px solid #333; color:#fff; border-radius:8px; padding:8px 16px; text-decoration:none; text-transform:uppercase; letter-spacing:1px; font-size:12px;">
                    Exportar Excel
                </a>
            </div>

            <form method="get" action="<?= base_url('suscripciones') ?>" class="no-print"
                  style="display:flex; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:25px;">
                <label for="fecha" style="color:#cfcfcf; text-transform:uppercase; letter-spacing:1px; font-size:13px;">Fecha</label>
                <input type="date" id="fecha" name="fecha" value="<?= esc($fecha) ?>"
                       onchange="this.form.submit()"
                       style="background:#111; border:1px solid #262626; color:#fff; border-radius:8px; padding:8px 12px;">
            </form>

            <div style="display:flex; gap:15px; flex-wrap:wrap; margin-bottom:30px;">
                <div class="dashboard-card" style="flex:1; min-width:200px; padding:22px;">
                    <h6 style="text-transform:uppercase; letter-spacing:1px; color:#17a2b8;">Total recaudado</h6>
                    <h2 style="margin:6px 0 0; color:#17a2b8;">$<?= number_format($totalCaja, 0, ',', '.') ?></h2>
                </div>

                <div class="dashboard-card" style="flex:1; min-width:200px; padding:22px;">
                    <h6 style="text-transform:uppercase; letter-spacing:1px; color:#0b8f70;">Movimientos</h6>
                    <h2 style="margin:6px 0 0;"><?= count($pagos) ?></h2>
                </div>

                <div class="dashboard-card" style="flex:1; min-width:200px; padding:22px;">
                    <h6 style="text-transform:uppercase; letter-spacing:1px; color:#ffc107;">Fecha</h6>
                    <h2 style="margin:6px 0 0; font-size:22px;"><?= esc(date('d/m/Y', strtotime($fecha))) ?></h2>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="dashboard-card" style="padding:25px; height:100%; max-width:none;">
                        <h4 style="margin-bottom:20px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                            Totales por medio de pago
                        </h4>

                        <?php if (!empty($porMedio)): ?>
                            <?php foreach ($porMedio as $medio => $monto): ?>
                                <div style="display:flex; justify-content:space-between; gap:10px; padding:10px 0; border-bottom:1px solid #262626; color:#ddd; overflow-wrap:anywhere;">
                                    <span><?= esc($medio) ?></span>
                                    <strong style="color:#17a2b8;">$<?= number_format($monto, 0, ',', '.') ?></strong>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color:#aaa;">Sin movimientos este día.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="dashboard-card" style="padding:25px; height:100%; max-width:none;">
                        <h4 style="margin-bottom:20px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                            Totales por quién cobró
                        </h4>

                        <?php if (!empty($porCobrador)): ?>
                            <?php foreach ($porCobrador as $cobrador => $monto): ?>
                                <div style="display:flex; justify-content:space-between; gap:10px; padding:10px 0; border-bottom:1px solid #262626; color:#ddd; overflow-wrap:anywhere;">
                                    <span><?= esc($cobrador) ?></span>
                                    <strong style="color:#20c997;">$<?= number_format($monto, 0, ',', '.') ?></strong>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color:#aaa;">Sin movimientos este día.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="dashboard-card" style="padding:25px; max-width:none;">
                <h4 style="margin-bottom:20px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                    Detalle de pagos
                </h4>

                <?php if (!empty($pagos)): ?>
                    <div style="overflow-x:auto;">
                        <div style="max-height:520px; overflow-y:auto; border:1px solid #262626; border-radius:8px;">
                            <table class="table table-dark table-hover" style="background:#111; border-radius:8px; overflow:hidden; min-width:640px; margin:0;">
                                <thead style="position:sticky; top:0; z-index:1;">
                                    <tr style="background:#262626; color:#fff; text-transform:uppercase; letter-spacing:1px; font-size:13px;">
                                        <th style="padding:12px;">Cliente</th>
                                        <th style="padding:12px;">Medio de pago</th>
                                        <th style="padding:12px;">Cobrado por</th>
                                        <th style="padding:12px; text-align:right;">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pagos as $pago): ?>
                                        <?php
                                            $cobrador = trim(($pago['cobrador_nombre'] ?? '') . ' ' . ($pago['cobrador_apellido'] ?? ''));
                                            if ($cobrador === '') {
                                                $cobrador = $pago['cobrador_usuario'] ?: 'Sin registrar';
                                            }
                                            $cliente = trim(($pago['cliente_apellido'] ?? '') . ', ' . ($pago['cliente_nombre'] ?? ''));
                                            if ($cliente === ',') {
                                                $cliente = '—';
                                            }
                                        ?>
                                        <tr style="color:#ddd; border-bottom:1px solid #262626;">
                                            <td style="padding:12px; overflow-wrap:anywhere;"><?= esc($cliente) ?></td>
                                            <td style="padding:12px; overflow-wrap:anywhere;"><?= esc($pago['medio_pago'] ?: 'Sin especificar') ?></td>
                                            <td style="padding:12px; overflow-wrap:anywhere;"><?= esc($cobrador) ?></td>
                                            <td style="padding:12px; text-align:right; color:#17a2b8; font-weight:700;">$<?= number_format((float) $pago['monto'], 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <p style="color:#aaa;">No hay pagos registrados en esta fecha.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- ===================== REPORTE DE CUOTAS ===================== -->
        <div class="reporte-seccion">
            <div class="reporte-titulo">
                <h2>Reporte de Cuotas</h2>
                <div class="no-print" style="display:flex; gap:10px; flex-wrap:wrap;">
                    <a href="<?= base_url('suscripciones/exportar/cuotas') ?>"
                       style="background:#262626; border:1px solid #333; color:#fff; border-radius:8px; padding:8px 16px; text-decoration:none; text-transform:uppercase; letter-spacing:1px; font-size:12px;">
                        Exportar Excel
                    </a>
                    <a href="<?= base_url('suscripciones/cuotas') ?>"
                       style="background:#0b8f70; color:#fff; border-radius:8px; padding:8px 16px; text-decoration:none; text-transform:uppercase; letter-spacing:1px; font-size:12px;">
                        Ver reporte completo
                    </a>
                </div>
            </div>

            <div style="display:flex; gap:15px; flex-wrap:wrap; margin-bottom:30px;">
                <div class="dashboard-card" style="flex:1; min-width:170px; padding:20px;">
                    <h6 style="text-transform:uppercase; letter-spacing:1px; color:#0b8f70;">Total de clientes</h6>
                    <h2 style="margin:5px 0 0;"><?= $total ?></h2>
                </div>

                <div class="dashboard-card" style="flex:1; min-width:170px; padding:20px;">
                    <h6 style="text-transform:uppercase; letter-spacing:1px; color:#0b8f70;">Cuotas al día</h6>
                    <h2 style="margin:5px 0 0; color:#28a745;"><?= $alDia ?></h2>
                </div>

                <div class="dashboard-card" style="flex:1; min-width:170px; padding:20px;">
                    <h6 style="text-transform:uppercase; letter-spacing:1px; color:#0b8f70;">Cuotas vencidas</h6>
                    <h2 style="margin:5px 0 0; color:#dc3545;"><?= $vencido ?></h2>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="dashboard-card" style="padding:25px; height:100%; max-width:none;">
                        <h4 style="margin-bottom:20px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                            Clientes por sistema
                        </h4>
                        <div style="height:280px;">
                            <canvas id="graficoSistemas"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="dashboard-card" style="padding:25px; height:100%; max-width:none;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:10px; flex-wrap:wrap;">
                            <h4 style="margin:0; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                                Ganancia por sistema
                            </h4>
                            <a href="<?= base_url('suscripciones/ingresos') ?>" style="background:#0b8f70; color:#fff; border-radius:8px; padding:6px 12px; text-decoration:none; text-transform:uppercase; letter-spacing:1px; font-size:11px;">Ver por mes</a>
                        </div>

                        <?php $maxSistema = !empty($ingresosPorSistema) ? max(array_column($ingresosPorSistema, 'total')) : 0; ?>
                        <?php if (!empty($ingresosPorSistema)): ?>
                            <?php foreach ($ingresosPorSistema as $s): ?>
                                <div style="margin-bottom:18px;">
                                    <div style="display:flex; justify-content:space-between; gap:10px; color:#ddd; font-size:14px;">
                                        <span><?= esc($s['nombre_sistema']) ?></span>
                                        <strong style="color:#0b8f70;">$<?= number_format((float) $s['total'], 0, ',', '.') ?></strong>
                                    </div>
                                    <div style="background:#262626; border-radius:6px; height:14px; overflow:hidden; margin-top:6px;">
                                        <div style="width:<?= $maxSistema > 0 ? round(((float) $s['total'] / $maxSistema) * 100) : 0 ?>%; background:#0b8f70; height:100%;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color:#aaa;">Aún no hay pagos registrados.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== ACCESOS ===================== -->
        <div class="reporte-seccion" style="margin-bottom:0;">
            <div class="reporte-titulo">
                <h2>Más reportes</h2>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <a href="<?= base_url('suscripciones/balance') ?>" class="btn-acceso" style="border-left-color:#0b8f70;">
                        <h5 style="color:#10c796;">Gastos y Balance</h5>
                        <p>Registrá egresos y calculá la ganancia neta del mes.</p>
                    </a>
                </div>

                <div class="col-lg-6">
                    <a href="<?= base_url('suscripciones/bitacora') ?>" class="btn-acceso" style="border-left-color:#0b8f70;">
                        <h5 style="color:#10c796;">Historial de movimientos</h5>
                        <p>Quién hizo qué y cuándo: altas, bajas y cambios.</p>
                    </a>
                </div>
            </div>

        </div>

    </section>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const etiquetasSistemas = <?= json_encode(array_keys($sistemas)) ?>;
    const datosSistemas = <?= json_encode(array_values($sistemas)) ?>;

    new Chart(document.getElementById('graficoSistemas'), {
        type: 'bar',
        data: {
            labels: etiquetasSistemas,
            datasets: [{
                label: 'Clientes inscritos',
                data: datosSistemas,
                backgroundColor: 'rgba(11, 143, 112, 0.8)',
                borderColor: '#0b8f70',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#cfcfcf' }, grid: { color: 'rgba(255, 255, 255, 0.08)' } },
                y: { beginAtZero: true, ticks: { color: '#cfcfcf', stepSize: 1 }, grid: { color: 'rgba(255, 255, 255, 0.08)' } }
            }
        }
    });

</script>

<script>
    const sidebarOpen = document.getElementById('sidebarOpen');
    const sidebarClose = document.getElementById('sidebarClose');
    const sidebar = document.getElementById('dashboardSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function abrirSidebar() {
        sidebar.classList.add('show-sidebar');
        overlay.classList.add('active');
        sidebarOpen.style.display = 'none';
        sidebarClose.style.display = 'flex';
    }

    function cerrarSidebar() {
        sidebar.classList.remove('show-sidebar');
        overlay.classList.remove('active');
        sidebarOpen.style.display = '';
        sidebarClose.style.display = '';
    }

    if (sidebarOpen && sidebarClose && sidebar && overlay) {
        sidebarOpen.addEventListener('click', abrirSidebar);
        sidebarClose.addEventListener('click', cerrarSidebar);
        overlay.addEventListener('click', cerrarSidebar);

        document.addEventListener('keydown', function(e) {
            if (e.key === "Escape") {
                cerrarSidebar();
            }
        });
    }
</script>
