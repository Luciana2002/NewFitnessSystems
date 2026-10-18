<?= view('reportes/_print_estilos') ?>

<style>
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

        <a href="<?= base_url('suscripciones') ?>" class="no-print" style="display:inline-block; margin-bottom:25px; color:#aaa; font-size:14px; text-decoration:none;">
            ← Volver a reportes
        </a>

        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:25px;">
            <div>
                <h1 style="margin:0;">Cierre de Caja Diario</h1>
                <p style="margin:5px 0 0; color:#aaa;">
                    Total del día por medio de pago y por quién cobró.
                </p>
            </div>
            <?= view('reportes/_acciones', ['exportUrl' => base_url('suscripciones/exportar/caja?fecha=' . $fecha)]) ?>
        </div>

        <form method="get" action="<?= base_url('suscripciones/caja') ?>" class="no-print"
              style="display:flex; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:25px;">
            <label for="fecha" style="color:#cfcfcf; text-transform:uppercase; letter-spacing:1px; font-size:13px;">Fecha</label>
            <input type="date" id="fecha" name="fecha" value="<?= esc($fecha) ?>"
                   onchange="this.form.submit()"
                   style="background:#111; border:1px solid #262626; color:#fff; border-radius:8px; padding:8px 12px;">
        </form>

        <div style="display:flex; gap:15px; flex-wrap:wrap; margin-bottom:30px;">
            <div class="dashboard-card" style="flex:1; min-width:200px; padding:22px;">
                <h6 style="text-transform:uppercase; letter-spacing:1px; color:#17a2b8;">Total recaudado</h6>
                <h2 style="margin:6px 0 0; color:#17a2b8;">$<?= number_format($total, 0, ',', '.') ?></h2>
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
                    <table class="table table-dark table-hover" style="background:#111; border-radius:8px; overflow:hidden; min-width:640px;">
                        <thead>
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
                        <tfoot>
                            <tr style="background:#262626;">
                                <td colspan="3" style="padding:12px; text-align:right; text-transform:uppercase; letter-spacing:1px; color:#fff;">Total</td>
                                <td style="padding:12px; text-align:right; color:#17a2b8; font-weight:700;">$<?= number_format($total, 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php else: ?>
                <p style="color:#aaa;">No hay pagos registrados en esta fecha.</p>
            <?php endif; ?>
        </div>
    </section>

</main>

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
