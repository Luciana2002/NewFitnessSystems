<aside class="dashboard-sidebar" id="dashboardSidebar">

    <div style="text-align:center; padding:10px 0 40px;">
        <img src="<?= base_url('assets/img/logob.png') ?>" style="width:90px;">
    </div>

    <?php if (session()->get('id_rol') == 1): ?>
        <h3>Panel Admin</h3>
        <a href="<?= base_url('clientes') ?>">Clientes</a>
        <a href="<?= base_url('profesores') ?>">Profesores</a>
        <a href="<?= base_url('pagos') ?>">Pagos</a>

        <hr class="sidebar-divider">

        <a href="<?= base_url('sistemas') ?>">Sistemas</a>
        <a href="<?= base_url('admin_horarios') ?>">Horarios</a>
        <a href="<?= base_url('rutinas') ?>">Rutinas</a>

        <hr class="sidebar-divider">

        <a href="<?= base_url('avisos') ?>">Avisos</a>
        <a href="<?= base_url('comentarios') ?>">
            <i class="bi bi-chat-left-text"></i> Sugerencias
            <?php if ((int) ($comentariosNoLeidos ?? 0) > 0): ?>
                <span class="sidebar-badge"><?= (int) $comentariosNoLeidos ?></span>
            <?php endif; ?>
        </a>
        <a href="<?= base_url('suscripciones') ?>">Reportes</a>
        <a href="<?= base_url('checkin') ?>"><i class="bi bi-qr-code"></i> QR Asistencia</a>
        <a href="<?= base_url('backup') ?>">Backup</a>

    <?php elseif (session()->get('id_rol') == 2): ?>
        <h3>Panel Profesor</h3>
        <a href="<?= base_url('pagos') ?>">Pagos</a>
        <a href="<?= base_url('clientes') ?>">Clientes</a>
        <a href="<?= base_url('rutinas') ?>">Rutinas</a>
        <a href="<?= base_url('checkin') ?>"><i class="bi bi-qr-code"></i> QR Asistencia</a>

    <?php else: ?>
        <h3>Mi cuenta</h3>
        <a href="<?= base_url('cliente/suscripcion') ?>">Mi suscripción</a>
        <a href="<?= base_url('cliente/reservas') ?>">Reservar clases</a>
        <a href="<?= base_url('cliente/asistencias') ?>"><i class="bi bi-clock-history"></i> Mis Asistencias</a>
        <a href="<?= base_url('cliente/comentarios') ?>"><i class="bi bi-chat-left-text"></i> Sugerencias</a>
        <a href="<?= base_url('cliente/rutinas') ?>">Mis rutinas</a>
        <a href="<?= base_url('cliente/seguimiento') ?>">Seguimiento físico</a>
    <?php endif; ?>

</aside>