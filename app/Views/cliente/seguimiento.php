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

        <h1 style="margin-bottom:25px;">Seguimiento físico</h1>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="toast-custom success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="toast-custom error"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <?php if($ultimo): ?>
            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:16px; margin-bottom:35px;">
                <?php
                $tiles = [
                    'Peso'        => ($ultimo['peso'] !== null ? $ultimo['peso'] . ' kg' : '-'),
                    'Grasa'       => ($ultimo['grasa_corporal'] !== null ? $ultimo['grasa_corporal'] . ' %' : '-'),
                    'Cintura'     => ($ultimo['cintura'] !== null ? $ultimo['cintura'] . ' cm' : '-'),
                    'Pecho'       => ($ultimo['pecho'] !== null ? $ultimo['pecho'] . ' cm' : '-'),
                    'Brazo'       => ($ultimo['brazo'] !== null ? $ultimo['brazo'] . ' cm' : '-'),
                    'Muslo'       => ($ultimo['muslo'] !== null ? $ultimo['muslo'] . ' cm' : '-'),
                    'Cadera'      => ($ultimo['cadera'] !== null ? $ultimo['cadera'] . ' cm' : '-'),
                ];
                ?>
                <?php foreach($tiles as $titulo => $valor): ?>
                    <div class="dashboard-card" style="max-width:none; padding:20px;">
                        <div class="menu-section-title" style="margin-bottom:4px;"><?= $titulo ?></div>
                        <div style="font-size:22px; font-weight:800;"><?= esc($valor) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-card" style="max-width:none; width:100%; padding:30px 35px; margin-bottom:35px;">
            <h2 style="font-size:22px; font-weight:800; margin-bottom:18px;">Evolución</h2>
            <?php if(empty($historial)): ?>
                <p style="color:#9a9a9a;">Cargá tu primer registro para ver el gráfico de evolución.</p>
            <?php else: ?>
                <canvas id="graficoEvolucion" height="110"></canvas>
            <?php endif; ?>
        </div>

        <div class="dashboard-card" style="max-width:none; width:100%; padding:30px 35px; margin-bottom:35px;">
            <h2 style="font-size:22px; font-weight:800; margin-bottom:18px;">Nuevo registro</h2>

            <form action="<?= base_url('cliente/guardar_seguimiento') ?>" method="post">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Peso (kg)</label>
                        <input type="number" step="0.1" min="0" name="peso" class="form-control" placeholder="Ej: 72.5">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">% Grasa</label>
                        <input type="number" step="0.1" min="0" name="grasa_corporal" class="form-control" placeholder="Ej: 18.4">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Cintura (cm)</label>
                        <input type="number" step="0.1" min="0" name="cintura" class="form-control">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Pecho (cm)</label>
                        <input type="number" step="0.1" min="0" name="pecho" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Brazo (cm)</label>
                        <input type="number" step="0.1" min="0" name="brazo" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Muslo (cm)</label>
                        <input type="number" step="0.1" min="0" name="muslo" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Cadera (cm)</label>
                        <input type="number" step="0.1" min="0" name="cadera" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notas</label>
                    <textarea name="notas" class="form-control" rows="2" placeholder="Cómo te sentiste, alimentación, etc."></textarea>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-lg"></i> Guardar registro
                </button>
            </form>
        </div>

        <h2 style="font-size:22px; font-weight:800; margin-bottom:18px;">Historial</h2>

        <div class="table-responsive" style="background:#1b1b1b; padding:20px; border-left:5px solid #0b8f70;">
            <table class="table table-dark table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Peso</th>
                        <th>Grasa</th>
                        <th>Cintura</th>
                        <th>Pecho</th>
                        <th>Brazo</th>
                        <th>Muslo</th>
                        <th>Cadera</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($historial)): ?>
                        <tr><td colspan="9" class="text-center">Sin registros.</td></tr>
                    <?php else: ?>
                        <?php foreach(array_reverse($historial) as $fila): ?>
                            <tr>
                                <td><?= formatear_fecha($fila['fecha']) ?></td>
                                <td><?= $fila['peso'] !== null ? esc($fila['peso']) . ' kg' : '-' ?></td>
                                <td><?= $fila['grasa_corporal'] !== null ? esc($fila['grasa_corporal']) . ' %' : '-' ?></td>
                                <td><?= $fila['cintura'] !== null ? esc($fila['cintura']) : '-' ?></td>
                                <td><?= $fila['pecho'] !== null ? esc($fila['pecho']) : '-' ?></td>
                                <td><?= $fila['brazo'] !== null ? esc($fila['brazo']) : '-' ?></td>
                                <td><?= $fila['muslo'] !== null ? esc($fila['muslo']) : '-' ?></td>
                                <td><?= $fila['cadera'] !== null ? esc($fila['cadera']) : '-' ?></td>
                                <td><?= esc($fila['notas'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </section>

</main>

<?php if(!empty($historial)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const chartData = <?= json_encode($chart) ?>;

    var canvas = document.getElementById('graficoEvolucion');

    if (canvas && typeof Chart !== 'undefined') {
        const etiquetas = chartData.fechas.map(function (f) {
            var partes = f.split('-');
            return partes.length === 3 ? (partes[2] + '/' + partes[1]) : f;
        });

        new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: etiquetas,
                datasets: [
                    {
                        label: 'Peso (kg)',
                        data: chartData.peso,
                        borderColor: '#0b8f70',
                        backgroundColor: 'rgba(11,143,112,0.2)',
                        tension: 0.3,
                        spanGaps: true,
                        yAxisID: 'y'
                    },
                    {
                        label: '% Grasa',
                        data: chartData.grasa,
                        borderColor: '#e6b800',
                        backgroundColor: 'rgba(230,184,0,0.2)',
                        tension: 0.3,
                        spanGaps: true,
                        yAxisID: 'y1'
                    },
                    {
                        label: 'Cintura (cm)',
                        data: chartData.cintura,
                        borderColor: '#4aa3df',
                        backgroundColor: 'rgba(74,163,223,0.2)',
                        tension: 0.3,
                        spanGaps: true,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { labels: { color: '#eee' } }
                },
                scales: {
                    x: { ticks: { color: '#bbb' }, grid: { color: '#333' } },
                    y: {
                        position: 'left',
                        ticks: { color: '#0b8f70' },
                        grid: { color: '#333' },
                        title: { display: true, text: 'Peso (kg)', color: '#0b8f70' }
                    },
                    y1: {
                        position: 'right',
                        ticks: { color: '#e6b800' },
                        grid: { drawOnChartArea: false },
                        title: { display: true, text: 'Grasa % / Medidas (cm)', color: '#e6b800' }
                    }
                }
            }
        });
    }

    // SIDEBAR
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
<?php else: ?>
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
            if (e.key === "Escape") { cerrarSidebar(); }
        });
    }
</script>
<?php endif; ?>

<script>
    const toasts = document.querySelectorAll('.toast-custom');
    toasts.forEach((toast, index) => {
        setTimeout(() => { toast.style.opacity = "1"; toast.style.transform = "translateY(0)"; }, 100);
        setTimeout(() => {
            toast.style.opacity = "0";
            toast.style.transform = "translateY(-20px)";
            setTimeout(() => toast.remove(), 400);
        }, 2800 + (index * 200));
    });
</script>
