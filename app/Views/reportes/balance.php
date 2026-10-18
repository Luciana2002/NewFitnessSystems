<?= view('reportes/_print_estilos') ?>

<style>
    input[type="month"],
    input[type="date"] {
        color-scheme: dark;
    }
</style>

<?php
    $esMesActual = $mes === date('Y-m');
    $fechaDefault = $esMesActual ? date('Y-m-d') : $mes . '-01';
    $nombreMes = date('m/Y', strtotime($mes . '-01'));
    $maxCategoria = !empty($gastosPorCategoria) ? max(array_column($gastosPorCategoria, 'total')) : 0;

    $totalCobradoProfes = 0.0;
    foreach ($liquidacion as $prof) {
        $totalCobradoProfes += (float) $prof['cobrado'];
    }
?>

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

        <a href="<?= base_url('suscripciones') ?>" class="no-print" style="display:inline-block; margin-bottom:25px; color:#aaa; font-size:14px; text-decoration:none;">
            ← Volver a reportes
        </a>

        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:25px;">
            <div>
                <h1 style="margin:0;">Gastos y Balance</h1>
                <p style="margin:5px 0 0; color:#aaa;">Ingresos, egresos y ganancia neta del mes.</p>
            </div>
            <?= view('reportes/_acciones', ['exportUrl' => base_url('suscripciones/exportar/balance?mes=' . $mes)]) ?>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success no-print" style="background:#0b8f70; border:none; color:#fff;">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger no-print"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <form method="get" action="<?= base_url('suscripciones/balance') ?>" class="no-print"
              style="display:flex; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:25px;">
            <label for="mes" style="color:#cfcfcf; text-transform:uppercase; letter-spacing:1px; font-size:13px;">Mes</label>
            <input type="month" id="mes" name="mes" value="<?= esc($mes) ?>"
                   onchange="this.form.submit()"
                   style="background:#111; border:1px solid #262626; color:#fff; border-radius:8px; padding:8px 12px;">
        </form>

        <div style="display:flex; gap:15px; flex-wrap:wrap; margin-bottom:30px;">
            <div class="dashboard-card" style="flex:1; min-width:190px; padding:22px;">
                <h6 style="text-transform:uppercase; letter-spacing:1px; color:#28a745;">Ingresos</h6>
                <h2 style="margin:6px 0 0; color:#28a745;">$<?= number_format($ingresos, 0, ',', '.') ?></h2>
            </div>

            <div class="dashboard-card" style="flex:1; min-width:190px; padding:22px;">
                <h6 style="text-transform:uppercase; letter-spacing:1px; color:#ffc107;">Sueldos / comisiones</h6>
                <h2 style="margin:6px 0 0; color:#ffc107;">$<?= number_format($sueldos, 0, ',', '.') ?></h2>
            </div>

            <div class="dashboard-card" style="flex:1; min-width:190px; padding:22px;">
                <h6 style="text-transform:uppercase; letter-spacing:1px; color:#dc3545;">Otros gastos</h6>
                <h2 style="margin:6px 0 0; color:#dc3545;">$<?= number_format($otrosGastos, 0, ',', '.') ?></h2>
            </div>

            <div class="dashboard-card" style="flex:1; min-width:190px; padding:22px;">
                <h6 style="text-transform:uppercase; letter-spacing:1px; color:#20c997;">Ganancia neta</h6>
                <h2 style="margin:6px 0 0; color:<?= $neto >= 0 ? '#20c997' : '#dc3545' ?>;">
                    $<?= number_format($neto, 0, ',', '.') ?>
                </h2>
            </div>
        </div>

        <div class="dashboard-card" style="padding:25px; margin-bottom:30px; max-width:none;">
            <h4 style="margin-bottom:8px; text-transform:uppercase; letter-spacing:1px; color:#ffc107; font-size:18px;">
                Liquidación del mes (sueldos de profesores)
            </h4>

            <form method="post" action="<?= base_url('suscripciones/comision') ?>" class="no-print"
                  style="display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap; margin-bottom:22px; padding:16px; background:#111; border:1px solid #262626; border-radius:10px;">
                <input type="hidden" name="mes" value="<?= esc($mes) ?>">
                <div>
                    <label for="porcentaje" style="color:#cfcfcf; font-size:13px; display:block; margin-bottom:4px; text-transform:uppercase; letter-spacing:1px;">
                        Comisión del profesor (%)
                    </label>
                    <input type="number" id="porcentaje" name="porcentaje" min="0" max="100" step="1" required
                           value="<?= (int) $porcentaje ?>"
                           style="background:#111; border:1px solid #262626; color:#fff; border-radius:8px; padding:8px 12px; width:130px;">
                </div>
                <button type="submit" class="btn btn-success">Guardar</button>
                <span style="color:#888; font-size:12px;">Se aplica a la liquidación y al balance de todos los meses.</span>
            </form>

            <?php if (!empty($liquidacion)): ?>
                <div style="overflow-x:auto;">
                    <table class="table table-dark table-hover" style="background:#111; border-radius:8px; overflow:hidden; min-width:640px;">
                        <thead>
                            <tr style="background:#262626; color:#fff; text-transform:uppercase; letter-spacing:1px; font-size:13px;">
                                <th style="padding:12px;">Profesor</th>
                                <th style="padding:12px;">Usuario</th>
                                <th style="padding:12px;">Cuotas cobradas</th>
                                <th style="padding:12px; text-align:right;">Total cobrado</th>
                                <th style="padding:12px; text-align:right;">Comisión <?= (int) $porcentaje ?>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($liquidacion as $prof): ?>
                                <tr style="color:#ddd; border-bottom:1px solid #262626;">
                                    <td style="padding:12px; overflow-wrap:anywhere;"><?= esc($prof['nombre'] . ' ' . $prof['apellido']) ?></td>
                                    <td style="padding:12px; overflow-wrap:anywhere;">@<?= esc($prof['nombre_usuario']) ?></td>
                                    <td style="padding:12px;"><?= (int) $prof['cuotas'] ?></td>
                                    <td style="padding:12px; text-align:right;">$<?= number_format((float) $prof['cobrado'], 0, ',', '.') ?></td>
                                    <td style="padding:12px; text-align:right; color:#ffc107; font-weight:700;">
                                        $<?= number_format((float) $prof['comision'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background:#262626;">
                                <td colspan="3" style="padding:12px; text-align:right; text-transform:uppercase; letter-spacing:1px; color:#fff;">Total sueldos</td>
                                <td style="padding:12px; text-align:right; color:#fff; font-weight:700;">$<?= number_format($totalCobradoProfes, 0, ',', '.') ?></td>
                                <td style="padding:12px; text-align:right; color:#ffc107; font-weight:700;">$<?= number_format($sueldos, 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php else: ?>
                <p style="color:#aaa;">No hay pagos cobrados por profesores en este mes.</p>
            <?php endif; ?>
        </div>

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="dashboard-card" style="padding:25px; height:100%; max-width:none;">
                    <h4 style="margin-bottom:8px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                        Gastos por categoría
                    </h4>

                    <?php if (!empty($gastosPorCategoria)): ?>
                        <?php foreach ($gastosPorCategoria as $g): ?>
                            <div style="margin-bottom:18px;">
                                <div style="display:flex; justify-content:space-between; gap:10px; color:#ddd; font-size:14px; margin-bottom:5px; overflow-wrap:anywhere;">
                                    <span><?= esc($g['categoria']) ?></span>
                                    <strong>$<?= number_format((float) $g['total'], 0, ',', '.') ?></strong>
                                </div>
                                <div style="background:#262626; border-radius:6px; height:14px; overflow:hidden;">
                                    <div style="width:<?= $maxCategoria > 0 ? round(((float) $g['total'] / $maxCategoria) * 100) : 0 ?>%; background:#dc3545; height:100%;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color:#aaa;">No hay gastos cargados en este mes.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="dashboard-card" style="padding:25px; height:100%; max-width:none;">
                    <h4 style="margin-bottom:20px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                        Registrar gasto
                    </h4>

                    <form method="post" action="<?= base_url('gastos/guardar') ?>" class="no-print">
                        <input type="hidden" name="mes" value="<?= esc($mes) ?>">

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label style="color:#cfcfcf; font-size:13px;">Fecha</label>
                                <input type="date" name="fecha" value="<?= esc($fechaDefault) ?>" required
                                       style="background:#111; border:1px solid #262626; color:#fff; border-radius:8px; padding:8px 12px; width:100%;">
                            </div>

                            <div class="col-md-4">
                                <label style="color:#cfcfcf; font-size:13px;">Categoría</label>
                                <select name="categoria" required
                                        style="background:#111; border:1px solid #262626; color:#fff; border-radius:8px; padding:8px 12px; width:100%;">
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= esc($cat, 'attr') ?>"><?= esc($cat) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label style="color:#cfcfcf; font-size:13px;">Monto</label>
                                <input type="number" name="monto" min="0" step="0.01" required placeholder="0"
                                       style="background:#111; border:1px solid #262626; color:#fff; border-radius:8px; padding:8px 12px; width:100%;">
                            </div>

                            <div class="col-12">
                                <label style="color:#cfcfcf; font-size:13px;">Detalle (opcional)</label>
                                <input type="text" name="detalle" placeholder="Ej: Alquiler de octubre"
                                       style="background:#111; border:1px solid #262626; color:#fff; border-radius:8px; padding:8px 12px; width:100%;">
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-plus-lg"></i> Agregar gasto
                                </button>
                            </div>
                        </div>
                    </form>

                    <hr style="border-color:#262626;">

                    <h4 style="margin-bottom:15px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:16px;">
                        Gastos del mes
                    </h4>

                    <?php if (!empty($listaGastos)): ?>
                        <div style="overflow-x:auto;">
                            <table class="table table-dark table-hover" style="background:#111; border-radius:8px; overflow:hidden; min-width:600px;">
                                <thead>
                                    <tr style="background:#262626; color:#fff; text-transform:uppercase; letter-spacing:1px; font-size:13px;">
                                        <th style="padding:10px;">Fecha</th>
                                        <th style="padding:10px;">Categoría</th>
                                        <th style="padding:10px;">Detalle</th>
                                        <th style="padding:10px;">Cargado por</th>
                                        <th style="padding:10px; text-align:right;">Monto</th>
                                        <th style="padding:10px;" class="no-print"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listaGastos as $g): ?>
                                        <?php
                                            $cargadoPor = trim(($g['cargado_por_persona'] ?? '') . ' ' . ($g['cargado_por_apellido'] ?? ''));
                                            if ($cargadoPor === '') {
                                                $cargadoPor = $g['cargado_por_nombre'] ?: '—';
                                            }
                                        ?>
                                        <tr style="color:#ddd; border-bottom:1px solid #262626;">
                                            <td style="padding:10px; white-space:nowrap;"><?= esc(date('d/m/Y', strtotime($g['fecha']))) ?></td>
                                            <td style="padding:10px;"><?= esc($g['categoria']) ?></td>
                                            <td style="padding:10px; overflow-wrap:anywhere;"><?= esc($g['detalle'] ?: '—') ?></td>
                                            <td style="padding:10px; overflow-wrap:anywhere;"><?= esc($cargadoPor) ?></td>
                                            <td style="padding:10px; text-align:right; color:#dc3545; font-weight:700;">$<?= number_format((float) $g['monto'], 0, ',', '.') ?></td>
                                            <td style="padding:10px; text-align:right;" class="no-print">
                                                <a href="<?= base_url('gastos/eliminar/' . $g['id_gasto'] . '?mes=' . $mes) ?>"
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('¿Eliminar este gasto?');">✕</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr style="background:#262626;">
                                        <td colspan="4" style="padding:10px; text-align:right; text-transform:uppercase; letter-spacing:1px; color:#fff;">Total otros gastos</td>
                                        <td style="padding:10px; text-align:right; color:#dc3545; font-weight:700;">$<?= number_format($otrosGastos, 0, ',', '.') ?></td>
                                        <td class="no-print"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php else: ?>
                        <p style="color:#aaa;">No hay gastos cargados en este mes.</p>
                    <?php endif; ?>
                </div>
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
