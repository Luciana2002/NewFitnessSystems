<main class="dashboard-page">

    <!-- BOTÓN LATERAL (flecha) -->
    <button type="button" id="sidebarOpen" class="sidebar-handle">
        <span>›</span>
    </button>

    <button type="button" id="sidebarClose" class="sidebar-close">
        ✕
    </button>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <?= view('back/layout/sidebar') ?>

    <section class="dashboard-content" style="padding:50px 70px;">

        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:30px;">
            <h1 style="margin:0;">Lista de Usuarios</h1>

            <input type="text" id="buscarUsuario" class="form-control"
                   placeholder="Buscar usuario..."
                   style="max-width:320px;">
        </div>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="toast-custom success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="toast-custom error">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive" style="background:#1b1b1b; padding:25px; border-left:5px solid #0b8f70; overflow-x:auto;">
            <table class="table table-dark table-striped table-hover" id="tablaUsuarios">
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Rol</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Usuario</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if($usuarios): ?>
                        <?php foreach($usuarios as $usuario): ?>
                            <tr>
                                <td><?= $usuario['dni'] ?></td>
                                <td><?= $usuario['nombre'] ?></td>
                                <td><?= $usuario['apellido'] ?></td>
                                <td><?= $usuario['rol'] ?></td>
                                <td><?= $usuario['email'] ?></td>
                                <td><?= $usuario['telefono'] ?></td>
                                <td><?= $usuario['nombre_usuario'] ?></td>
                                <td><?= $usuario['baja'] == 'S' ? 'Baja' : 'Activo' ?></td>

                                <td style="white-space: nowrap;">
                                    <div style="display:flex; gap:6px; flex-wrap:nowrap;">
                                        <a href="<?= base_url('editar_usuario/'.$usuario['id_usuario']) ?>" class="btn btn-primary btn-sm">
                                            Editar
                                        </a>

                                        <?php if($usuario['baja'] == 'N'): ?>
                                            <a href="<?= base_url('baja_usuario/'.$usuario['id_usuario']) ?>" class="btn btn-danger btn-sm">
                                                Dar de baja
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url('alta_usuario/'.$usuario['id_usuario']) ?>" class="btn btn-success btn-sm">
                                                Activar
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>

            </table>
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

    const buscarUsuario = document.getElementById('buscarUsuario');
    const tablaUsuarios = document.getElementById('tablaUsuarios');

    if (buscarUsuario && tablaUsuarios) {
        buscarUsuario.addEventListener('keyup', function () {
            const texto = this.value.toLowerCase();
            const filas = tablaUsuarios.querySelectorAll('tbody tr');

            filas.forEach(function (fila) {
                fila.style.display = fila.textContent.toLowerCase().includes(texto) ? '' : 'none';
            });
        });
    }

    const toasts = document.querySelectorAll('.toast-custom');

    toasts.forEach((toast, index) => {
        setTimeout(() => {
            toast.style.transition = "all 0.4s ease";
            toast.style.opacity = "1";
            toast.style.transform = "translateY(0)";
        }, 100);

        setTimeout(() => {
            toast.style.opacity = "0";
            toast.style.transform = "translateY(-20px)";
            setTimeout(() => toast.remove(), 400);
        }, 2500 + (index * 200));
    });
</script>