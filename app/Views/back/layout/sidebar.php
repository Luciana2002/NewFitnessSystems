<aside class="dashboard-sidebar" id="dashboardSidebar">

    <div style="text-align:center; padding:10px 0 40px;">
        <img src="<?= base_url('assets/img/logob.png') ?>" style="width:90px;">
    </div>

    <?php if (session()->get('id_rol') == 1): ?>
        <h3>Panel Admin</h3>
        <a href="<?= base_url('usuarios') ?>">Usuarios</a>
        <a href="<?= base_url('clientes') ?>">Clientes</a>
        <a href="<?= base_url('profesores') ?>">Profesores</a>
        <a href="<?= base_url('sistemas') ?>">Sistemas</a>
        <a href="<?= base_url('admin_horarios') ?>">Horarios</a>
        <a href="<?= base_url('pagos') ?>">Pagos</a>
        <a href="<?= base_url('suscripciones') ?>">Suscripciones</a>

    <?php elseif (session()->get('id_rol') == 2): ?>
        <h3>Panel Profesor</h3>
        <a href="<?= base_url('pagos') ?>">Pagos</a>
        <a href="<?= base_url('clientes') ?>">Clientes</a>

    <?php else: ?>
        <h3>Mi cuenta</h3>
        <a href="<?= base_url('cliente/pagos') ?>">Mis pagos</a>
        <a href="<?= base_url('cliente/suscripcion') ?>">Mi mensualidad</a>
    <?php endif; ?>

</aside>