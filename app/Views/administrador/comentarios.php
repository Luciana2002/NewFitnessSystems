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

        <h1>Sugerencias y comentarios</h1>
        <p class="reporte-metrica-help">Mensajes enviados por los socios a través del buzón.</p>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success comentario-alert">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger comentario-alert">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <div class="reporte-metricas">
            <div class="dashboard-card reporte-metrica es-alerta">
                <h6 class="reporte-metrica-label">Sin leer</h6>
                <div class="reporte-metrica-valor"><?= (int) ($noLeidos ?? 0) ?></div>
                <div class="reporte-metrica-help">Mensajes pendientes de revisión</div>
            </div>

            <div class="dashboard-card reporte-metrica es-info">
                <h6 class="reporte-metrica-label">En el filtro actual</h6>
                <div class="reporte-metrica-valor"><?= count($comentarios ?? []) ?></div>
                <div class="reporte-metrica-help">Resultados mostrados en la tabla</div>
            </div>
        </div>

        <div class="dashboard-card reporte-card">
            <div class="panel-card-head">
                <h2><i class="bi bi-chat-left-text"></i> Buzón</h2>

                <form method="get" action="<?= base_url('comentarios') ?>" class="reporte-fechas"
                      id="formFiltroComentarios">
                    <label for="filtroComentarios">Mostrar</label>
                    <select id="filtroComentarios" name="filtro" class="reporte-fecha-input">
                        <option value="no_leidos" <?= ($filtro ?? 'todos') === 'no_leidos' ? 'selected' : '' ?>>
                            No leídos
                        </option>
                        <option value="todos" <?= ($filtro ?? '') === 'todos' ? 'selected' : '' ?>>
                            Todos
                        </option>
                        <?php foreach ($tipos ?? [] as $tipo): ?>
                            <option value="<?= esc(strtolower($tipo)) ?>"
                                <?= ($filtro ?? '') === strtolower($tipo) ? 'selected' : '' ?>>
                                <?= esc($tipo) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="nfs-btn nfs-btn-secondary nfs-btn-sm">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                </form>
            </div>

            <?php if (! empty($comentarios)): ?>
                <div class="table-responsive reporte-tabla-wrap">
                    <table class="table table-dark table-hover reporte-tabla">
                        <thead>
                            <tr>
                                <th>Socio</th>
                                <th>Tipo</th>
                                <th>Mensaje</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($comentarios as $comentario): ?>
                                <?php
                                    $leido = (int) ($comentario['leido'] ?? 0) === 1;
                                    $socio = trim(($comentario['nombre'] ?? '') . ' ' . ($comentario['apellido'] ?? ''));
                                    $fecha = ! empty($comentario['fecha_registro'])
                                        ? date('d/m/Y H:i', strtotime($comentario['fecha_registro']))
                                        : '—';
                                ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($socio !== '' ? $socio : '—') ?></strong>
                                        <div class="reporte-metrica-help">
                                            DNI <?= esc($comentario['dni'] ?? '—') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-pill is-info"><?= esc($comentario['tipo']) ?></span>
                                    </td>
                                    <td><?= esc($comentario['mensaje']) ?></td>
                                    <td><?= esc($fecha) ?></td>
                                    <td>
                                        <span class="status-pill <?= $leido ? 'is-ok' : 'is-warn' ?>">
                                            <?= $leido ? 'Leído' : 'Sin leer' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (! $leido): ?>
                                            <form method="post"
                                                  action="<?= base_url('comentarios/leido/' . (int) $comentario['id_comentario']) ?>"
                                                  class="comentario-item-accion">
                                                <input type="hidden" name="filtro" value="<?= esc($filtro ?? 'todos') ?>">
                                                <button type="submit" class="nfs-btn nfs-btn-primary nfs-btn-sm">
                                                    <i class="bi bi-check2"></i> Marcar leído
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="panel-empty">No hay mensajes para el filtro seleccionado.</div>
            <?php endif; ?>
        </div>

    </section>

</main>

<script>
    const filtroComentarios = document.getElementById('filtroComentarios');

    if (filtroComentarios) {
        filtroComentarios.addEventListener('change', function () {
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
