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

        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:30px;">
            <h1 style="margin:0;">Reporte de Suscripciones</h1>

            <a href="<?= base_url('clientes') ?>" class="btn btn-outline-success btn-sm">← Volver</a>
        </div>

        <!-- RESUMEN -->
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

            <div class="dashboard-card" style="flex:1; min-width:170px; padding:20px;">
                <h6 style="text-transform:uppercase; letter-spacing:1px; color:#0b8f70;">Sin pagos</h6>
                <h2 style="margin:5px 0 0; color:#ffc107;"><?= $sinPagos ?></h2>
            </div>

            <div class="dashboard-card" style="flex:1; min-width:170px; padding:20px;">
                <h6 style="text-transform:uppercase; letter-spacing:1px; color:#0b8f70;">Sin suscripción</h6>
                <h2 style="margin:5px 0 0; color:#6c757d;"><?= $sinSuscripcion ?></h2>
            </div>
        </div>

        <!-- GRÁFICOS -->
        <div class="row g-4">

            <div class="col-lg-7">
                <div class="dashboard-card" style="padding:25px; height:100%;">
                    <h4 style="margin-bottom:20px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                        Sistemas más elegidos por los clientes
                    </h4>

                    <div style="position:relative; height:340px;">
                        <canvas id="graficoSistemas"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="dashboard-card" style="padding:25px; height:100%;">
                    <h4 style="margin-bottom:20px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                        Estado de las cuotas
                    </h4>

                    <div style="position:relative; height:340px;">
                        <canvas id="graficoCuotas"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- RECOMENDACIONES DE PROMOS -->
        <?php if (!empty($recomendaciones)): ?>
        <div class="dashboard-card" style="padding:25px; margin-top:30px; max-width:none;">
            <h4 style="margin-bottom:5px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                Recomendaciones de promos
            </h4>
            <p style="color:#aaa; margin-bottom:20px;">
                Basadas en las suscripciones más elegidas por los clientes, para tomar decisiones estratégicas.
            </p>

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

            <div class="row g-4">
                <?php foreach ($recomendaciones as $rec): ?>
                    <?php $color = $colores[$rec['tipo']] ?? '#0b8f70'; ?>
                    <div class="col-md-6">
                        <div class="dashboard-card" style="padding:20px; border-left:4px solid <?= $color ?>; height:100%;">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px; margin-bottom:8px;">
                                <span style="background:<?= $color ?>; color:#111; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:1px; padding:4px 10px; border-radius:20px;">
                                    <?= $badges[$rec['tipo']] ?? 'Recomendación' ?>
                                </span>
                                <span style="color:#cfcfcf; font-weight:700; text-transform:uppercase;"><?= esc($rec['sistema']) ?></span>
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
        </div>
        <?php endif; ?>
    </section>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

    // GRÁFICO DE SISTEMAS MÁS ELEGIDOS
    const etiquetasSistemas = <?= json_encode(array_keys($sistemas)) ?>;
    const datosSistemas = <?= json_encode(array_values($sistemas)) ?>;

    const graficoSistemas = new Chart(document.getElementById('graficoSistemas'), {
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
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    ticks: { color: '#cfcfcf' },
                    grid: { color: 'rgba(255, 255, 255, 0.08)' }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: '#cfcfcf', stepSize: 1 },
                    grid: { color: 'rgba(255, 255, 255, 0.08)' }
                }
            }
        }
    });

    // GRÁFICO DE ESTADO DE LAS CUOTAS
    const etiquetasCuotas = <?= json_encode(['Al día', 'Vencidas', 'Sin pagos', 'Sin suscripción']) ?>;
    const datosCuotas = <?= json_encode([$alDia, $vencido, $sinPagos, $sinSuscripcion]) ?>;

    const graficoCuotas = new Chart(document.getElementById('graficoCuotas'), {
        type: 'doughnut',
        data: {
            labels: etiquetasCuotas,
            datasets: [{
                data: datosCuotas,
                backgroundColor: [
                    '#28a745',
                    '#dc3545',
                    '#ffc107',
                    '#6c757d'
                ],
                borderColor: '#1b1b1b',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#cfcfcf', padding: 18, boxWidth: 14 }
                }
            }
        }
    });
</script>
