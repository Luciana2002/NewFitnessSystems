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

        <h1 style="margin-bottom:25px;">Reservar clases</h1>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="toast-custom success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="toast-custom error"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <?php if(!$membresia['activo']): ?>
            <div class="dashboard-card" style="max-width:none; width:100%; border-left-color:#c0392b; margin-bottom:30px; background:#3a1d1d;">
                <strong style="font-size:18px;"><i class="bi bi-exclamation-triangle"></i> Tu cuota está vencida</strong>
                <p style="margin:8px 0 0;">Regularizá tu pago para poder reservar clases. Podés ver el estado en <a href="<?= base_url('cliente/suscripcion') ?>" style="color:#14ffc0;">Mi suscripción</a>.</p>
            </div>
        <?php endif; ?>

        <h2 style="font-size:26px; font-weight:800; margin-bottom:18px;">Próximas clases (7 días)</h2>

        <?php if(empty($ocurrencias)): ?>
            <div class="dashboard-card" style="max-width:none; margin-bottom:40px;">No hay clases programadas.</div>
        <?php else: ?>
            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:18px; margin-bottom:45px;">
                <?php foreach($ocurrencias as $oc): ?>
                    <?php
                    $color    = esc($oc['color'] ?? '#0b8f70');
                    $mi       = $oc['mi_reserva'];
                    $porcentaje = $oc['cupo'] > 0 ? min(100, round($oc['ocupado'] / $oc['cupo'] * 100)) : 0;
                    ?>
                    <div class="dashboard-card" style="max-width:none; padding:0; overflow:hidden; border-left-color:<?= $color ?>;">
                        <div style="background:<?= $color ?>; padding:12px 18px; color:#fff; font-weight:800; display:flex; justify-content:space-between; align-items:center;">
                            <span><?= esc($oc['nombre_sistema']) ?></span>
                            <?php if($oc['es_hoy']): ?><span class="badge bg-dark">HOY</span><?php endif; ?>
                        </div>
                        <div style="padding:18px;">
                            <div style="font-size:15px; margin-bottom:6px;"><i class="bi bi-calendar-event"></i> <?= esc($oc['dia_semana']) ?> <?= esc($oc['fecha_mostrar']) ?></div>
                            <div style="font-size:15px; margin-bottom:12px;"><i class="bi bi-clock"></i> <?= formatear_hora($oc['hora_inicio']) ?> a <?= formatear_hora($oc['hora_fin']) ?></div>

                            <div style="font-size:13px; color:#9a9a9a; margin-bottom:6px;">
                                <?= $oc['ocupado'] ?>/<?= $oc['cupo'] ?> anotados &mdash;
                                <?php if($oc['completo']): ?>
                                    <span style="color:#e6b800;">Completo (lista de espera)</span>
                                <?php else: ?>
                                    <?= $oc['libres'] ?> lugar<?= $oc['libres'] == 1 ? '' : 'es' ?> libre<?= $oc['libres'] == 1 ? '' : 's' ?>
                                <?php endif; ?>
                            </div>

                            <div style="height:6px; background:#333; border-radius:4px; overflow:hidden; margin-bottom:16px;">
                                <div style="height:100%; width:<?= $porcentaje ?>%; background:<?= $oc['completo'] ? '#e6b800' : $color ?>;"></div>
                            </div>

                            <?php if($mi): ?>
                                <div style="margin-bottom:10px;">
                                    <?php if($mi['estado'] === 'RESERVADA'): ?>
                                        <span class="badge bg-success">Reserva confirmada</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">En lista de espera</span>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= base_url('cliente/cancelar_reserva/' . $mi['id_reserva']) ?>"
                                   class="btn btn-sm btn-outline-danger w-100"
                                   onclick="return confirm('¿Cancelar tu reserva?');">
                                    Cancelar
                                </a>
                            <?php else: ?>
                                <form action="<?= base_url('cliente/reservar') ?>" method="post">
                                    <input type="hidden" name="id_horario" value="<?= $oc['id_horario'] ?>">
                                    <input type="hidden" name="fecha" value="<?= esc($oc['fecha']) ?>">
                                    <button type="submit" class="btn btn-sm btn-success w-100" <?= !$membresia['activo'] ? 'disabled' : '' ?>>
                                        <?= $oc['completo'] ? 'Anotarme en lista de espera' : 'Reservar lugar' ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h2 style="font-size:26px; font-weight:800; margin-bottom:18px;">Mis reservas</h2>

        <div class="table-responsive" style="background:#1b1b1b; padding:20px; border-left:5px solid #0b8f70;">
            <table class="table table-dark table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Sistema</th>
                        <th>Horario</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($misReservas)): ?>
                        <tr><td colspan="5" class="text-center">No tenés reservas.</td></tr>
                    <?php else: ?>
                        <?php foreach($misReservas as $res): ?>
                            <tr>
                                <td><?= formatear_fecha($res['fecha']) ?></td>
                                <td><?= esc($res['nombre_sistema']) ?></td>
                                <td><?= esc($res['dia_semana']) ?> <?= formatear_hora($res['hora_inicio']) ?>-<?= formatear_hora($res['hora_fin']) ?></td>
                                <td>
                                    <?php
                                    $mapEstado = [
                                        'RESERVADA'    => ['bg-success', 'Reservada'],
                                        'LISTA_ESPERA' => ['bg-warning text-dark', 'Lista de espera'],
                                        'CANCELADA'    => ['bg-secondary', 'Cancelada'],
                                        'ATENDIDA'     => ['bg-primary', 'Asistió'],
                                    ];
                                    [$clase, $texto] = $mapEstado[$res['estado']] ?? ['bg-secondary', $res['estado']];
                                    ?>
                                    <span class="badge <?= $clase ?>"><?= $texto ?></span>
                                </td>
                                <td>
                                    <?php if(in_array($res['estado'], ['RESERVADA', 'LISTA_ESPERA'])): ?>
                                        <a href="<?= base_url('cliente/cancelar_reserva/' . $res['id_reserva']) ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('¿Cancelar tu reserva?');">Cancelar</a>
                                    <?php endif; ?>
                                </td>
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
