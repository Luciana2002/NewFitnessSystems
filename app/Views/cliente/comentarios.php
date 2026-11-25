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
        <p class="reporte-metrica-help">Contanos qué te gustaría mejorar o dejanos tu opinión.</p>

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

        <div class="comentario-layout">

            <!-- ===================== FORMULARIO ===================== -->
            <div class="dashboard-card reporte-card comentario-formulario">
                <div class="panel-card-head">
                    <h2><i class="bi bi-chat-left-text"></i> Enviar mensaje</h2>
                </div>

                <form method="post" action="<?= base_url('cliente/comentarios/enviar') ?>">
                    <div class="comentario-campos">
                        <div class="comentario-campo">
                            <label for="tipoComentario">Tipo de mensaje</label>
                            <select id="tipoComentario" name="tipo" class="comentario-select">
                                <?php foreach ($tipos as $tipo): ?>
                                    <option value="<?= esc($tipo) ?>"><?= esc($tipo) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="comentario-campo comentario-campo-ancho">
                            <label for="mensajeComentario">Tu mensaje</label>
                            <textarea id="mensajeComentario" name="mensaje" class="comentario-textarea"
                                      maxlength="1000" required
                                      placeholder="Escribí aquí tu sugerencia, comentario o reclamo..."></textarea>
                        </div>
                    </div>

                    <div class="comentario-acciones">
                        <button type="submit" class="nfs-btn nfs-btn-primary">
                            <i class="bi bi-send"></i> Enviar
                        </button>
                        <span id="contadorMensaje" class="comentario-contador">0/1000 caracteres</span>
                    </div>
                </form>
            </div>

            <!-- ===================== MIS MENSAJES ===================== -->
            <div class="dashboard-card reporte-card">
                <div class="panel-card-head">
                    <h2><i class="bi bi-list-check"></i> Mis mensajes</h2>
                </div>

                <?php if (! empty($comentarios)): ?>
                    <div class="comentario-lista">
                        <?php foreach ($comentarios as $comentario): ?>
                            <?php
                                $leido = (int) ($comentario['leido'] ?? 0) === 1;
                                $fecha = ! empty($comentario['fecha_registro'])
                                    ? date('d/m/Y H:i', strtotime($comentario['fecha_registro']))
                                    : '—';
                            ?>
                            <article class="comentario-item">
                                <div class="comentario-item-head">
                                    <div class="comentario-item-meta">
                                        <span class="status-pill <?= $leido ? 'is-ok' : 'is-muted' ?>">
                                            <?= $leido ? 'Leído por administración' : 'Enviado' ?>
                                        </span>
                                        <span><?= esc($fecha) ?></span>
                                    </div>
                                    <span class="status-pill is-info"><?= esc($comentario['tipo']) ?></span>
                                </div>
                                <p class="comentario-item-mensaje"><?= esc($comentario['mensaje']) ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="panel-empty">Todavía no enviaste mensajes.</div>
                <?php endif; ?>
            </div>

        </div>

    </section>

</main>

<script>
    const mensajeComentario = document.getElementById('mensajeComentario');
    const contadorMensaje = document.getElementById('contadorMensaje');

    if (mensajeComentario && contadorMensaje) {
        mensajeComentario.addEventListener('input', function () {
            contadorMensaje.textContent = mensajeComentario.value.length + '/1000 caracteres';
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
