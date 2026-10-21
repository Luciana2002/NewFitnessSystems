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

        <h1 style="margin-bottom:25px;">Check-in / Asistencia</h1>

        <div style="display:grid; grid-template-columns:minmax(320px,1fr) minmax(320px,1fr); gap:30px; align-items:start;">

            <div class="dashboard-card" style="max-width:none; width:100%; padding:30px;">
                <h2 style="font-size:20px; font-weight:800; margin-bottom:16px;">Escanear QR</h2>

                <div id="reader" style="width:100%; border-radius:14px; overflow:hidden; background:#000;"></div>

                <button type="button" id="btnIniciarCamara" class="nfs-btn nfs-btn-primary nfs-btn-block" style="margin-top:16px;">
                    <i class="bi bi-camera-video"></i> Iniciar cámara
                </button>
                <button type="button" id="btnDetenerCamara" class="nfs-btn nfs-btn-light nfs-btn-block" style="margin-top:10px; display:none;">
                    Detener cámara
                </button>

                <hr style="border-color:#333; margin:22px 0;">

                <label class="form-label">Ingreso manual de código</label>
                <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <input type="text" id="codigoManual" class="form-control" placeholder="NFS-000-00000000" style="flex:1; min-width:200px;">
                    <button type="button" id="btnManual" class="nfs-btn nfs-btn-primary">Verificar</button>
                </div>
            </div>

            <div>
                <div id="panelResultado" class="dashboard-card" style="max-width:none; width:100%; padding:35px; text-align:center; min-height:230px; display:flex; flex-direction:column; justify-content:center;">
                    <div id="resultadoIcono" style="font-size:52px; margin-bottom:12px;"><i class="bi bi-qr-code-scan"></i></div>
                    <div id="resultadoTitulo" style="font-size:24px; font-weight:800;">Esperando escaneo…</div>
                    <div id="resultadoMensaje" style="color:#9a9a9a; margin-top:8px;">Escaneá el QR del cliente o ingresá el código manualmente.</div>
                </div>

                <h2 style="font-size:20px; font-weight:800; margin:30px 0 16px;">Ingresos de hoy</h2>

                <div class="table-responsive" style="background:#1b1b1b; padding:18px; border-left:5px solid #0b8f70; max-height:420px; overflow-y:auto;">
                    <table class="table table-dark table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Cliente</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="tablaAsistencias">
                            <?php if(empty($asistenciasHoy)): ?>
                                <tr><td colspan="3" class="text-center">Sin ingresos registrados hoy.</td></tr>
                            <?php else: ?>
                                <?php foreach($asistenciasHoy as $asis): ?>
                                    <tr>
                                        <td><?= formatear_hora($asis['hora']) ?></td>
                                        <td><?= esc(($asis['nombre'] ?? '') . ' ' . ($asis['apellido'] ?? '')) ?></td>
                                        <td>
                                            <?php if($asis['resultado'] === 'OK'): ?>
                                                <span class="badge bg-success">OK</span>
                                            <?php elseif($asis['resultado'] === 'VENCIDA'): ?>
                                                <span class="badge bg-danger">Cuota vencida</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?= esc($asis['resultado']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </section>

</main>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    var html5QrCode = null;
    var escaneando = false;
    var ultimoCodigo = '';
    var ultimoTiempo = 0;

    function pintarResultado(tipo, titulo, mensaje) {
        var panel = document.getElementById('panelResultado');
        var icono = document.getElementById('resultadoIcono');

        var estilos = {
            ok:       { borde: '#0b8f70', fondo: '#12281f', icono: 'bi-check-circle-fill', color: '#14ffc0' },
            vencida:  { borde: '#c0392b', fondo: '#2c1515', icono: 'bi-exclamation-octagon-fill', color: '#ff6b5e' },
            rechazado:{ borde: '#e6b800', fondo: '#2b2510', icono: 'bi-slash-circle-fill', color: '#ffd94a' },
            error:    { borde: '#555',    fondo: '#1b1b1b', icono: 'bi-x-circle-fill', color: '#aaa' },
            espera:   { borde: '#0b8f70', fondo: '#1b1b1b', icono: 'bi-qr-code-scan', color: '#fff' }
        };

        var e = estilos[tipo] || estilos.error;

        panel.style.borderLeftColor = e.borde;
        panel.style.background = e.fondo;
        icono.innerHTML = '<i class="bi ' + e.icono + '" style="color:' + e.color + '"></i>';
        document.getElementById('resultadoTitulo').textContent = titulo;
        document.getElementById('resultadoMensaje').textContent = mensaje;
    }

    function registrarCodigo(codigo) {
        var form = new FormData();
        form.append('codigo', codigo);

        pintarResultado('espera', 'Verificando…', 'Esperá un instante');

        fetch('<?= base_url('checkin/procesar') ?>', { method: 'POST', body: form })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.ok) {
                    pintarResultado('ok', '✔ ' + data.nombre, data.mensaje + (data.vence ? ' — vence ' + data.vence : ''));
                    agregarFilaTiempoReal(data.nombre, 'OK');
                    beep(880);
                } else if (data.tipo === 'vencida') {
                    pintarResultado('vencida', '✘ CUOTA VENCIDA', (data.nombre ? data.nombre + ' — ' : '') + data.mensaje);
                    agregarFilaTiempoReal(data.nombre || '-', 'VENCIDA');
                    beep(220);
                } else if (data.tipo === 'rechazado') {
                    pintarResultado('rechazado', '✘ Rechazado', (data.nombre ? data.nombre + ' — ' : '') + data.mensaje);
                    beep(220);
                } else {
                    pintarResultado('error', 'Error', data.mensaje || 'No se pudo verificar');
                    beep(220);
                }
            })
            .catch(function () {
                pintarResultado('error', 'Error', 'Error de conexión al verificar');
            });
    }

    function agregarFilaTiempoReal(nombre, estado) {
        var tbody = document.getElementById('tablaAsistencias');
        var vacio = tbody.querySelector('td[colspan]');

        if (vacio) { tbody.innerHTML = ''; }

        var hora = new Date().toLocaleTimeString('es-AR', { hour: '2-digit', minute: '2-digit' });

        var clase = estado === 'OK' ? 'bg-success' : (estado === 'VENCIDA' ? 'bg-danger' : 'bg-secondary');
        var texto = estado === 'OK' ? 'OK' : (estado === 'VENCIDA' ? 'Cuota vencida' : estado);

        var fila = document.createElement('tr');
        fila.innerHTML = '<td>' + hora + '</td><td></td><td><span class="badge ' + clase + '">' + texto + '</span></td>';
        fila.children[1].textContent = nombre;
        tbody.prepend(fila);
    }

    function beep(frecuencia) {
        try {
            var ctx = new (window.AudioContext || window.webkitAudioContext)();
            var osc = ctx.createOscillator();
            var gain = ctx.createGain();
            osc.frequency.value = frecuencia;
            osc.connect(gain);
            gain.connect(ctx.destination);
            gain.gain.setValueAtTime(0.08, ctx.currentTime);
            osc.start();
            setTimeout(function () { osc.stop(); ctx.close(); }, 160);
        } catch (e) {}
    }

    function alEscanear(textoDecodificado) {
        var ahora = Date.now();

        if (textoDecodificado === ultimoCodigo && (ahora - ultimoTiempo) < 4000) {
            return;
        }

        ultimoCodigo = textoDecodificado;
        ultimoTiempo = ahora;

        registrarCodigo(textoDecodificado);
    }

    function iniciarCamara() {
        if (escaneando || typeof Html5Qrcode === 'undefined') {
            if (typeof Html5Qrcode === 'undefined') {
                pintarResultado('error', 'Sin conexión', 'No se pudo cargar el lector de QR (revisá la conexión). Usá el ingreso manual.');
            }
            return;
        }

        html5QrCode = new Html5Qrcode('reader');
        escaneando = true;

        html5QrCode.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: 250 },
            alEscanear
        ).then(function () {
            document.getElementById('btnDetenerCamara').style.display = 'block';
            document.getElementById('btnIniciarCamara').style.display = 'none';
        }).catch(function () {
            escaneando = false;
            pintarResultado('error', 'Cámara no disponible', 'No se pudo acceder a la cámara. Usá el ingreso manual.');
        });
    }

    function detenerCamara() {
        if (html5QrCode && escaneando) {
            html5QrCode.stop().then(function () {
                html5QrCode.clear();
                escaneando = false;
                document.getElementById('btnDetenerCamara').style.display = 'none';
                document.getElementById('btnIniciarCamara').style.display = 'block';
            }).catch(function () {});
        }
    }

    document.getElementById('btnIniciarCamara')?.addEventListener('click', iniciarCamara);
    document.getElementById('btnDetenerCamara')?.addEventListener('click', detenerCamara);

    document.getElementById('btnManual')?.addEventListener('click', function () {
        var valor = document.getElementById('codigoManual').value.trim();
        if (valor) { registrarCodigo(valor); document.getElementById('codigoManual').value = ''; }
    });

    document.getElementById('codigoManual')?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btnManual').click();
        }
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
