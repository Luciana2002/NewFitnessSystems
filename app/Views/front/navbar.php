<body>
<header class="site-header">
    <div class="container nav-container">

        <a class="brand-logo" href="<?= base_url('/') ?>" 
            style="<?= uri_string() === 'usuario_logueado' ? 'visibility:hidden;' : '' ?>">
            <img src="<?= base_url('assets/img/logob.png') ?>" alt="New Fitness Systems">
        </a>

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
                    <?= session()->get('nombre_usuario') ?>
                </a>

                <a href="<?= base_url('logout') ?>" class="btn-nav mobile-btn">Salir</a>
            <?php else: ?>
                <a href="<?= base_url('login') ?>" class="btn-nav mobile-btn">Iniciar sesión</a>
            <?php endif; ?>
        </nav>

        <?php if (session()->get('logged_in')): ?>
            <div class="desktop-user-area" style="margin-left:auto; display:flex; gap:15px;">
                <a href="<?= base_url('usuario_logueado') ?>" class="user-nav">
                    <i class="bi bi-person-circle"></i>
                    <?= strtoupper(session()->get('nombre_usuario')) ?>
                </a>

                <a href="<?= base_url('logout') ?>" class="btn-nav desktop-btn">Salir</a>
            </div>
        <?php else: ?>
            <a href="<?= base_url('login') ?>" class="btn-nav desktop-btn">Iniciar sesión</a>
        <?php endif; ?>

    </div>
</header>