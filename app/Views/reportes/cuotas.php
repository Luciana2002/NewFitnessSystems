<?= view('reportes/_print_estilos') ?>

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

        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:30px;">
            <h1 style="margin:0;">Reporte de Cuotas</h1>
            <?= view('reportes/_acciones', ['exportUrl' => base_url('suscripciones/exportar/cuotas')]) ?>
        </div>

        <div style="display:flex; gap:15px; flex-wrap:wrap; margin-bottom:30px;">

            <div class="dashboard-card" style="flex:1; min-width:170px; padding:20px;">
                <h6 style="text-transform:uppercase; letter-spacing:1px; color:#0b8f70;">Total de clientes</h6>
                <h2 style="margin:5px 0 0;"><?= $total ?></h2>
            </div>

            <div class="dashboard-card" style="flex:1; min-width:170px; padding:20px;">
                <h6 style="text-transform:uppercase; letter-spacing:1px; color:#0b8f70;">Cuotas al día</h6>
                <h2 style="margin:5px 0 0; color:#28a745;"><?= $alDia ?></h2>
            </div>

            <div class="dashboard-card" style="flex:1; min-width:170px; padding:20px;">
                <h6 style="text-transform:uppercase; letter-spacing:1px; color:#0b8f70;">Cuotas vencidas</h6>
                <h2 style="margin:5px 0 0; color:#dc3545;"><?= $vencido ?></h2>
            </div>
        </div>

        <div class="row g-4">

            <div class="col-lg-7">
                <div class="dashboard-card" style="padding:25px; height:100%; max-width:none;">
                    <h4 style="margin-bottom:20px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                        Sistemas más elegidos por los clientes
                    </h4>

                    <div style="position:relative; height:340px;">
                        <canvas id="graficoSistemas"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="dashboard-card" style="padding:25px; height:100%; max-width:none;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:10px; flex-wrap:wrap;">
                        <h4 style="margin:0; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                            Ganancia por sistema
                        </h4>
                        <a href="<?= base_url('suscripciones/ingresos') ?>" style="background:#0b8f70; color:#fff; border-radius:8px; padding:6px 12px; text-decoration:none; text-transform:uppercase; letter-spacing:1px; font-size:11px;">Ver por mes</a>
                    </div>

                    <?php $maxSistema = !empty($ingresosPorSistema) ? max(array_column($ingresosPorSistema, 'total')) : 0; ?>
                    <?php if (!empty($ingresosPorSistema)): ?>
                        <?php foreach ($ingresosPorSistema as $s): ?>
                            <div style="margin-bottom:18px;">
                                <div style="display:flex; justify-content:space-between; gap:10px; color:#ddd; font-size:14px;">
                                    <span><?= esc($s['nombre_sistema']) ?></span>
                                    <strong style="color:#0b8f70;">$<?= number_format((float) $s['total'], 0, ',', '.') ?></strong>
                                </div>
                                <div style="background:#262626; border-radius:6px; height:14px; overflow:hidden; margin-top:6px;">
                                    <div style="width:<?= $maxSistema > 0 ? round(((float) $s['total'] / $maxSistema) * 100) : 0 ?>%; background:#0b8f70; height:100%;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color:#aaa;">Aún no hay pagos registrados.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const etiquetasSistemas = <?= json_encode(array_keys($sistemas)) ?>;
    const datosSistemas = <?= json_encode(array_values($sistemas)) ?>;

    new Chart(document.getElementById('graficoSistemas'), {
        type: 'bar',
        data: {
            labels: etiquetasSistemas,
            datasets: [{
                label: 'Clientes inscritos',
                data: datosSistemas,
                backgroundColor: 'rgba(11, 143, 112, 0.8)',
                borderColor: '#0b8f70',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#cfcfcf' }, grid: { color: 'rgba(255, 255, 255, 0.08)' } },
                y: { beginAtZero: true, ticks: { color: '#cfcfcf', stepSize: 1 }, grid: { color: 'rgba(255, 255, 255, 0.08)' } }
            }
        }
    });
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