<body>
<header class="site-header">
    <div class="container nav-container">

        <?php
        $uri = uri_string();

        $ocultarLogo =
            $uri === 'usuario_logueado' ||
            $uri === 'perfil' ||
            $uri === 'configuracion' ||
            $uri === 'personas' ||
            str_starts_with($uri, 'editar_persona') ||
            $uri === 'clientes' ||
            str_starts_with($uri, 'cliente_info') ||
            str_starts_with($uri, 'editar_cliente') ||
            $uri === 'profesores' ||
            $uri === 'sistemas' ||
            $uri === 'pagos' ||
            $uri === 'suscripciones' ||
            str_starts_with($uri, 'suscripciones') ||
            str_starts_with($uri, 'cliente/') ||
            $uri === 'checkin' ||
            $uri === 'rutinas' ||
            str_starts_with($uri, 'rutinas/') ||
            $uri === 'avisos' ||
            str_starts_with($uri, 'avisos') ||
            $uri === 'admin_horarios';

        $nombreCompleto = trim((string) session()->get('nombre') . ' ' . (string) session()->get('apellido'));

        $iniciales = strtoupper(
            mb_substr((string) session()->get('nombre'), 0, 1) .
            mb_substr((string) session()->get('apellido'), 0, 1)
        );

        if ($iniciales === '') {
            $iniciales = '?';
        }

        $rolUsuario = (string) (session()->get('rol') ?: 'Usuario');
        ?>

        <?php if (!$ocultarLogo): ?>
            <a class="brand-logo" href="<?= base_url('/') ?>">
                <img src="<?= base_url('assets/img/logob.png') ?>" alt="New Fitness Systems">
            </a>
        <?php endif; ?>

        <button class="menu-toggle" id="menuToggle" type="button">☰</button>

        <nav class="nav-links" id="navMenu">
            <a href="<?= base_url('/') ?>">Inicio</a>
            <a href="<?= base_url('horarios') ?>">Horarios</a>
            <a href="<?= base_url('precios') ?>">Precios</a>
            <a href="<?= base_url('contacto') ?>">Contacto</a>
            <a href="<?= base_url('nosotros') ?>">Nosotros</a>

            <?php if (session()->get('logged_in')): ?>
                <a href="<?= base_url('usuario_logueado') ?>" class="user-nav mobile-btn">
                    <i class="bi bi-person-circle"></i>
                    Mi panel
                </a>

                <a href="<?= base_url('perfil') ?>" class="mobile-btn">Mi perfil</a>
                <a href="<?= base_url('configuracion') ?>" class="mobile-btn">Configuración</a>
                <a href="<?= base_url('logout') ?>" class="btn-nav mobile-btn">Cerrar sesión</a>
            <?php else: ?>
                <a href="<?= base_url('login') ?>" class="btn-nav mobile-btn">Iniciar sesión</a>
            <?php endif; ?>
        </nav>

        <?php if (session()->get('logged_in')): ?>
            <div class="desktop-user-area">
                <div class="dropdown profile-dropdown">
                    <button class="profile-trigger" type="button" id="profileToggle"
                            data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menú de usuario">
                        <span class="profile-avatar"><?= esc($iniciales) ?></span>
                        <span class="profile-meta">
                            <span class="profile-name"><?= esc($nombreCompleto) ?></span>
                            <span class="profile-role"><?= esc($rolUsuario) ?></span>
                        </span>
                        <i class="bi bi-chevron-down profile-caret"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end profile-menu" aria-labelledby="profileToggle">
                        <li class="profile-menu-header">
                            <span class="profile-avatar profile-avatar-lg"><?= esc($iniciales) ?></span>
                            <div class="profile-menu-info">
                                <strong><?= esc($nombreCompleto) ?></strong>
                                <span><?= esc(session()->get('email')) ?></span>
                                <span class="profile-chip"><?= esc($rolUsuario) ?></span>
                            </div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('usuario_logueado') ?>">
                                <i class="bi bi-grid-1x2"></i> Mi panel
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('perfil') ?>">
                                <i class="bi bi-person"></i> Mi perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('configuracion') ?>">
                                <i class="bi bi-gear"></i> Configuración
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item profile-logout" href="<?= base_url('logout') ?>">
                                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        <?php else: ?>
            <a href="<?= base_url('login') ?>" class="btn-nav desktop-btn">Iniciar sesión</a>
        <?php endif; ?>

    </div>
</header>