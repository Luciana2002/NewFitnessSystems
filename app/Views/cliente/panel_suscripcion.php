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

        <h1 style="margin-bottom:25px;">Mi suscripción</h1>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="toast-custom success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="toast-custom error"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <?php
        $estado   = $membresia['estado'];
        $activo   = $membresia['activo'];
        $dias     = $membresia['dias'];
        $vence    = $membresia['vencimiento'];

        if ($estado === 'sin_suscripcion') {
            $badgeClass = 'badge bg-secondary';
            $badgeTxt   = 'Sin suscripción';
            $resumen    = 'Todavía no tenés ningún sistema activo. Consultá en recepción.';
        } elseif ($activo) {
            $badgeClass = $dias <= 5 ? 'badge bg-warning text-dark' : 'badge bg-success';
            $badgeTxt   = $dias <= 5 ? 'Por vencer' : 'Al día';
            $resumen    = $dias === 0
                ? 'Tu cuota vence hoy (' . formatear_fecha($vence) . ').'
                : 'Tu cuota vence en ' . $dias . ' día' . ($dias == 1 ? '' : 's') . ' (' . formatear_fecha($vence) . ').';
        } else {
            $badgeClass = 'badge bg-danger';
            $badgeTxt   = 'Vencida';
            $resumen    = 'Tu cuota venció hace ' . abs((int) $dias) . ' día' . (abs((int) $dias) == 1 ? '' : 's') . ' (' . formatear_fecha($vence) . ').';
        }
        ?>

        <div class="dashboard-card" style="max-width:none; width:100%; padding:30px 35px; margin-bottom:30px;">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap;">
                <div>
                    <div class="menu-section-title" style="margin-bottom:6px;">Estado de tu cuota</div>
                    <div style="font-size:22px; font-weight:800; margin-bottom:8px;"><?= $resumen ?></div>
                    <?php if($vence): ?>
                        <div style="color:#9a9a9a; font-size:14px;">Vencimiento: <?= formatear_fecha($vence) ?></div>
                    <?php endif; ?>
                </div>
                <span class="<?= $badgeClass ?>" style="font-size:16px; padding:12px 22px;"><?= $badgeTxt ?></span>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:20px; margin-bottom:40px;">
            <?php if(empty($suscripciones)): ?>
                <div class="dashboard-card" style="max-width:none;">No hay sistemas registrados.</div>
            <?php else: ?>
                <?php foreach($suscripciones as $sus): ?>
                    <?php
                    $esCancelada = ($sus['estado_suscripcion'] === 'Cancelada');
                    $vencida = !$esCancelada && !empty($sus['fecha_vencimiento']) && strtotime((string) $sus['fecha_vencimiento']) < strtotime(date('Y-m-d'));
                    ?>
                    <div class="dashboard-card" style="max-width:none;">
                        <div style="font-size:19px; font-weight:800; margin-bottom:10px;"><?= esc($sus['nombre_sistema']) ?></div>
                        <p style="margin-bottom:6px;"><strong>Estado:</strong>
                            <?php if($esCancelada): ?>
                                <span class="badge bg-secondary">Cancelada</span>
                            <?php elseif($vencida): ?>
                                <span class="badge bg-danger">Vencida</span>
                            <?php else: ?>
                                <span class="badge bg-success">Activa</span>
                            <?php endif; ?>
                        </p>
                        <p style="margin-bottom:6px;"><strong>Inicio:</strong> <?= formatear_fecha($sus['fecha_inicio']) ?></p>
                        <p style="margin-bottom:6px;"><strong>Vencimiento:</strong> <?= formatear_fecha($sus['fecha_vencimiento']) ?></p>
                        <p style="margin-bottom:0;"><strong>Precio:</strong> <?= formatear_monto($sus['precio_mensualidad']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <h2 style="font-size:26px; font-weight:800; margin-bottom:18px;">Historial de pagos</h2>

        <div class="table-responsive" style="background:#1b1b1b; padding:20px; border-left:5px solid #0b8f70; margin-bottom:40px;">
            <table class="table table-dark table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Sistema</th>
                        <th>Monto</th>
                        <th>Medio</th>
                        <th>Cobrado por</th>
                        <th>Comprobante</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($pagos)): ?>
                        <tr><td colspan="6" class="text-center">No hay pagos registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach($pagos as $pago): ?>
                            <tr>
                                <td><?= formatear_fecha($pago['fecha_pago']) ?></td>
                                <td><?= esc($pago['nombre_sistema']) ?></td>
                                <td><?= formatear_monto($pago['monto']) ?></td>
                                <td><?= esc($pago['medio_pago']) ?></td>
                                <td><?= esc($pago['cobrado_por']) ?></td>
                                <td>
                                    <a class="btn btn-sm btn-success"
                                       href="<?= base_url('cliente/comprobante/' . $pago['id_pago']) ?>"
                                       target="_blank">
                                        <i class="bi bi-file-earmark-pdf"></i> PDF
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <h2 style="font-size:26px; font-weight:800; margin-bottom:18px;">Mis asistencias</h2>

        <div class="table-responsive" style="background:#1b1b1b; padding:20px; border-left:5px solid #0b8f70;">
            <table class="table table-dark table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Registrado por</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($asistencias)): ?>
                        <tr><td colspan="3" class="text-center">Todavía no registraste ingresos.</td></tr>
                    <?php else: ?>
                        <?php foreach($asistencias as $asis): ?>
                            <tr>
                                <td><?= formatear_fecha($asis['fecha']) ?></td>
                                <td><?= formatear_hora($asis['hora']) ?></td>
                                <td><?= esc($asis['registrado_por'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
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
            if (e.key === "Escape") { cerrarSidebar(); }
        });
    }

    const toasts = document.querySelectorAll('.toast-custom');
    toasts.forEach((toast, index) => {
        setTimeout(() => { toast.style.opacity = "1"; toast.style.transform = "translateY(0)"; }, 100);
        setTimeout(() => {
            toast.style.opacity = "0";
            toast.style.transform = "translateY(-20px)";
            setTimeout(() => toast.remove(), 400);
        }, 2800 + (index * 200));
    });
</script>
