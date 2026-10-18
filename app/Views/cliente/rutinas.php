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

        <h1 style="margin-bottom:8px;">Mis rutinas</h1>
        <p style="color:#9a9a9a; margin-bottom:25px;">
            Tu plan semanal. Se muestra automáticamente el entrenamiento del día de hoy.
        </p>

        <div class="rutina-dias" style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:28px;">
            <?php foreach($dias as $num => $etiqueta): ?>
                <button type="button"
                        class="btn <?= (int) $num === (int) $diaActual ? 'btn-success' : 'btn-outline-light' ?> btn-sm btn-dia"
                        data-dia="<?= (int) $num ?>">
                    <?= esc($etiqueta) ?><?= (int) $num === (int) $diaActual ? ' (hoy)' : '' ?>
                </button>
            <?php endforeach; ?>
        </div>

        <?php foreach($dias as $num => $etiqueta): ?>
            <div class="panel-dia" data-dia="<?= (int) $num ?>" style="display:<?= (int) $num === (int) $diaActual ? 'block' : 'none' ?>;">

                <h2 style="font-size:20px; font-weight:800; margin-bottom:18px;">
                    <?= esc($etiqueta) ?><?= (int) $num === (int) $diaActual ? ' — Hoy' : '' ?>
                </h2>

                <?php $lista = $rutinasPorDia[$num] ?? []; ?>

                <?php if(empty($lista)): ?>
                    <div class="dashboard-card" style="max-width:none; width:100%;">
                        No tenés rutina asignada para este día.
                    </div>
                <?php else: ?>
                    <?php foreach($lista as $rutina): ?>
                        <div class="dashboard-card" style="max-width:none; width:100%; margin-bottom:35px;">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:20px; flex-wrap:wrap; margin-bottom:18px;">
                                <div>
                                    <div style="font-size:24px; font-weight:800; margin-bottom:6px;"><?= esc($rutina['nombre']) ?></div>
                                    <div style="color:#9a9a9a; font-size:14px;">
                                        <?php if((int) $rutina['para_todos'] === 1): ?>
                                            <span class="badge bg-success">Rutina general</span>
                                        <?php else: ?>
                                            Profesor: <?= esc(trim(($rutina['profesor_nombre'] ?? '') . ' ' . ($rutina['profesor_apellido'] ?? ''))) ?: '-' ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <?php if(!empty($rutina['descripcion'])): ?>
                                <p style="margin-bottom:18px;"><?= nl2br(esc($rutina['descripcion'])) ?></p>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-dark table-striped table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width:40px;">#</th>
                                            <th>Ejercicio</th>
                                            <th>Series</th>
                                            <th>Repeticiones</th>
                                            <th>Peso</th>
                                            <th>Descanso</th>
                                            <th>Observación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($rutina['ejercicios'])): ?>
                                            <tr><td colspan="7" class="text-center">Sin ejercicios cargados.</td></tr>
                                        <?php else: ?>
                                            <?php foreach($rutina['ejercicios'] as $i => $ej): ?>
                                                <tr>
                                                    <td><?= $i + 1 ?></td>
                                                    <td style="font-weight:600;"><?= esc($ej['nombre']) ?></td>
                                                    <td><?= esc($ej['series'] ?? '-') ?></td>
                                                    <td><?= esc($ej['repeticiones'] ?? '-') ?></td>
                                                    <td><?= esc($ej['peso'] ?? '-') ?></td>
                                                    <td><?= esc($ej['descanso'] ?? '-') ?></td>
                                                    <td><?= esc($ej['observacion'] ?? '-') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>

    </section>

</main>

<script>
    var botonesDia = document.querySelectorAll('.btn-dia');
    var panelesDia = document.querySelectorAll('.panel-dia');

    botonesDia.forEach(function (boton) {
        boton.addEventListener('click', function () {
            var dia = boton.getAttribute('data-dia');

            botonesDia.forEach(function (b) {
                var esActivo = b.getAttribute('data-dia') === dia;
                b.classList.toggle('btn-success', esActivo);
                b.classList.toggle('btn-outline-light', !esActivo);
            });

            panelesDia.forEach(function (p) {
                p.style.display = p.getAttribute('data-dia') === dia ? 'block' : 'none';
            });
        });
    });

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
</script>
