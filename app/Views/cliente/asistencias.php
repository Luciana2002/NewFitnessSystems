<main class="dashboard-page">

    <button type="button" id="sidebarOpen" class="sidebar-handle">
        <span>›</span>
    </button>

    <button type="button" id="sidebarClose" class="sidebar-close">
        ✕
    </button>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <?= view('layout/sidebar') ?>

    <section class="dashboard-content">

        <h1>Mis Asistencias</h1>
        <p class="reporte-metrica-help">Historial de ingresos registrados en el gimnasio.</p>

        <div class="reporte-metricas">
            <div class="dashboard-card reporte-metrica es-ok">
                <h6 class="reporte-metrica-label">Entrenamientos este mes</h6>
                <div class="reporte-metrica-valor"><?= (int) ($resumen['cantidad_mes'] ?? 0) ?></div>
                <div class="reporte-metrica-help">Ingresos aprobados en el mes actual</div>
            </div>

            <div class="dashboard-card reporte-metrica es-info">
                <h6 class="reporte-metrica-label">Última asistencia</h6>
                <div class="reporte-metrica-valor pequeno">
                    <?php if (! empty($resumen['ultima'])): ?>
                        <?= esc(date('d/m/Y H:i', strtotime($resumen['ultima']))) ?>
                    <?php else: ?>
                        Sin registros
                    <?php endif; ?>
                </div>
                <div class="reporte-metrica-help">Último ingreso aprobado</div>
            </div>
        </div>

        <div class="dashboard-card reporte-card">
            <div class="panel-card-head">
                <h2><i class="bi bi-clock-history"></i> Historial</h2>

                <form method="get" action="<?= base_url('cliente/asistencias') ?>" class="reporte-fechas">
                    <label for="filtroPeriodo">Mes</label>
                    <select id="filtroPeriodo" name="periodo" class="reporte-fecha-input">
                        <option value="todos" <?= $periodo === 'todos' ? 'selected' : '' ?>>Todos</option>
                        <?php foreach ($meses as $opcion): ?>
                            <option value="<?= esc($opcion['valor']) ?>" <?= $periodo === $opcion['valor'] ? 'selected' : '' ?>>
                                <?= esc($opcion['etiqueta']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <?php if (! empty($asistencias)): ?>
                <div class="table-responsive reporte-tabla-wrap">
                    <table class="table table-dark table-hover reporte-tabla">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Disciplina / Detalle</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($asistencias as $asistencia): ?>
                                <?php
                                    $claseEstado = match ($asistencia['resultado']) {
                                        'OK'       => 'is-ok',
                                        'VENCIDA'  => 'is-danger',
                                        default    => 'is-muted',
                                    };
                                ?>
                                <tr>
                                    <td>
                                        <?= ! empty($asistencia['fecha'])
                                            ? esc(date('d/m/Y', strtotime($asistencia['fecha'])))
                                            : '—' ?>
                                    </td>
                                    <td><?= esc(substr((string) $asistencia['hora'], 0, 5)) ?></td>
                                    <td>
                                        <?= esc($asistencia['observacion'] !== ''
                                            ? $asistencia['observacion']
                                            : '—') ?>
                                    </td>
                                    <td>
                                        <span class="status-pill <?= $claseEstado ?>">
                                            <?= esc($asistencia['resultado']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="panel-empty">No hay asistencias para el período seleccionado.</div>
            <?php endif; ?>
        </div>

    </section>

</main>

<script>
    const filtroPeriodo = document.getElementById('filtroPeriodo');

    if (filtroPeriodo) {
        filtroPeriodo.addEventListener('change', function () {
            this.form.submit();
        });
    }

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
        document.addEventListener('keydown', function (evento) {
            if (evento.key === 'Escape') {
                cerrarSidebar();
            }
        });
    }
</script>
