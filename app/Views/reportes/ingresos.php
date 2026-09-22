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

        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:25px;">
            <h1 style="margin:0;">Reporte de Ingresos</h1>
            <div style="background:#111; border-left:4px solid #ffc107; padding:12px 22px; border-radius:8px;">
                <h6 style="margin:0; text-transform:uppercase; letter-spacing:1px; color:#aaa; font-size:12px;" id="periodoLabel">Ganado en 1 mes</h6>
                <h2 style="margin:2px 0 0; color:#ffc107;" id="periodoTotal">$0</h2>
            </div>
        </div>

        <div style="display:flex; align-items:center; gap:15px; flex-wrap:wrap; margin-bottom:30px;">
            <span style="color:#cfcfcf; text-transform:uppercase; letter-spacing:1px; font-size:13px;">Período</span>

            <div style="display:flex; align-items:center; gap:8px; background:#111; border:1px solid #262626; border-radius:8px; padding:6px;">
                <button
                    type="button"
                    id="menosMeses"
                    style="width:36px; height:36px; border-radius:6px; border:none; background:#262626; color:#fff; font-size:18px; font-weight:700; line-height:1; cursor:pointer;"
                >&lt;</button>
                <span id="textoMeses" style="min-width:130px; text-align:center; color:#fff; font-weight:600; font-size:14px;">Último mes</span>
                <button
                    type="button"
                    id="masMeses"
                    style="width:36px; height:36px; border-radius:6px; border:none; background:#262626; color:#fff; font-size:18px; font-weight:700; line-height:1; cursor:pointer;"
                >&gt;</button>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="dashboard-card" style="padding:25px; height:100%; max-width:none;">
                    <h4 style="margin-bottom:20px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                        Ingresos por mes
                    </h4>

                    <div style="position:relative; height:320px;">
                        <canvas id="graficoIngresos"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="dashboard-card" style="padding:25px; height:100%; max-width:none;">
                    <h4 style="margin-bottom:20px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                        Ganancia por sistema
                    </h4>

                <?php if (!empty($ingresosPorSistema)): ?>
                    <?php $maxSistema = max(array_column($ingresosPorSistema, 'total')); ?>
                    <?php foreach ($ingresosPorSistema as $sist): ?>
                        <div style="margin-bottom:18px;">
                            <div style="display:flex; justify-content:space-between; gap:10px; color:#ddd; font-size:14px; margin-bottom:5px; overflow-wrap:anywhere;">
                                <span><?= esc($sist['nombre_sistema']) ?></span>
                                <strong>$<?= number_format((float) $sist['total'], 0, ',', '.') ?></strong>
                            </div>
                            <div style="background:#262626; border-radius:6px; height:14px; overflow:hidden;">
                                <div style="width:<?= $maxSistema > 0 ? round(((float) $sist['total'] / $maxSistema) * 100) : 0 ?>%; background:#dc3545; height:100%;"></div>
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
    const graficoIngresos = new Chart(document.getElementById('graficoIngresos'), {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Ingresos ($)',
                data: [],
                backgroundColor: 'rgba(220, 53, 69, 0.8)',
                borderColor: '#dc3545',
                borderWidth: 1,
                borderRadius: 6,
                maxBarThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#cfcfcf' }, grid: { color: 'rgba(255, 255, 255, 0.08)' } },
                y: { beginAtZero: true, ticks: { color: '#cfcfcf' }, grid: { color: 'rgba(255, 255, 255, 0.08)' } }
            }
        }
    });

    const textoMeses = document.getElementById('textoMeses');
    const botonMenos = document.getElementById('menosMeses');
    const botonMas = document.getElementById('masMeses');
    const periodoLabel = document.getElementById('periodoLabel');
    const periodoTotal = document.getElementById('periodoTotal');

    let mesesActuales = 1;

    function actualizarPeriodo() {
        textoMeses.textContent = mesesActuales === 1 ? 'Último mes' : 'Últimos ' + mesesActuales + ' meses';
        cargarIngresos(mesesActuales);
    }

    botonMenos.addEventListener('click', function () {
        if (mesesActuales > 1) {
            mesesActuales--;
            actualizarPeriodo();
        }
    });

    botonMas.addEventListener('click', function () {
        if (mesesActuales < 12) {
            mesesActuales++;
            actualizarPeriodo();
        }
    });

    function formatearMonto(valor) {
        return '$' + Number(valor).toLocaleString('es-AR', { maximumFractionDigits: 0 });
    }

    async function cargarIngresos(meses) {
        try {
            const respuesta = await fetch('<?= base_url('suscripciones/ingresos/') ?>' + meses, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const datos = await respuesta.json();

            graficoIngresos.data.labels = datos.etiquetas;
            graficoIngresos.data.datasets[0].data = datos.datos;
            graficoIngresos.update();

            periodoLabel.textContent = 'Ganado en ' + meses + (meses === 1 ? ' mes' : ' meses');
            periodoTotal.textContent = formatearMonto(datos.totalPeriodo);
        } catch (e) {
            // Sin cambios si falla la consulta
        }
    }

    cargarIngresos(mesesActuales);
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