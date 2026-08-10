<main class="dashboard-page">

    <!-- BOTÓN LATERAL -->
    <button type="button" id="sidebarOpen" class="sidebar-handle">
        <span>›</span>
    </button>

    <button type="button" id="sidebarClose" class="sidebar-close">
        ✕
    </button>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <?= view('layout/sidebar') ?>

    <section class="dashboard-content" style="padding:50px 70px;">

        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:30px;">
            <div style="display:flex; align-items:center; gap:15px;">
                <a href="<?= base_url(empty($esProfesor) ? 'clientes' : 'profesores') ?>"
                   class="btn btn-outline-success btn-sm">← Volver</a>
                <h1 style="margin:0;">
                    <?= esc($cliente['nombre']) ?> <?= esc($cliente['apellido']) ?>
                </h1>
            </div>

            <?php if($cliente['baja'] == 'S'): ?>
                <span class="badge bg-danger" style="font-size:14px;">Dado de baja</span>
            <?php else: ?>
                <span class="badge bg-success" style="font-size:14px;">Activo</span>
            <?php endif; ?>
        </div>

        <!-- ALERTAS -->
        <?php if(session()->getFlashdata('success')): ?>
            <div class="toast-custom success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="toast-custom error">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <!-- DATOS PERSONALES -->
        <div class="dashboard-card" style="max-width:100%; padding:30px; margin-bottom:25px;">
            <h4 style="margin-bottom:20px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                Datos personales
            </h4>

            <div class="row">
                <div class="col-md-4 mb-2">
                    <strong>DNI:</strong>
                    <?= esc($cliente['dni']) ?>
                </div>

                <div class="col-md-4 mb-2">
                    <strong>Nombre:</strong>
                    <?= esc($cliente['nombre']) ?>
                </div>

                <div class="col-md-4 mb-2">
                    <strong>Apellido:</strong>
                    <?= esc($cliente['apellido']) ?>
                </div>

                <div class="col-md-4 mb-2">
                    <strong>Email:</strong>
                    <?= esc($cliente['email'] ?? 'Sin email') ?>
                </div>

                <div class="col-md-4 mb-2">
                    <strong>Teléfono:</strong>
                    <?= esc($cliente['telefono']) ?>
                </div>

                <div class="col-md-4 mb-2">
                    <strong>Rol:</strong>
                    <?= esc($cliente['rol']) ?>
                </div>

                <div class="col-md-4 mb-2">
                    <strong>Usuario:</strong>
                    <?= !empty($cliente['nombre_usuario']) ? esc($cliente['nombre_usuario']) : 'No tiene usuario registrado' ?>
                </div>

                <div class="col-md-4 mb-2">
                    <strong>Estado:</strong>
                    <?= $cliente['baja'] == 'S' ? 'Dado de baja' : 'Activo' ?>
                </div>
            </div>
        </div>

        <!-- SUSCRIPCIONES -->
        <?php if(empty($esProfesor)): ?>
        <div class="table-responsive" style="background:#1b1b1b; padding:25px; border-left:5px solid #0b8f70; overflow-x:auto; margin-bottom:25px;">
            <h4 style="margin-bottom:20px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                Suscripciones / Sistemas
            </h4>

            <table class="table table-dark table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Sistema</th>
                        <th>Último pago</th>
                        <th>Inicio suscripción</th>
                        <th>Vencimiento</th>
                        <th>Estado</th>
                        <th>Mensualidad</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if(!empty($suscripciones)): ?>
                        <?php foreach($suscripciones as $suscripcion): ?>
                            <?php
                                $estadoSus = $suscripcion['estado_suscripcion'] ?? 'Sin suscripción';

                                $susBadge = 'secondary';

                                if ($estadoSus !== 'Cancelada' && $estadoSus !== 'Sin suscripción') {
                                    $tsVenc = !empty($suscripcion['fecha_vencimiento'])
                                        ? strtotime((string) $suscripcion['fecha_vencimiento'])
                                        : null;

                                    if ($tsVenc && $tsVenc < strtotime(date('Y-m-d'))) {
                                        $estadoSus = 'Vencida';
                                        $susBadge  = 'danger';
                                    } else {
                                        $estadoSus = 'Activa';
                                        $susBadge  = 'success';
                                    }
                                }
                            ?>
                            <tr>
                                <td><?= esc($suscripcion['nombre_sistema']) ?></td>
                                <td>
                                    <?= !empty($suscripcion['ultimo_pago_sistema'])
                                        ? formatear_fecha($suscripcion['ultimo_pago_sistema'])
                                        : '<span style="color:#d98b45;">Sin pagos</span>' ?>
                                </td>
                                <td><?= formatear_fecha($suscripcion['fecha_inicio']) ?></td>
                                <td><?= formatear_fecha($suscripcion['fecha_vencimiento']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $susBadge ?>">
                                        <?= esc($estadoSus) ?>
                                    </span>
                                </td>
                                <td><?= formatear_monto($suscripcion['precio_mensualidad']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">El cliente no tiene suscripciones registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- PAGOS -->
        <?php if(empty($esProfesor)): ?>
        <div class="table-responsive" style="background:#1b1b1b; padding:25px; border-left:5px solid #0b8f70; overflow-x:auto;">
            <h4 style="margin-bottom:20px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                Historial de pagos
            </h4>

            <table class="table table-dark table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Sistema</th>
                        <th>Monto</th>
                        <th>Medio de pago</th>
                        <th>Periodo</th>
                        <th>Estado</th>
                        <th>Cobrado por</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if(!empty($pagos)): ?>
                        <?php foreach($pagos as $pago): ?>
                            <tr>
                                <td><?= formatear_fecha($pago['fecha_pago']) ?></td>
                                <td><?= esc($pago['nombre_sistema']) ?></td>
                                <td><?= formatear_monto($pago['monto']) ?></td>
                                <td><?= esc($pago['medio_pago']) ?></td>
                                <td>
                                    <?= formatear_fecha($pago['fecha_inicio']) ?> — <?= formatear_fecha($pago['fecha_vencimiento']) ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= ($pago['estado_suscripcion'] == 'Activa') ? 'success' : (($pago['estado_suscripcion'] == 'Vencida') ? 'danger' : 'warning') ?>">
                                        <?= esc($pago['estado_suscripcion']) ?>
                                    </span>
                                </td>
                                <td><?= esc($pago['cobrado_por']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">El cliente no tiene pagos registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
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

    const toasts = document.querySelectorAll('.toast-custom');

    toasts.forEach((toast, index) => {
        setTimeout(() => {
            toast.style.transition = "all 0.4s ease";
            toast.style.opacity = "1";
            toast.style.transform = "translateY(0)";
        }, 100);

        setTimeout(() => {
            toast.style.opacity = "0";
            toast.style.transform = "translateY(-20px)";
            setTimeout(() => toast.remove(), 400);
        }, 2500 + (index * 200));
    });
</script>
