<?php
$idRol    = (int) session()->get('id_rol');
$rol      = (string) (session()->get('rol') ?: 'Usuario');
$nombre   = (string) $persona['nombre'];
$apellido = (string) $persona['apellido'];

$error   = session()->getFlashdata('error');
$success = session()->getFlashdata('success');

$iniciales = strtoupper(mb_substr($nombre, 0, 1) . mb_substr($apellido, 0, 1));

if ($iniciales === '') {
    $iniciales = '?';
}

$activo = ((string) $persona['baja'] === 'N');
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
                <span class="home-eyebrow"><?= esc('Mi cuenta') ?></span>
                <h1 class="home-title"><?= esc('Mi perfil') ?></h1>
                <p class="home-sub">Editá tus datos personales y tu contraseña desde acá.</p>
            </div>

            <a class="home-cta ghost" href="<?= base_url('configuracion') ?>">
                <i class="bi bi-sliders"></i>
                <?= esc('Configuración') ?>
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

        <div class="profile-hero panel-card">
            <span class="profile-avatar profile-avatar-xl"><?= esc($iniciales) ?></span>

            <div class="profile-hero-info">
                <h2><?= esc($nombre . ' ' . $apellido) ?></h2>
                <p><?= esc($persona['email']) ?></p>

                <div class="profile-hero-tags">
                    <span class="profile-chip"><?= esc($rol) ?></span>
                    <span class="status-pill <?= $activo ? 'is-ok' : 'is-danger' ?>">
                        <?= $activo ? 'Cuenta activa' : 'Cuenta dada de baja' ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="settings-grid">
            <form class="panel-card settings-card" action="<?= base_url('perfil/actualizar') ?>" method="post">
                <div class="panel-card-head">
                    <h2><i class="bi bi-person-vcard"></i> <?= esc('Datos personales') ?></h2>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="nombre">Nombre</label>
                        <input type="text" class="form-control form-control-dark" id="nombre" name="nombre"
                               value="<?= esc(old('nombre', $persona['nombre'])) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="apellido">Apellido</label>
                        <input type="text" class="form-control form-control-dark" id="apellido" name="apellido"
                               value="<?= esc(old('apellido', $persona['apellido'])) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="email">Correo electrónico</label>
                        <input type="email" class="form-control form-control-dark" id="email" name="email"
                               value="<?= esc(old('email', $persona['email'])) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="telefono">Teléfono</label>
                        <input type="text" class="form-control form-control-dark" id="telefono" name="telefono"
                               value="<?= esc(old('telefono', $persona['telefono'])) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="dni">DNI</label>
                        <input type="text" class="form-control form-control-dark" id="dni" name="dni"
                               value="<?= esc(old('dni', $persona['dni'])) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="nombre_usuario">Usuario</label>
                        <input type="text" class="form-control form-control-dark" id="nombre_usuario"
                               value="<?= esc($usuario['nombre_usuario'] ?? '') ?>" disabled>
                        <small class="form-help">El nombre de usuario no se puede modificar.</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="home-cta">
                        <i class="bi bi-check2"></i>
                        <?= esc('Guardar cambios') ?>
                    </button>
                </div>
            </form>

            <form class="panel-card settings-card" action="<?= base_url('perfil/password') ?>" method="post">
                <div class="panel-card-head">
                    <h2><i class="bi bi-shield-lock"></i> <?= esc('Seguridad') ?></h2>
                </div>

                <p class="settings-note">
                    Usá una contraseña de al menos 6 caracteres. Se guarda cifrada.
                </p>

                <div class="form-group">
                    <label class="form-label" for="actual">Contraseña actual</label>
                    <input type="password" class="form-control form-control-dark" id="actual" name="actual"
                           autocomplete="current-password" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="nueva">Nueva contraseña</label>
                    <input type="password" class="form-control form-control-dark" id="nueva" name="nueva"
                           minlength="6" autocomplete="new-password" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="repetir">Repetir nueva contraseña</label>
                    <input type="password" class="form-control form-control-dark" id="repetir" name="repetir"
                           minlength="6" autocomplete="new-password" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="home-cta">
                        <i class="bi bi-key"></i>
                        Actualizar contraseña
                    </button>
                </div>
            </form>
        </div>

        <?php if ($idRol === 3 && !empty($membresia)): ?>
            <?php
            $estadoMembresia = $membresia['estado'];

            if ($estadoMembresia === 'sin_suscripcion') {
                $claseMembresia = 'is-muted';
                $textoMembresia = 'Sin suscripción';
            } elseif ($membresia['activo']) {
                $claseMembresia = ((int) $membresia['dias']) <= 5 ? 'is-warn' : 'is-ok';
                $textoMembresia = ((int) $membresia['dias']) <= 5 ? 'Por vencer' : 'Activa';
            } else {
                $claseMembresia = 'is-danger';
                $textoMembresia = 'Vencida';
            }
            ?>

            <article class="panel-card">
                <div class="panel-card-head">
                    <h2><i class="bi bi-collection"></i> Mis sistemas</h2>
                    <span class="status-pill <?= $claseMembresia ?>"><?= esc($textoMembresia) ?></span>
                </div>

                <?php if (empty($sistemas)): ?>
                    <p class="panel-empty">Todavía no estás inscripto en ningún sistema.</p>
                <?php else: ?>
                    <ul class="panel-list">
                        <?php foreach ($sistemas as $sistema): ?>
                            <?php
                            $estadoSistema = (string) ($sistema['estado_suscripcion'] ?? '');

                            $claseSistema = match ($estadoSistema) {
                                'Activa' => 'is-ok',
                                'Vencida' => 'is-danger',
                                'Cancelada' => 'is-muted',
                                default => 'is-info',
                            };
                            ?>
                            <li class="list-row">
                                <div class="list-main">
                                    <strong><?= esc($sistema['nombre_sistema']) ?></strong>
                                    <span>Inscripto el <?= esc(formatear_fecha($sistema['fecha_inscripcion'])) ?></span>
                                </div>
                                <div class="list-side">
                                    <span class="status-pill <?= $claseSistema ?>"><?= esc($estadoSistema ?: 'Sin estado') ?></span>
                                    <span class="list-date">Vence <?= esc(formatear_fecha($sistema['fecha_vencimiento'])) ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </article>
        <?php endif; ?>

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
