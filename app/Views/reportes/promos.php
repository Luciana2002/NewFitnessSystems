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

        <a href="<?= base_url('suscripciones') ?>" style="display:inline-block; margin-bottom:25px; color:#aaa; font-size:14px; text-decoration:none;">
            ← Volver a reportes
        </a>

        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:20px;">
            <h1 style="margin:0;">Recomendaciones de Promos</h1>
        </div>

        <div class="dashboard-card" style="padding:25px; margin-bottom:30px; border-left:5px solid #4a6fa5; max-width:none;">
            <h4 style="margin:0 0 12px; text-transform:uppercase; letter-spacing:1px; color:#7d9fc4; font-size:16px;">
                ¿Cómo se calcula cada recomendación?
            </h4>

            <div style="display:flex; gap:20px; flex-direction:column;">
                <div style="display:flex; gap:12px; align-items:flex-start;">
                    <span style="background:#ffc107; color:#111; font-weight:700; font-size:11px; text-transform:uppercase; letter-spacing:1px; padding:4px 10px; border-radius:20px; white-space:nowrap;">Sistema estrella</span>
                    <p style="margin:0; color:#ddd; font-size:14px;">
                        Es el sistema con la mayor cantidad de suscripciones activas de todo el negocio.
                        Al tener mucha demanda, conviene promocionarlo para atraer más clientes.
                    </p>
                </div>

                <div style="display:flex; gap:12px; align-items:flex-start;">
                    <span style="background:#28a745; color:#111; font-weight:700; font-size:11px; text-transform:uppercase; letter-spacing:1px; padding:4px 10px; border-radius:20px; white-space:nowrap;">Subir precio</span>
                    <p style="margin:0; color:#ddd; font-size:14px;">
                        Tiene 5 o más suscripciones activas y menos del 15% de sus cuotas vencidas:
                        la alta demanda y el buen cobro indican que hay margen para subir el precio sin perder clientes.
                    </p>
                </div>

                <div style="display:flex; gap:12px; align-items:flex-start;">
                    <span style="background:#dc3545; color:#111; font-weight:700; font-size:11px; text-transform:uppercase; letter-spacing:1px; padding:4px 10px; border-radius:20px; white-space:nowrap;">Retención</span>
                    <p style="margin:0; color:#ddd; font-size:14px;">
                        El 40% o más de sus suscripciones están vencidas: hay riesgo de que los clientes se vayan,
                        así que conviene ofrecer una promo para recuperarlos.
                    </p>
                </div>

                <div style="display:flex; gap:12px; align-items:flex-start;">
                    <span style="background:#0b8f70; color:#111; font-weight:700; font-size:11px; text-transform:uppercase; letter-spacing:1px; padding:4px 10px; border-radius:20px; white-space:nowrap;">Captación</span>
                    <p style="margin:0; color:#ddd; font-size:14px;">
                        Tiene menos de 3 suscripciones activas: la demanda es baja,
                        así que conviene lanzar una promo para atraer nuevos clientes.
                    </p>
                </div>
            </div>
        </div>

        <?php
            $colores = [
                'estrella'     => '#ffc107',
                'subir-precio' => '#28a745',
                'retencion'    => '#dc3545',
                'captacion'    => '#0b8f70'
            ];
            $badges = [
                'estrella'     => 'Sistema estrella',
                'subir-precio' => 'Candidato a subir precio',
                'retencion'    => 'Promo de retención',
                'captacion'    => 'Promo de captación'
            ];
        ?>

        <?php if (!empty($recomendaciones)): ?>
            <div class="row g-4">
                <?php foreach ($recomendaciones as $rec): ?>
                    <?php $color = $colores[$rec['tipo']] ?? '#0b8f70'; ?>
                    <div class="col-md-6">
                        <div class="dashboard-card" style="padding:20px; border-left:4px solid <?= $color ?>; height:100%; max-width:none;">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px; margin-bottom:8px; flex-wrap:wrap;">
                                <span style="background:<?= $color ?>; color:#111; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:1px; padding:4px 10px; border-radius:20px;">
                                    <?= $badges[$rec['tipo']] ?? 'Recomendación' ?>
                                </span>
                                <span style="color:#cfcfcf; font-weight:700; text-transform:uppercase; overflow-wrap:anywhere;"><?= esc($rec['sistema']) ?></span>
                            </div>

                            <p style="color:#ddd; margin-bottom:12px;"><?= esc($rec['mensaje']) ?></p>

                            <div style="display:flex; gap:15px; font-size:13px; color:#aaa; flex-wrap:wrap;">
                                <span>Precio actual: <strong style="color:white;">$<?= number_format($rec['precio'], 0, ',', '.') ?></strong></span>
                                <span>Suscripciones: <strong style="color:white;"><?= $rec['activos'] ?></strong></span>
                                <span>Vencidas: <strong style="color:white;"><?= $rec['vencidos'] ?></strong></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color:#aaa;">Aún no hay recomendaciones para mostrar.</p>
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