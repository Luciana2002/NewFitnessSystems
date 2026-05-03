<body>
<header class="site-header">
    <div class="container nav-container">

        <a class="brand-logo" href="<?= base_url('/') ?>">
            <img src="<?= base_url('assets/img/logob.png') ?>" alt="New Fitness Systems">
        </a>

        <button class="menu-toggle" id="menuToggle" type="button">
            ☰
        </button>

        <nav class="nav-links" id="navMenu">
            <a href="<?= base_url('/') ?>">Inicio</a>

            <!-- <div class="custom-dropdown" id="programasDropdown">
                <a class="nav-item-custom dropdown-link" href="#" id="programasToggle">
                    Programas <span class="arrow">▾</span>
                </a>

                <ul class="dropdown-menu-custom">
                    <li><a href="#">BodyPump</a></li>
                    <li><a href="#">PowerJump</a></li>
                    <li><a href="#">Funcional</a></li>
                    <li><a href="#">Zumba</a></li>
                    <li><a href="#">Artes Marciales</a></li>
                </ul>
            </div> -->

            <a href="<?= base_url('horarios') ?>">Horarios</a>
            <a href="<?= base_url('precios') ?>">Precios</a>
            <a href="<?= base_url('contacto') ?>">Contacto</a>
            <a href="<?= base_url('nosotros') ?>">Nosotros</a>

            <a href="#" class="btn-nav mobile-btn">Iniciar sesión</a>
        </nav>

        <a href="#" class="btn-nav desktop-btn">Iniciar sesión</a>

    </div>
</header>