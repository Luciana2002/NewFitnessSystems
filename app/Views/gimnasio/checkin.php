<main class="dashboard-page">

    <button type="button" id="sidebarOpen" class="sidebar-handle">
        <span>›</span>
    </button>

    <button type="button" id="sidebarClose" class="sidebar-close">
        ✕
    </button>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <?= view('layout/sidebar') ?>

    <section class="dashboard-content">

        <div class="reporte-titulo">
            <div>
                <h1>Check-in / Asistencia</h1>
                <p class="reporte-metrica-help">Modalidad Opción A: un único QR oficial que los socios escanean con su celular.</p>
            </div>
        </div>

        <div class="row g-4">

            <!-- ===================== QR OFICIAL DEL GIMNASIO ===================== -->
            <div class="col-lg-6">
                <section class="dashboard-card reporte-card checkin-qr">
                    <div class="reporte-titulo">
                        <div>
                            <h2>QR oficial del gimnasio</h2>
                            <p class="reporte-metrica-help">Imprimí el cartel y colocalo en recepción. Cada socio escanea con su celular y registra su ingreso.</p>
                        </div>
                        <div class="reporte-acciones no-print">
                            <button type="button" class="nfs-btn nfs-btn-primary nfs-btn-sm" id="btnImprimirCartel">
                                <i class="bi bi-printer"></i> Imprimir cartel QR
                            </button>
                            <form method="post" action="<?= base_url('checkin/token/regenerar') ?>" data-confirmar="¿Regenerar el código? Los carteles impresos dejarán de funcionar.">
                                <button type="submit" class="nfs-btn nfs-btn-outline nfs-btn-sm">
                                    <i class="bi bi-arrow-clockwise"></i> Regenerar código
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="qr-print-area">
                        <div class="qr-cartel">
                            <div class="qr-cartel-marca"><i class="bi bi-house-heart-fill"></i> New Fitness Systems</div>
                            <h3 class="qr-cartel-titulo">Escaneá para registrar tu ingreso</h3>
                            <div id="qrGimnasio" class="qr-imagen"></div>
                            <p class="qr-cartel-codigo">Ingresá con tu usuario de socio</p>
                            <p class="qr-cartel-nota">El ingreso se registra automáticamente. Mostrá la pantalla verde en recepción.</p>
                        </div>
                    </div>
                </section>
            </div>

            <!-- ===================== INGRESOS DE HOY ===================== -->
            <div class="col-lg-6">
                <section class="dashboard-card reporte-card checkin-ingresos">
                    <div class="panel-card-head">
                        <h2><i class="bi bi-list-check"></i> Ingresos de hoy</h2>
                        <button type="button" id="btnActualizarIngresos" class="nfs-btn nfs-btn-secondary nfs-btn-sm no-print">
                            <i class="bi bi-arrow-clockwise"></i> Actualizar
                        </button>
                    </div>

                    <div class="table-responsive reporte-tabla-wrap checkin-tabla-wrap">
                        <table class="table table-dark table-hover reporte-tabla">
                            <thead>
                                <tr>
                                    <th>Hora</th>
                                    <th>Cliente</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($asistenciasHoy)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Sin ingresos registrados hoy.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($asistenciasHoy as $asis): ?>
                                        <?php
                                            $claseEstado = match ($asis['resultado']) {
                                                'OK'      => 'is-ok',
                                                'VENCIDA' => 'is-danger',
                                                default   => 'is-muted',
                                            };
                                            $textoEstado = match ($asis['resultado']) {
                                                'OK'      => 'OK',
                                                'VENCIDA' => 'Cuota vencida',
                                                default   => (string) $asis['resultado'],
                                            };
                                        ?>
                                        <tr>
                                            <td><?= formatear_hora($asis['hora']) ?></td>
                                            <td><?= esc(($asis['nombre'] ?? '') . ' ' . ($asis['apellido'] ?? '')) ?></td>
                                            <td>
                                                <span class="status-pill <?= $claseEstado ?>"><?= esc($textoEstado) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

        </div>

    </section>

</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    // QR OFICIAL DEL GIMNASIO
    const qrUrlGimnasio = <?= json_encode($qrUrl ?? '') ?>;
    const contenedorQrGimnasio = document.getElementById('qrGimnasio');

    if (contenedorQrGimnasio && qrUrlGimnasio && typeof QRCode !== 'undefined') {
        new QRCode(contenedorQrGimnasio, {
            text: qrUrlGimnasio,
            width: 300,
            height: 300,
            colorDark: '#111111',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        });
    } else if (contenedorQrGimnasio) {
        contenedorQrGimnasio.textContent = 'No se pudo generar el QR (sin conexión).';
    }

    document.getElementById('btnImprimirCartel')?.addEventListener('click', function () {
        window.print();
    });

    document.getElementById('btnActualizarIngresos')?.addEventListener('click', function () {
        window.location.reload();
    });

    document.querySelectorAll('[data-confirmar]').forEach(function (formulario) {
        formulario.addEventListener('submit', function (evento) {
            if (! window.confirm(formulario.dataset.confirmar)) {
                evento.preventDefault();
            }
        });
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
        document.addEventListener('keydown', function (evento) {
            if (evento.key === 'Escape') {
                cerrarSidebar();
            }
        });
    }
</script>
