<style>
    .dashboard-content .form-control::placeholder,
    .dashboard-content textarea::placeholder {
        color: #ffffff;
        opacity: 0.75;
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

        <h1 style="margin:0 0 20px;">Comunicaciones</h1>

        <?= view('comunicaciones/_tabs', ['tabActiva' => $tabActiva]) ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" style="background:#0b8f70; border:none; color:#fff;">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?php if (!$emailOk): ?>
            <div class="alert alert-warning">
                Todavía no está configurado el correo. Cargá <code>email.SMTPUser</code>, <code>email.SMTPPass</code> y
                <code>email.fromEmail</code> en el archivo <code>.env</code> para poder enviar.
            </div>
        <?php endif; ?>

        <div class="row g-4">

            <!-- ============ PLANTILLAS ============ -->
            <div class="col-lg-4">
                <div class="dashboard-card" style="padding:25px; max-width:none; height:100%;">
                    <h4 style="margin:0 0 18px; color:#0b8f70; text-transform:uppercase; letter-spacing:1px; font-size:18px;">
                        Mensajes predefinidos
                    </h4>

                    <form method="post" action="<?= base_url('comunicaciones/plantilla/guardar') ?>">
                        <input type="text" name="titulo" class="form-control mb-2" required
                               placeholder="Asunto / título (ej: Promo del mes)"
                               style="background:#111; border:1px solid #262626; color:#fff;">

                        <select name="categoria" class="form-control mb-2"
                                style="background:#111; border:1px solid #262626; color:#fff;">
                            <option value="Vencimiento">Vencimiento</option>
                            <option value="Promo">Promo</option>
                            <option value="Retención">Retención</option>
                            <option value="Aviso">Aviso</option>
                            <option value="General">General</option>
                        </select>

                        <textarea name="mensaje" class="form-control mb-2" rows="3" required
                                  placeholder="Escribí el mensaje. Podés usar {nombre}, {vencimiento}, {dias}."
                                  style="background:#111; border:1px solid #262626; color:#fff;"></textarea>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-plus-lg"></i> Guardar plantilla
                        </button>
                    </form>

                    <hr style="border-color:#262626;">

                    <div style="max-height:360px; overflow-y:auto; display:flex; flex-direction:column; gap:10px;">
                        <?php if (!empty($plantillas)): ?>
                            <?php foreach ($plantillas as $p): ?>
                                <div style="background:#111; border:1px solid #262626; border-radius:8px; padding:12px;">
                                    <div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
                                        <div style="min-width:0;">
                                            <strong style="color:#fff; display:block; overflow-wrap:anywhere;"><?= esc($p['titulo']) ?></strong>
                                            <span style="color:#0b8f70; font-size:12px; text-transform:uppercase; letter-spacing:1px;"><?= esc($p['categoria']) ?></span>
                                        </div>
                                        <div style="display:flex; gap:6px; flex-shrink:0;">
                                            <button type="button" class="btn btn-sm btn-outline-light usar-plantilla"
                                                    data-titulo="<?= esc($p['titulo'], 'attr') ?>"
                                                    data-mensaje="<?= esc($p['mensaje'], 'attr') ?>">Usar</button>
                                            <a href="<?= base_url('comunicaciones/plantilla/eliminar/' . $p['id_plantilla']) ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('¿Eliminar esta plantilla?');">✕</a>
                                        </div>
                                    </div>
                                    <p style="margin:8px 0 0; color:#aaa; font-size:13px; overflow-wrap:anywhere;"><?= esc($p['mensaje']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color:#777; margin:0;">Todavía no hay plantillas guardadas.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ============ ENVÍO ============ -->
            <div class="col-lg-8">
                <div class="dashboard-card" style="padding:25px; max-width:none; height:100%;">
                    <h4 style="margin:0 0 18px; color:#0b8f70; text-transform:uppercase; letter-spacing:1px; font-size:18px;">
                        Armar y enviar
                    </h4>

                    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:15px;">
                        <?php
                            $grupos = [
                                'todos'      => 'Todos los clientes',
                                'vencen'     => 'Vencen esta semana',
                                'inactivos'  => 'Inactivos',
                                'individual' => 'Un cliente',
                            ];
                        ?>
                        <?php foreach ($grupos as $clave => $etiqueta): ?>
                            <a href="<?= base_url('comunicaciones/mensajes?grupo=' . $clave) ?>"
                               class="btn btn-sm <?= $grupo === $clave ? 'btn-success' : 'btn-outline-light' ?>">
                                <?= esc($etiqueta) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <form method="post" action="<?= base_url('comunicaciones/enviar-masivo') ?>"
                          id="formEnvio" onsubmit="return confirmarEnvio();">
                        <input type="hidden" name="grupo" value="<?= esc($grupo, 'attr') ?>">
                        <input type="hidden" name="id_cliente" value="<?= (int) $idCliente ?>">

                        <?php if ($grupo === 'individual'): ?>
                            <select id="selectorCliente" class="form-control mb-3"
                                    style="background:#111; border:1px solid #262626; color:#fff;"
                                    onchange="if(this.value){ location.href='<?= base_url('comunicaciones/mensajes?grupo=individual&id=') ?>' + this.value; }">
                                <option value="">— Elegí un cliente —</option>
                                <?php foreach ($clientes as $cl): ?>
                                    <?php if ((int) $cl['baja'] === 1) continue; ?>
                                    <option value="<?= (int) $cl['id_persona'] ?>" <?= $idCliente === (int) $cl['id_persona'] ? 'selected' : '' ?>>
                                        <?= esc($cl['apellido'] . ', ' . $cl['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>

                        <label style="color:#cfcfcf; font-size:13px; text-transform:uppercase; letter-spacing:1px;">Asunto</label>
                        <input type="text" id="asuntoTexto" name="asunto" class="form-control mb-2" required
                               placeholder="Asunto del correo"
                               style="background:#111; border:1px solid #262626; color:#fff;">

                        <label style="color:#cfcfcf; font-size:13px; text-transform:uppercase; letter-spacing:1px;">Mensaje</label>
                        <textarea id="mensajeTexto" name="mensaje" rows="5" class="form-control mb-2" required
                                  placeholder="Elegí una plantilla o escribí tu mensaje. Marcadores: {nombre}, {apellido}, {vencimiento}, {dias}"
                                  style="background:#111; border:1px solid #262626; color:#fff;"></textarea>
                        <p style="color:#777; font-size:13px; margin:0 0 15px;">
                            Marcadores disponibles: <code>{nombre}</code>, <code>{apellido}</code>, <code>{vencimiento}</code>, <code>{dias}</code>.
                            Se personalizan por cliente al enviar.
                        </p>

                        <div style="display:flex; justify-content:space-between; align-items:center; gap:15px; flex-wrap:wrap; margin-bottom:12px;">
                            <strong style="color:#fff;">
                                Destinatarios: <span style="color:#0b8f70;"><?= count($destinatarios) ?></span>
                            </strong>
                            <button type="submit" class="btn btn-success" <?= (!$emailOk || empty($destinatarios)) ? 'disabled' : '' ?>>
                                <i class="bi bi-envelope-fill"></i> Enviar a <?= count($destinatarios) ?> destinatario<?= count($destinatarios) === 1 ? '' : 's' ?>
                            </button>
                        </div>
                    </form>

                    <div style="max-height:380px; overflow-y:auto; display:flex; flex-direction:column; gap:8px;">
                        <?php if (!empty($destinatarios)): ?>
                            <?php foreach ($destinatarios as $dest): ?>
                                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; background:#111; border:1px solid #262626; border-radius:8px; padding:10px 14px;">
                                    <div style="min-width:0;">
                                        <strong style="color:#fff; display:block; overflow-wrap:anywhere;">
                                            <?= esc($dest['nombre'] . ' ' . $dest['apellido']) ?>
                                        </strong>
                                        <span style="color:#888; font-size:13px; overflow-wrap:anywhere;">
                                            <?= esc($dest['email'] ?: 'sin correo') ?>
                                        </span>
                                    </div>
                                    <?php if (empty($dest['email'])): ?>
                                        <span style="color:#777; flex-shrink:0;">Sin correo</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color:#777; margin:0;">No hay destinatarios para este grupo.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </section>

</main>

<script>
    document.querySelectorAll('.usar-plantilla').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const asunto = document.getElementById('asuntoTexto');
            const mensaje = document.getElementById('mensajeTexto');

            if (asunto) asunto.value = btn.getAttribute('data-titulo') || '';
            if (mensaje) {
                mensaje.value = btn.getAttribute('data-mensaje') || '';
                mensaje.focus();
            }
        });
    });

    function confirmarEnvio() {
        const total = <?= (int) count($destinatarios) ?>;
        if (total === 0) return false;
        return confirm('¿Enviar el correo a ' + total + ' destinatario(s)? Esta acción no se puede deshacer.');
    }
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
