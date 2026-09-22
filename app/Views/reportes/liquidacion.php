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
            <h1 style="margin:0;">Reporte de Liquidación</h1>
        </div>

        <?php if (!empty($liquidacionProfesores)): ?>
            <div style="display:flex; align-items:center; gap:15px; flex-wrap:wrap; margin-bottom:25px;">
                <label for="porcentajeComision" style="color:#cfcfcf; text-transform:uppercase; letter-spacing:1px; font-size:13px;">
                    Porcentaje de comisión
                </label>
                <div style="display:flex; align-items:center; gap:8px; background:#111; border:1px solid #262626; border-radius:8px; padding:8px 14px;">
                    <input
                        type="number"
                        id="porcentajeComision"
                        min="0"
                        max="100"
                        step="1"
                        value="30"
                        style="background:transparent; border:none; color:#fff; font-size:18px; font-weight:700; width:70px; outline:none;"
                    >
                    <span style="color:#ffc107; font-weight:700;">%</span>
                </div>
            </div>

            <div style="overflow-x:auto;">
                <table class="table table-dark table-hover" style="background:#111; border-radius:8px; overflow:hidden; min-width:600px;">
                    <thead>
                        <tr style="background:#262626; color:#fff; text-transform:uppercase; letter-spacing:1px; font-size:13px;">
                            <th style="padding:12px;">Profesor</th>
                            <th style="padding:12px;">Usuario</th>
                            <th style="padding:12px;">Cuotas cobradas</th>
                            <th style="padding:12px;">Total cobrado</th>
                            <th style="padding:12px; color:#ffc107;" id="comisionHeader">Comisión 30%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($liquidacionProfesores as $prof): ?>
                            <tr style="color:#ddd; border-bottom:1px solid #262626;"
                                data-cobrado="<?= (float) $prof['cobrado'] ?>">
                                <td style="padding:12px; overflow-wrap:anywhere;"><?= esc($prof['nombre'] . ' ' . $prof['apellido']) ?></td>
                                <td style="padding:12px; overflow-wrap:anywhere;">@<?= esc($prof['nombre_usuario']) ?></td>
                                <td style="padding:12px;"><?= (int) $prof['cuotas'] ?></td>
                                <td style="padding:12px;">$<?= number_format((float) $prof['cobrado'], 0, ',', '.') ?></td>
                                <td style="padding:12px; color:#ffc107; font-weight:700;" class="comision-valor">
                                    $<?= number_format($prof['comision'], 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="color:#aaa;">Aún no hay pagos registrados.</p>
        <?php endif; ?>
    </section>

</main>

<script>
    const inputComision = document.getElementById('porcentajeComision');
    const comisionHeader = document.getElementById('comisionHeader');

    const CLAVE_PORCENTAJE = 'porcentajeLiquidacion';

    function formatearMonto(valor) {
        return '$' + Number(valor).toLocaleString('es-AR', { maximumFractionDigits: 0 });
    }

    function recalcularComision() {
        const pct = parseFloat(inputComision.value);

        if (isNaN(pct)) {
            return;
        }

        comisionHeader.textContent = 'Comisión ' + pct + '%';

        document.querySelectorAll('tr[data-cobrado]').forEach(function (fila) {
            const cobrado = parseFloat(fila.getAttribute('data-cobrado'));
            const celda = fila.querySelector('.comision-valor');

            if (celda) {
                celda.textContent = formatearMonto(cobrado * pct / 100);
            }
        });
    }

    if (inputComision) {
        const porcentajeGuardado = localStorage.getItem(CLAVE_PORCENTAJE);

        if (porcentajeGuardado !== null) {
            inputComision.value = porcentajeGuardado;
        }

        inputComision.addEventListener('input', function () {
            recalcularComision();
            localStorage.setItem(CLAVE_PORCENTAJE, inputComision.value);
        });

        recalcularComision();
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