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

    <section class="dashboard-content" style="padding:50px 70px;">

        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:25px;">
            <div>
                <h1 style="margin:0;">Backup de la base de datos</h1>
                <p style="margin:5px 0 0; color:#aaa;">Descargá un respaldo en .sql o importá uno para restaurar los datos.</p>
            </div>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success no-print" style="background:#0b8f70; border:none; color:#fff;">
                <?= esc($success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger no-print" style="background:#dc3545; border:none; color:#fff;">
                <?= esc($error) ?>
            </div>
        <?php endif; ?>

        <div class="row g-4 no-print">

            <div class="col-lg-6">
                <div class="dashboard-card" style="padding:25px; height:100%; max-width:none;">
                    <h4 style="margin-bottom:10px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                        Descargar backup
                    </h4>
                    <p style="color:#aaa; font-size:14px; margin-bottom:20px;">
                        Genera un archivo <strong>.sql</strong> con la <strong>estructura</strong> (base, tablas,
                        claves y relaciones) y <strong>todos los datos</strong> (<?= esc($hoy) ?>). Ejecutándolo en otro
                        servidor recrea la base completa. También se puede importar en esta sección.
                    </p>

                    <a href="<?= base_url('backup/descargar') ?>"
                       class="nfs-btn nfs-btn-primary">
                        Descargar .sql
                    </a>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="dashboard-card" style="padding:25px; height:100%; max-width:none; border-left:4px solid #ffc107;">
                    <h4 style="margin-bottom:10px; text-transform:uppercase; letter-spacing:1px; color:#ffc107; font-size:18px;">
                        Importar backup
                    </h4>
                    <p style="color:#aaa; font-size:14px; margin-bottom:20px;">
                        Seleccioná un archivo <strong>.sql</strong> generado por esta sección.
                        Los datos actuales se <strong style="color:#dc3545;">reemplazan</strong> por los del backup.
                    </p>

                    <form method="post" action="<?= base_url('backup/importar') ?>" enctype="multipart/form-data" id="formBackup"
                          style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                        <input type="file" name="archivo" accept=".sql" required
                               style="color:#ddd; font-size:13px;">
                        <button type="submit"
                                class="nfs-btn nfs-btn-warning">
                            Importar
                        </button>
                    </form>

                    <p id="backupCargando" style="display:none; margin-top:14px; color:#ffc107; font-size:14px;">
                        Importando, no cierres esta ventana…
                    </p>
                </div>
            </div>
        </div>

        <div class="dashboard-card" style="padding:25px; max-width:none; margin-top:25px;">
            <h4 style="margin-bottom:20px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:18px;">
                Contenido de la base
            </h4>

            <?php if (!empty($tablas)): ?>
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(230px, 1fr)); gap:12px;">
                    <?php foreach ($tablas as $t): ?>
                        <div style="background:#111; border:1px solid #262626; border-radius:8px; padding:14px; display:flex; justify-content:space-between; gap:10px; align-items:center;">
                            <span style="color:#ddd; font-size:14px; overflow-wrap:anywhere;"><?= esc($t) ?></span>
                            <strong style="color:#17a2b8; white-space:nowrap;">
                                <?= isset($filas[$t]) ? number_format($filas[$t], 0, ',', '.') : '0' ?> filas
                            </strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color:#aaa;">No se pudieron listar las tablas.</p>
            <?php endif; ?>
        </div>

    </section>

</main>

<script>
    (function () {
        var form = document.getElementById('formBackup');
        var aviso = document.getElementById('backupCargando');
        if (form) {
            form.addEventListener('submit', function () {
                if (form.checkValidity()) {
                    aviso.style.display = 'block';
                    form.querySelector('button[type="submit"]').disabled = true;
                }
            });
        }
    })();
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
