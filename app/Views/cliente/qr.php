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

        <h1 style="margin-bottom:25px;">Mi código QR</h1>

        <div style="display:flex; gap:30px; flex-wrap:wrap; align-items:flex-start;">

            <div style="background:#fff; padding:28px; border-radius:18px; text-align:center;">
                <div id="qrcode" style="display:inline-block;"></div>
            </div>

            <div class="dashboard-card" style="max-width:520px; width:100%; padding:30px 35px;">
                <div style="font-size:22px; font-weight:800; margin-bottom:4px;"><?= esc($cliente) ?></div>
                <div style="color:#9a9a9a; margin-bottom:18px;">Mostrá este código al ingresar al gimnasio</div>

                <p><strong>Código:</strong> <span style="font-family:monospace;"><?= esc($codigo) ?></span></p>
                <p><strong>DNI:</strong> <?= esc(session()->get('dni')) ?></p>

                <hr style="border-color:#333;">

                <?php if($membresia['estado'] === 'sin_suscripcion'): ?>
                    <p><span class="badge bg-secondary">Sin suscripción</span></p>
                <?php elseif($membresia['activo']): ?>
                    <p><span class="badge bg-success">Cuota al día</span>
                       <span style="color:#9a9a9a; margin-left:8px;">vence en <?= (int) $membresia['dias'] ?> día<?= $membresia['dias'] == 1 ? '' : 's' ?></span></p>
                <?php else: ?>
                    <p><span class="badge bg-danger">Cuota vencida</span>
                       <span style="color:#9a9a9a; margin-left:8px;"><?= formatear_fecha($membresia['vencimiento']) ?></span></p>
                    <p style="color:#e6b800; font-size:14px;">Al escanear, el sistema avisará que tu cuota está vencida.</p>
                <?php endif; ?>

                <p style="font-size:13px; color:#9a9a9a; margin-top:18px;">
                    <i class="bi bi-info-circle"></i> El profesor o recepción escaneará este código para registrar tu ingreso.
                </p>

                <button type="button" id="btnDescargarQr" class="btn btn-success" style="margin-top:10px;">
                    <i class="bi bi-download"></i> Descargar QR
                </button>
            </div>

        </div>

    </section>

</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    const codigoQr = <?= json_encode($codigo) ?>;

    var contenedorQr = document.getElementById('qrcode');

    if (contenedorQr && typeof QRCode !== 'undefined') {
        new QRCode(contenedorQr, {
            text: codigoQr,
            width: 260,
            height: 260,
            colorDark: '#111111',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        });
    } else if (contenedorQr) {
        contenedorQr.innerHTML = '<p style="color:#a00; max-width:260px;">No se pudo cargar el generador de QR (sin conexión).</p>';
    }

    document.getElementById('btnDescargarQr')?.addEventListener('click', function () {
        var canvas = contenedorQr.querySelector('canvas');
        var img = contenedorQr.querySelector('img');

        var url = null;

        if (canvas) {
            url = canvas.toDataURL('image/png');
        } else if (img) {
            url = img.src;
        }

        if (!url) return;

        var enlace = document.createElement('a');
        enlace.href = url;
        enlace.download = 'mi-qr-ingreso.png';
        enlace.click();
    });

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
