<?php
$error   = session()->getFlashdata('error');
$success = session()->getFlashdata('success');

$tema       = (string) ($preferencias['tema'] ?? 'dark');
$notifEmail = (int) ($preferencias['notif_email'] ?? 1);
$notifDias  = (int) ($preferencias['notif_dias'] ?? 7);
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

    <section class="dashboard-content home-shell">

        <header class="home-head">
            <div class="home-head-text">
                <span class="home-eyebrow">Mi cuenta</span>
                <h1 class="home-title">Configuración</h1>
                <p class="home-sub">Personalizá la apariencia del sistema y tus notificaciones.</p>
            </div>

            <a class="home-cta ghost" href="<?= base_url('perfil') ?>">
                <i class="bi bi-person"></i>
                Mi perfil
            </a>
        </header>

        <?php if ($error): ?>
            <div class="form-alert error">
                <i class="bi bi-exclamation-triangle"></i>
                <span><?= esc($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="form-alert success">
                <i class="bi bi-check-circle"></i>
                <span><?= esc($success) ?></span>
            </div>
        <?php endif; ?>

        <form class="settings-stack" action="<?= base_url('configuracion/actualizar') ?>" method="post" id="prefForm">

            <article class="panel-card settings-card">
                <div class="panel-card-head">
                    <h2><i class="bi bi-palette"></i> Apariencia</h2>
                </div>

                <div class="settings-block">
                    <span class="settings-block-title">Tema del sitio</span>
                    <div class="option-grid">
                        <label class="option-card">
                            <input type="radio" name="tema" value="dark" <?= $tema === 'dark' ? 'checked' : '' ?>>
                            <i class="bi bi-moon-stars"></i>
                            <span>Oscuro</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="tema" value="light" <?= $tema === 'light' ? 'checked' : '' ?>>
                            <i class="bi bi-sun"></i>
                            <span>Claro</span>
                        </label>
                    </div>
                </div>
            </article>

            <article class="panel-card settings-card">
                <div class="panel-card-head">
                    <h2><i class="bi bi-bell"></i> Notificaciones</h2>
                </div>

                <label class="switch-row">
                    <input type="checkbox" name="notif_email" value="1" <?= $notifEmail ? 'checked' : '' ?>>
                    <span class="switch-text">
                        <strong>Recibir avisos por email</strong>
                        <small>Avisos de vencimientos de cuotas.</small>
                    </span>
                </label>

                <div class="form-group">
                    <label class="form-label" for="notif_dias">Días de anticipación</label>
                    <input type="number" class="form-control form-control-dark" id="notif_dias" name="notif_dias"
                           min="1" max="30" value="<?= (int) $notifDias ?>">
                    <small class="form-help">Entre 1 y 30 días antes del vencimiento.</small>
                </div>
            </article>

            <div class="form-actions form-actions-right">
                <button type="submit" class="home-cta">
                    <i class="bi bi-check2"></i>
                    Guardar cambios
                </button>
            </div>
        </form>

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

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                cerrarSidebar();
            }
        });
    }
</script>
