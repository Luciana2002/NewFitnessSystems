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

        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:25px;">
            <div>
                <h4 style="margin:0; color:#0b8f70; text-transform:uppercase; letter-spacing:1px;">Avisos de vencimiento</h4>
                <p style="margin:5px 0 0; color:#aaa; font-size:14px;">
                    Clientes cuya cuota vence en los próximos <strong><?= (int) $dias ?></strong> días.
                </p>
            </div>

            <form method="get" action="<?= base_url('comunicaciones/vencimientos') ?>"
                  style="display:flex; align-items:center; gap:15px; flex-wrap:wrap;">
                <label style="color:#cfcfcf; font-size:13px; text-transform:uppercase; letter-spacing:1px;">
                    Días
                    <input type="number" name="dias" min="1" max="60" value="<?= (int) $dias ?>"
                           style="background:#111; border:1px solid #262626; border-radius:6px; color:#fff; padding:8px 12px; width:80px; margin-left:6px;">
                </label>

                <label style="color:#cfcfcf; font-size:13px; display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" name="incluir_vencidas" value="1" <?= $incluirVencidas ? 'checked' : '' ?>>
                    Incluir ya vencidas
                </label>

                <button type="submit" class="btn btn-success btn-sm">Aplicar</button>
            </form>
        </div>

        <?php if (!empty($clientes)): ?>
            <div style="overflow-x:auto;">
                <table class="table table-dark table-hover" style="background:#111; border-radius:8px; overflow:hidden; min-width:880px;">
                    <thead>
                        <tr style="background:#262626; color:#fff; text-transform:uppercase; letter-spacing:1px; font-size:13px;">
                            <th style="padding:12px;">Cliente</th>
                            <th style="padding:12px;">Sistema</th>
                            <th style="padding:12px;">Vence</th>
                            <th style="padding:12px;">Estado</th>
                            <th style="padding:12px;">Correo</th>
                            <th style="padding:12px;">Contactar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $c): ?>
                            <?php
                                $fechaTxt = date('d/m/Y', strtotime($c['vencimiento']));
                                $dias = (int) $c['dias'];

                                if ($dias < 0) {
                                    $estado = 'Vencida hace ' . abs($dias) . ' días';
                                    $colorEstado = '#dc3545';
                                    $asunto = 'Tu cuota venció';
                                    $mensajeBase = 'Hola {nombre}! Notamos que tu cuota venció el {vencimiento}. Pasá por recepción o escribinos para regularizarla y seguir con tu rutina. Te esperamos!';
                                } elseif ($dias === 0) {
                                    $estado = 'Vence hoy';
                                    $colorEstado = '#ffc107';
                                    $asunto = 'Tu cuota vence hoy';
                                    $mensajeBase = 'Hola {nombre}! Hoy vence tu cuota ({vencimiento}). Renovala para seguir entrenando sin interrupciones. Cualquier consulta, escribinos!';
                                } else {
                                    $estado = 'En ' . $dias . ' ' . ($dias === 1 ? 'día' : 'días');
                                    $colorEstado = '#28a745';
                                    $asunto = 'Tu cuota está por vencer';
                                    $mensajeBase = 'Hola {nombre}! Te recordamos que tu cuota vence el {vencimiento}. Podés renovarla en recepción para seguir entrenando. Te esperamos!';
                                }

                                $mensaje = nfs_completar_plantilla($mensajeBase, [
                                    'nombre'      => $c['nombre'],
                                    'apellido'    => $c['apellido'],
                                    'vencimiento' => $fechaTxt,
                                ]);
                            ?>
                            <tr style="color:#ddd; border-bottom:1px solid #262626;">
                                <td style="padding:12px; overflow-wrap:anywhere;">
                                    <?= esc($c['nombre'] . ' ' . $c['apellido']) ?>
                                </td>
                                <td style="padding:12px; overflow-wrap:anywhere;"><?= esc($c['sistema']) ?></td>
                                <td style="padding:12px;"><?= esc($fechaTxt) ?></td>
                                <td style="padding:12px; color:<?= $colorEstado ?>; font-weight:700;"><?= esc($estado) ?></td>
                                <td style="padding:12px; overflow-wrap:anywhere;"><?= esc($c['email'] ?: '—') ?></td>
                                <td style="padding:12px;">
                                    <?php if (!empty($c['email'])): ?>
                                        <form method="post" action="<?= base_url('comunicaciones/enviar') ?>" style="display:inline;">
                                            <input type="hidden" name="id_persona" value="<?= (int) $c['id_persona'] ?>">
                                            <input type="hidden" name="volver" value="vencimientos">
                                            <input type="hidden" name="asunto" value="<?= esc($asunto, 'attr') ?>">
                                            <input type="hidden" name="mensaje" value="<?= esc($mensaje, 'attr') ?>">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bi bi-envelope"></i> Enviar mail
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color:#777;">Sin correo</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="dashboard-card" style="padding:30px; max-width:none;">
                <p style="color:#aaa; margin:0;">No hay clientes en este rango de vencimiento.</p>
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
</script>
