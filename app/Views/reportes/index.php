<style>
    .reporte-card {
        display: block;
        height: 100%;
        padding: 25px;
        border-radius: 12px;
        background: #1b1b1b;
        border: 1px solid #262626;
        border-left: 5px solid #0b8f70;
        color: #fff;
        text-decoration: none;
        transition: transform .15s ease, border-color .15s ease;
        min-width: 0;
        overflow: hidden;
    }

    .reporte-card:hover {
        transform: translateY(-3px);
        color: #fff;
    }

    .reporte-card h5 {
        margin: 0 0 8px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #0b8f70;
        font-size: 16px;
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .reporte-card p {
        margin: 0;
        color: #aaa;
        font-size: 14px;
        overflow-wrap: anywhere;
        white-space: normal;
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

        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:20px;">
            <h1 style="margin:0;">Reportes del Negocio</h1>
        </div>

        <div class="row g-4">

            <div class="col-md-4">
                <a href="<?= base_url('suscripciones/cuotas') ?>" class="reporte-card" style="border-left-color:#0b8f70;">
                    <h5 style="color:#0b8f70;">Reporte de Cuotas</h5>
                    <p>Total de clientes, cuotas al día y vencidas.</p>
                </a>
            </div>

            <div class="col-md-4">
                <a href="<?= base_url('suscripciones/liquidacion') ?>" class="reporte-card" style="border-left-color:#ffc107;">
                    <h5 style="color:#ffc107;">Reporte de Liquidación</h5>
                    <p>Comisión por empleado según lo cobrado.</p>
                </a>
            </div>

            <div class="col-md-4">
                <a href="<?= base_url('suscripciones/ingresos') ?>" class="reporte-card" style="border-left-color:#dc3545;">
                    <h5 style="color:#dc3545;">Reporte de Ingresos</h5>
                    <p>Total ganado, por mes y por sistema.</p>
                </a>
            </div>

            <div class="col-md-4">
                <a href="<?= base_url('suscripciones/promos') ?>" class="reporte-card" style="border-left-color:#4a6fa5;">
                    <h5 style="color:#7d9fc4;">Recomendaciones de Promos</h5>
                    <p>Sugerencias comerciales según la demanda.</p>
                </a>
            </div>
        </div>
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