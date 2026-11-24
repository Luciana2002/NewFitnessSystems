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

    <section class="dashboard-content">

        <div class="reporte-titulo">
            <div>
                <h1>Reportes del Negocio</h1>
                <p class="reporte-metrica-help">Elegí la sección que querés consultar.</p>
            </div>
        </div>

        <div class="reporte-tabs no-print" role="tablist" aria-label="Secciones de reportes">
            <button type="button" class="reporte-tab activa" id="tab-caja" data-tab="caja"
                    role="tab" aria-selected="true" aria-controls="panel-caja">
                <i class="bi bi-cash-coin"></i> Cierre de Caja
            </button>
            <button type="button" class="reporte-tab" id="tab-cuotas" data-tab="cuotas"
                    role="tab" aria-selected="false" aria-controls="panel-cuotas">
                <i class="bi bi-pie-chart-fill"></i> Cuotas y Disciplinas
            </button>
            <button type="button" class="reporte-tab" id="tab-balance" data-tab="balance"
                    role="tab" aria-selected="false" aria-controls="panel-balance">
                <i class="bi bi-graph-up"></i> Balance y Movimientos
            </button>
            <button type="button" class="reporte-tab" id="tab-asistencia" data-tab="asistencia"
                    role="tab" aria-selected="false" aria-controls="panel-asistencia">
                <i class="bi bi-person-check-fill"></i> Asistencia
            </button>
        </div>

        <!-- ===================== CIERRE DE CAJA DIARIO ===================== -->
        <div class="reporte-panel activo" id="panel-caja" data-panel="caja"
             role="tabpanel" aria-labelledby="tab-caja">
            <div class="reporte-seccion">
                <div class="reporte-titulo">
                    <div>
                        <h2>Cierre de Caja</h2>
                        <p class="reporte-metrica-help">Resumen de los pagos registrados en la fecha elegida.</p>
                    </div>
                    <div class="reporte-acciones">
                        <a class="nfs-btn nfs-btn-secondary nfs-btn-sm no-print"
                           href="<?= base_url('suscripciones/exportar/caja?fecha=' . $fecha) ?>">
                            <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                        </a>
                        <button type="button" class="nfs-btn nfs-btn-primary nfs-btn-sm no-print" data-imprimir>
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                    </div>
                </div>

                <form method="get" action="<?= base_url('suscripciones') ?>" class="reporte-fechas no-print" id="formFechaCaja">
                    <label for="fecha">Fecha a consultar</label>
                    <input type="date" id="fecha" name="fecha" value="<?= esc($fecha) ?>" class="reporte-fecha-input">
                    <button type="button" class="nfs-btn nfs-btn-secondary nfs-btn-sm" data-fecha="hoy">Hoy</button>
                    <button type="button" class="nfs-btn nfs-btn-secondary nfs-btn-sm" data-fecha="ayer">Ayer</button>
                </form>
                <p class="reporte-fecha-ayuda">
                    Estás viendo los pagos registrados el <strong><?= esc(date('d/m/Y', strtotime($fecha))) ?></strong>.
                </p>

                <div class="reporte-metricas">
                    <div class="dashboard-card reporte-metrica es-info">
                        <h6 class="reporte-metrica-label">Total recaudado</h6>
                        <div class="reporte-metrica-valor">$<?= number_format($totalCaja, 0, ',', '.') ?></div>
                        <div class="reporte-metrica-help">Recaudado en la fecha consultada</div>
                    </div>

                    <div class="dashboard-card reporte-metrica es-ok">
                        <h6 class="reporte-metrica-label">Movimientos</h6>
                        <div class="reporte-metrica-valor"><?= count($pagos) ?></div>
                        <div class="reporte-metrica-help">Pagos registrados ese día</div>
                    </div>

                    <div class="dashboard-card reporte-metrica">
                        <h6 class="reporte-metrica-label">Fecha</h6>
                        <div class="reporte-metrica-valor pequeno"><?= esc(date('d/m/Y', strtotime($fecha))) ?></div>
                        <div class="reporte-metrica-help">Día seleccionado en el filtro</div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <div class="dashboard-card reporte-card h-100">
                            <div class="panel-card-head">
                                <h2><i class="bi bi-credit-card"></i> Totales por medio de pago</h2>
                            </div>

                            <?php if (!empty($porMedio)): ?>
                                <ul class="panel-list">
                                    <?php foreach ($porMedio as $medio => $monto): ?>
                                        <li class="list-row">
                                            <div class="list-main">
                                                <strong><?= esc($medio) ?></strong>
                                            </div>
                                            <div class="list-side">
                                                <span class="reporte-importe">$<?= number_format($monto, 0, ',', '.') ?></span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="reporte-metrica-help">Sin movimientos este día.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="dashboard-card reporte-card h-100">
                            <div class="panel-card-head">
                                <h2><i class="bi bi-person-check"></i> Totales por quién cobró</h2>
                            </div>

                            <?php if (!empty($porCobrador)): ?>
                                <ul class="panel-list">
                                    <?php foreach ($porCobrador as $cobrador => $monto): ?>
                                        <li class="list-row">
                                            <div class="list-main">
                                                <strong><?= esc($cobrador) ?></strong>
                                            </div>
                                            <div class="list-side">
                                                <span class="reporte-importe verde">$<?= number_format($monto, 0, ',', '.') ?></span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="reporte-metrica-help">Sin movimientos este día.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card reporte-card">
                    <div class="panel-card-head">
                        <h2><i class="bi bi-list-check"></i> Detalle de pagos</h2>
                    </div>

                    <?php if (!empty($pagos)): ?>
                        <div class="table-responsive reporte-tabla-wrap">
                            <table class="table table-dark table-hover reporte-tabla">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Medio de pago</th>
                                        <th>Cobrado por</th>
                                        <th class="texto-derecha">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pagos as $pago): ?>
                                        <?php
                                            $cobrador = trim(($pago['cobrador_nombre'] ?? '') . ' ' . ($pago['cobrador_apellido'] ?? ''));
                                            if ($cobrador === '') {
                                                $cobrador = $pago['cobrador_usuario'] ?: 'Sin registrar';
                                            }
                                            $cliente = trim(($pago['cliente_apellido'] ?? '') . ', ' . ($pago['cliente_nombre'] ?? ''));
                                            if ($cliente === ',') {
                                                $cliente = '—';
                                            }
                                        ?>
                                        <tr>
                                            <td><?= esc($cliente) ?></td>
                                            <td><?= esc($pago['medio_pago'] ?: 'Sin especificar') ?></td>
                                            <td><?= esc($cobrador) ?></td>
                                            <td class="texto-derecha reporte-importe">$<?= number_format((float) $pago['monto'], 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="reporte-metrica-help">No hay pagos registrados en esta fecha.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ===================== CUOTAS Y DISCIPLINAS ===================== -->
        <div class="reporte-panel" id="panel-cuotas" data-panel="cuotas"
             role="tabpanel" aria-labelledby="tab-cuotas">
            <div class="reporte-seccion">
                <div class="reporte-titulo">
                    <div>
                        <h2>Cuotas y Disciplinas</h2>
                        <p class="reporte-metrica-help">Estado de las cuotas e ingresos de cada disciplina.</p>
                    </div>
                    <div class="reporte-acciones">
                        <a class="nfs-btn nfs-btn-secondary nfs-btn-sm no-print"
                           href="<?= base_url('suscripciones/exportar/cuotas') ?>">
                            <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                        </a>
                        <a class="nfs-btn nfs-btn-primary nfs-btn-sm no-print"
                           href="<?= base_url('suscripciones/cuotas') ?>">
                            <i class="bi bi-arrows-angle-expand"></i> Ver reporte completo
                        </a>
                        <button type="button" class="nfs-btn nfs-btn-outline nfs-btn-sm no-print" data-imprimir>
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                    </div>
                </div>

                <div class="reporte-metricas">
                    <div class="dashboard-card reporte-metrica">
                        <h6 class="reporte-metrica-label">Total de clientes</h6>
                        <div class="reporte-metrica-valor"><?= $total ?></div>
                        <div class="reporte-metrica-help">Socios registrados en el sistema</div>
                    </div>

                    <div class="dashboard-card reporte-metrica es-ok">
                        <h6 class="reporte-metrica-label">Cuotas al día</h6>
                        <div class="reporte-metrica-valor"><?= $alDia ?></div>
                        <div class="reporte-metrica-help">Socios con cuota vigente</div>
                    </div>

                    <div class="dashboard-card reporte-metrica es-alerta">
                        <h6 class="reporte-metrica-label">Cuotas vencidas</h6>
                        <div class="reporte-metrica-valor"><?= $vencido ?></div>
                        <div class="reporte-metrica-help">Socios con pago pendiente</div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="dashboard-card reporte-card h-100">
                            <div class="panel-card-head">
                                <h2><i class="bi bi-bar-chart"></i> Clientes por disciplina</h2>
                            </div>
                            <div class="reporte-chart">
                                <canvas id="graficoSistemas" class="reporte-canvas"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="dashboard-card reporte-card h-100">
                            <div class="panel-card-head">
                                <h2><i class="bi bi-cash-stack"></i> Ingresos por disciplina</h2>
                                <a href="<?= base_url('suscripciones/ingresos') ?>">Ver por mes</a>
                            </div>

                            <?php $maxSistema = !empty($ingresosPorSistema) ? max(array_column($ingresosPorSistema, 'total')) : 0; ?>
                            <?php if (!empty($ingresosPorSistema)): ?>
                                <div class="reporte-barras">
                                    <?php foreach ($ingresosPorSistema as $s): ?>
                                        <?php
                                            $porcentaje = $maxSistema > 0
                                                ? (int) round(((float) $s['total'] / $maxSistema) * 100)
                                                : 0;
                                            $porcentaje = max(0, min(100, $porcentaje));
                                            $porcentaje = (int) (round($porcentaje / 5) * 5);
                                        ?>
                                        <div class="reporte-barra-item">
                                            <div class="reporte-barra-info">
                                                <span><?= esc($s['nombre_sistema']) ?></span>
                                                <strong class="reporte-importe">$<?= number_format((float) $s['total'], 0, ',', '.') ?></strong>
                                            </div>
                                            <div class="reporte-barra">
                                                <div class="reporte-barra-relleno reporte-barra-<?= $porcentaje ?>"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="reporte-metrica-help">Aún no hay pagos registrados.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== BALANCE Y MOVIMIENTOS ===================== -->
        <div class="reporte-panel" id="panel-balance" data-panel="balance"
             role="tabpanel" aria-labelledby="tab-balance">
            <div class="reporte-seccion">
                <div class="reporte-titulo">
                    <div>
                        <h2>Balance y Movimientos</h2>
                        <p class="reporte-metrica-help">Accesos directos a gastos, balance e historial de cambios.</p>
                    </div>
                    <div class="reporte-acciones">
                        <button type="button" class="nfs-btn nfs-btn-secondary nfs-btn-sm no-print" data-imprimir>
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <a href="<?= base_url('suscripciones/balance') ?>" class="btn-acceso">
                            <h5>Gastos y Balance</h5>
                            <p>Registrá egresos y calculá la ganancia neta del mes.</p>
                        </a>
                    </div>

                    <div class="col-lg-6">
                        <a href="<?= base_url('suscripciones/bitacora') ?>" class="btn-acceso">
                            <h5>Historial de movimientos</h5>
                            <p>Quién hizo qué y cuándo: altas, bajas y cambios.</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== ASISTENCIA / CONCURRENCIA ===================== -->
        <div class="reporte-panel" id="panel-asistencia" data-panel="asistencia"
             role="tabpanel" aria-labelledby="tab-asistencia">
            <?php
                $diasSemana = [
                    1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves',
                    5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo',
                ];
                $porDia    = $asistencia['porDia'] ?? array_fill_keys(range(1, 7), 0);
                $porMes    = $asistencia['porMes'] ?? [];
                $porHora   = $asistencia['porHora'] ?? array_fill_keys(range(0, 23), 0);
                $inactivos = $asistencia['inactivos'] ?? [];
                $diaPico   = $asistencia['diaPico'] ?? null;
                $horaPico  = $asistencia['horaPico'] ?? null;
            ?>
            <div class="reporte-seccion">
                <div class="reporte-titulo">
                    <div>
                        <h2>Asistencia</h2>
                        <p class="reporte-metrica-help">Concurrencia del gimnasio y socios que necesitan un aviso.</p>
                    </div>
                    <div class="reporte-acciones">
                        <button type="button" class="nfs-btn nfs-btn-secondary nfs-btn-sm no-print" data-imprimir>
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                    </div>
                </div>

                <div class="reporte-metricas">
                    <div class="dashboard-card reporte-metrica es-info">
                        <h6 class="reporte-metrica-label">Asistencias de hoy</h6>
                        <div class="reporte-metrica-valor"><?= (int) ($asistencia['hoy'] ?? 0) ?></div>
                        <div class="reporte-metrica-help">Ingresos registrados en el día</div>
                    </div>

                    <div class="dashboard-card reporte-metrica es-ok">
                        <h6 class="reporte-metrica-label">Asistencias del mes</h6>
                        <div class="reporte-metrica-valor"><?= (int) ($asistencia['mes'] ?? 0) ?></div>
                        <div class="reporte-metrica-help">Ingresos de este mes</div>
                    </div>

                    <div class="dashboard-card reporte-metrica">
                        <h6 class="reporte-metrica-label">Día más concurrido</h6>
                        <div class="reporte-metrica-valor pequeno">
                            <?= $diaPico !== null && isset($diasSemana[$diaPico]) ? esc($diasSemana[$diaPico]) : '—' ?>
                        </div>
                        <div class="reporte-metrica-help">
                            <?= (int) ($asistencia['cantidadDiaPico'] ?? 0) > 0
                                ? (int) $asistencia['cantidadDiaPico'] . ' ingresos este mes'
                                : 'Sin ingresos este mes' ?>
                        </div>
                    </div>

                    <div class="dashboard-card reporte-metrica">
                        <h6 class="reporte-metrica-label">Horario pico</h6>
                        <div class="reporte-metrica-valor pequeno">
                            <?= $horaPico !== null ? esc(sprintf('%02d:00', $horaPico)) : '—' ?>
                        </div>
                        <div class="reporte-metrica-help">
                            <?= (int) ($asistencia['cantidadHoraPico'] ?? 0) > 0
                                ? (int) $asistencia['cantidadHoraPico'] . ' ingresos en ese horario'
                                : 'Sin ingresos este mes' ?>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <div class="dashboard-card reporte-card h-100">
                            <div class="panel-card-head">
                                <h2><i class="bi bi-bar-chart"></i> Concurrencia por día de la semana</h2>
                            </div>
                            <div class="reporte-chart">
                                <canvas id="graficoAsistenciaSemana" class="reporte-canvas"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="dashboard-card reporte-card h-100">
                            <div class="panel-card-head">
                                <h2><i class="bi bi-graph-up"></i> Evolución mensual de asistencias</h2>
                            </div>
                            <div class="reporte-chart">
                                <canvas id="graficoAsistenciaMeses" class="reporte-canvas"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card reporte-card">
                    <div class="panel-card-head">
                        <h2><i class="bi bi-person-exclamation"></i> Socios con cuota al día sin asistir hace +15 días</h2>
                    </div>

                    <?php if (! empty($inactivos)): ?>
                        <div class="table-responsive reporte-tabla-wrap">
                            <table class="table table-dark table-hover reporte-tabla">
                                <thead>
                                    <tr>
                                        <th>Socio</th>
                                        <th>Teléfono</th>
                                        <th>Disciplina</th>
                                        <th>Vencimiento</th>
                                        <th>Días sin venir</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($inactivos as $socio): ?>
                                        <?php
                                            $telefono = (string) ($socio['telefono'] ?? '');
                                            $numero   = preg_replace('/\D/', '', $telefono);
                                            if ($numero !== '' && strpos($numero, '54') !== 0) {
                                                $numero = '54' . ltrim($numero, '0');
                                            }
                                        ?>
                                        <tr>
                                            <td><?= esc($socio['nombre']) ?></td>
                                            <td><?= esc($telefono !== '' ? $telefono : '—') ?></td>
                                            <td><?= esc($socio['disciplinas'] !== '' ? $socio['disciplinas'] : '—') ?></td>
                                            <td><?= ! empty($socio['vencimiento']) ? esc(date('d/m/Y', strtotime($socio['vencimiento']))) : '—' ?></td>
                                            <td>
                                                <span class="status-pill is-warn">
                                                    <?= $socio['dias_sin_asistir'] === null
                                                        ? 'Nunca asistió'
                                                        : (int) $socio['dias_sin_asistir'] . ' días' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="reporte-acciones">
                                                    <a class="nfs-btn nfs-btn-secondary nfs-btn-sm"
                                                       href="<?= base_url('cliente_info/' . (int) $socio['id_persona']) ?>">
                                                        Ver ficha
                                                    </a>
                                                    <?php if ($numero !== ''): ?>
                                                        <a class="nfs-btn nfs-btn-outline nfs-btn-sm"
                                                           target="_blank" rel="noopener"
                                                           href="https://wa.me/<?= esc($numero) ?>">
                                                            WhatsApp
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="reporte-metrica-help">No hay socios en esta situación.</div>
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
    let graficoSistemas = null;

    const etiquetasDiasAsistencia = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    const datosDiasAsistencia = [
        <?= (int) ($porDia[1] ?? 0) ?>, <?= (int) ($porDia[2] ?? 0) ?>,
        <?= (int) ($porDia[3] ?? 0) ?>, <?= (int) ($porDia[4] ?? 0) ?>,
        <?= (int) ($porDia[5] ?? 0) ?>, <?= (int) ($porDia[6] ?? 0) ?>,
        <?= (int) ($porDia[7] ?? 0) ?>
    ];
    const etiquetasMesesAsistencia = <?= json_encode(array_column($porMes, 'etiqueta')) ?>;
    const datosMesesAsistencia = <?= json_encode(array_map('intval', array_column($porMes, 'total'))) ?>;
    let graficoAsistenciaSemana = null;
    let graficoAsistenciaMeses = null;

    function crearGraficoSistemas() {
        const lienzo = document.getElementById('graficoSistemas');
        if (!lienzo || graficoSistemas) {
            return;
        }

        graficoSistemas = new Chart(lienzo, {
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
    }

    function crearGraficosAsistencia() {
        const lienzoSemana = document.getElementById('graficoAsistenciaSemana');

        if (lienzoSemana && !graficoAsistenciaSemana) {
            graficoAsistenciaSemana = new Chart(lienzoSemana, {
                type: 'bar',
                data: {
                    labels: etiquetasDiasAsistencia,
                    datasets: [{
                        label: 'Ingresos',
                        data: datosDiasAsistencia,
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
        }

        const lienzoMeses = document.getElementById('graficoAsistenciaMeses');

        if (lienzoMeses && !graficoAsistenciaMeses) {
            graficoAsistenciaMeses = new Chart(lienzoMeses, {
                type: 'line',
                data: {
                    labels: etiquetasMesesAsistencia,
                    datasets: [{
                        label: 'Ingresos',
                        data: datosMesesAsistencia,
                        borderColor: '#10c796',
                        backgroundColor: 'rgba(16, 199, 150, 0.18)',
                        pointBackgroundColor: '#10c796',
                        pointRadius: 3,
                        tension: 0.35,
                        fill: true
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
        }
    }

    function activarPestana(nombre) {
        document.querySelectorAll('.reporte-tab').forEach(function (tab) {
            const activa = tab.dataset.tab === nombre;
            tab.classList.toggle('activa', activa);
            tab.setAttribute('aria-selected', activa ? 'true' : 'false');
        });

        document.querySelectorAll('.reporte-panel').forEach(function (panel) {
            panel.classList.toggle('activo', panel.dataset.panel === nombre);
        });

        if (nombre === 'cuotas') {
            requestAnimationFrame(function () {
                if (!graficoSistemas) {
                    crearGraficoSistemas();
                } else {
                    graficoSistemas.resize();
                }
            });
        }

        if (nombre === 'asistencia') {
            requestAnimationFrame(function () {
                crearGraficosAsistencia();

                if (graficoAsistenciaSemana) {
                    graficoAsistenciaSemana.resize();
                }
                if (graficoAsistenciaMeses) {
                    graficoAsistenciaMeses.resize();
                }
            });
        }
    }

    document.querySelectorAll('.reporte-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            activarPestana(tab.dataset.tab);
        });
    });

    function fechaLocalISO(fecha) {
        const anio = fecha.getFullYear();
        const mes = String(fecha.getMonth() + 1).padStart(2, '0');
        const dia = String(fecha.getDate()).padStart(2, '0');
        return anio + '-' + mes + '-' + dia;
    }

    document.querySelectorAll('[data-fecha]').forEach(function (boton) {
        boton.addEventListener('click', function () {
            const fecha = new Date();
            if (boton.dataset.fecha === 'ayer') {
                fecha.setDate(fecha.getDate() - 1);
            }
            const input = document.getElementById('fecha');
            const formulario = document.getElementById('formFechaCaja');
            if (input) {
                input.value = fechaLocalISO(fecha);
            }
            if (formulario) {
                formulario.submit();
            }
        });
    });

    const inputFecha = document.getElementById('fecha');
    if (inputFecha) {
        inputFecha.addEventListener('change', function () {
            this.form.submit();
        });
    }

    document.querySelectorAll('[data-imprimir]').forEach(function (boton) {
        boton.addEventListener('click', function () {
            window.print();
        });
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
