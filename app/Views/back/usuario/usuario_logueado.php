<main class="dashboard-page">

    <aside class="dashboard-sidebar">

        <!-- LOGO PERFECTAMENTE CENTRADO -->
        <div style="text-align:center; padding:10px 0 40px;">
            <img src="<?= base_url('assets/img/logob.png') ?>" style="width:90px;">
        </div>

        <?php if (session()->get('id_rol') == 1): ?>
            <h3>Panel Admin</h3>
            <a href="#">Usuarios</a>
            <a href="#">Clientes</a>
            <a href="#">Profesores</a>
            <a href="#">Sistemas</a>
            <a href="#">Horarios</a>
            <a href="#">Pagos</a>
            <a href="#">Suscripciones</a>

        <?php elseif (session()->get('id_rol') == 2): ?>
            <h3>Panel Profesor</h3>
            <a href="#">Mis horarios</a>
            <a href="#">Clientes inscriptos</a>
            <a href="#">Registrar pago</a>

        <?php else: ?>
            <h3>Mi cuenta</h3>
            <a href="#">Mi suscripción</a>
            <a href="#">Mis inscripciones</a>
            <a href="#">Mis pagos</a>
        <?php endif; ?>

    </aside>

    <section class="dashboard-content" style="padding: 50px 70px;">
        <h1>Bienvenido/a <?= session()->get('nombre') ?> <?= session()->get('apellido') ?></h1>

        <div class="dashboard-card" style="max-width: 1500px; width:100%; padding:35px 40px;">
            <p><strong>Usuario:</strong> <?= session()->get('nombre_usuario') ?></p>
            <p><strong>Email:</strong> <?= session()->get('email') ?></p>
            <p><strong>Rol:</strong> <?= session()->get('rol') ?></p>
        </div>
    </section>

</main>