<?= view('reportes/_print_estilos') ?>

<style>
    input[type="date"],
    input[type="month"] {
        color-scheme: dark;
    }
</style>

<?php
    $accionesBadge = [
        'ALTA'    => '#28a745',
        'BAJA'    => '#dc3545',
        'EDICION' => '#ffc107',
        'PRECIO'  => '#17a2b8',
    ];

    $queryFiltros = array_filter($filtros, static fn ($v) => $v !== '' && $v !== null);
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
                <h1 style="margin:0;">Historial de movimientos</h1>
                <p style="margin:5px 0 0; color:#aaa;">Registro de acciones realizadas en el sistema.</p>
            </div>
            <?= view('reportes/_acciones', ['exportUrl' => base_url('suscripciones/exportar/bitacora') . '?' . http_build_query($queryFiltros)]) ?>
        </div>

        <form method="get" action="<?= base_url('suscripciones/bitacora') ?>" class="no-print dashboard-card"
              style="padding:20px; margin-bottom:25px; max-width:none;">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label style="color:#cfcfcf; font-size:13px;">Entidad</label>
                    <select name="entidad" style="background:#111; border:1px solid #262626; color:#fff; border-radius:8px; padding:8px; width:100%;">
                        <option value="">Todas</option>
                        <?php foreach ($entidades as $e): ?>
                            <option value="<?= esc($e['entidad'], 'attr') ?>" <?= $filtros['entidad'] === $e['entidad'] ? 'selected' : '' ?>>
                                <?= esc($e['entidad']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label style="color:#cfcfcf; font-size:13px;">Acción</label>
                    <select name="accion" style="background:#111; border:1px solid #262626; color:#fff; border-radius:8px; padding:8px; width:100%;">
                        <option value="">Todas</option>
                        <?php foreach (['ALTA', 'BAJA', 'EDICION', 'PRECIO'] as $acc): ?>
                            <option value="<?= $acc ?>" <?= $filtros['accion'] === $acc ? 'selected' : '' ?>><?= $acc ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label style="color:#cfcfcf; font-size:13px;">Usuario</label>
                    <select name="usuario" style="background:#111; border:1px solid #262626; color:#fff; border-radius:8px; padding:8px; width:100%;">
                        <option value="">Todos</option>
                        <?php foreach ($usuarios as $u): ?>
                            <option value="<?= (int) $u['id_usuario'] ?>" <?= $filtros['usuario'] === (string) $u['id_usuario'] ? 'selected' : '' ?>>
                                <?= esc($u['nombre_usuario']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label style="color:#cfcfcf; font-size:13px;">Desde</label>
                    <input type="date" name="desde" value="<?= esc($filtros['desde']) ?>"
                           style="background:#111; border:1px solid #262626; color:#fff; border-radius:8px; padding:8px; width:100%;">
                </div>

                <div class="col-md-2">
                    <label style="color:#cfcfcf; font-size:13px;">Hasta</label>
                    <input type="date" name="hasta" value="<?= esc($filtros['hasta']) ?>"
                           style="background:#111; border:1px solid #262626; color:#fff; border-radius:8px; padding:8px; width:100%;">
                </div>

                <div class="col-md-2">
                    <label style="color:#cfcfcf; font-size:13px;">Buscar</label>
                    <input type="text" name="buscar" value="<?= esc($filtros['buscar']) ?>" placeholder="Texto..."
                           style="background:#111; border:1px solid #262626; color:#fff; border-radius:8px; padding:8px; width:100%;">
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-funnel"></i> Filtrar</button>
                    <a href="<?= base_url('suscripciones/bitacora') ?>" class="btn btn-outline-light btn-sm">Limpiar</a>
                </div>
            </div>
        </form>

        <div class="dashboard-card" style="padding:25px; max-width:none;">
            <h4 style="margin-bottom:20px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                Registros (<?= count($registros) ?>)
            </h4>

            <?php if (!empty($registros)): ?>
                <div style="overflow-x:auto;">
                    <table class="table table-dark table-hover" style="background:#111; border-radius:8px; overflow:hidden; min-width:760px;">
                        <thead>
                            <tr style="background:#262626; color:#fff; text-transform:uppercase; letter-spacing:1px; font-size:13px;">
                                <th style="padding:10px;">Fecha</th>
                                <th style="padding:10px;">Usuario</th>
                                <th style="padding:10px;">Acción</th>
                                <th style="padding:10px;">Entidad</th>
                                <th style="padding:10px;">ID</th>
                                <th style="padding:10px;">Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $r): ?>
                                <?php $color = $accionesBadge[$r['accion']] ?? '#6c757d'; ?>
                                <tr style="color:#ddd; border-bottom:1px solid #262626;">
                                    <td style="padding:10px; white-space:nowrap;"><?= esc(date('d/m/Y H:i', strtotime($r['fecha']))) ?></td>
                                    <td style="padding:10px; overflow-wrap:anywhere;"><?= esc($r['nombre_usuario'] ?: 'sistema') ?></td>
                                    <td style="padding:10px;">
                                        <span style="background:<?= $color ?>; color:#111; font-weight:700; font-size:11px; text-transform:uppercase; letter-spacing:1px; padding:3px 9px; border-radius:20px;">
                                            <?= esc($r['accion']) ?>
                                        </span>
                                    </td>
                                    <td style="padding:10px;"><?= esc($r['entidad']) ?></td>
                                    <td style="padding:10px;"><?= $r['id_entidad'] !== null ? (int) $r['id_entidad'] : '—' ?></td>
                                    <td style="padding:10px; overflow-wrap:anywhere;"><?= esc($r['detalle'] ?: '—') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="color:#aaa;">No hay registros que coincidan con los filtros.</p>
            <?php endif; ?>
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
