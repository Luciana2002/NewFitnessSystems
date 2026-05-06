<main class="dashboard-page">

    <!-- BOTÓN LATERAL -->
    <button type="button" id="sidebarOpen" class="sidebar-handle">
        <span>›</span>
    </button>

    <button type="button" id="sidebarClose" class="sidebar-close">
        ✕
    </button>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <?= view('back/layout/sidebar') ?>

    <section class="dashboard-content" style="padding:50px 70px;">

        <!-- TÍTULO + BUSCADOR -->
        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:30px;">
            <h1 style="margin:0;">Lista de Clientes</h1>

            <input type="text" id="buscarCliente" class="form-control"
                   placeholder="Buscar cliente..."
                   style="max-width:320px;">
        </div>

        <!-- ALERTAS -->
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

        <!-- TABLA -->
        <div class="table-responsive" style="background:#1b1b1b; padding:25px; border-left:5px solid #0b8f70; overflow-x:auto;">
            <table class="table table-dark table-striped table-hover" id="tablaClientes">
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if($clientes): ?>
                        <?php foreach($clientes as $cliente): ?>
                            <tr>
                                <td><?= $cliente['dni'] ?></td>
                                <td><?= $cliente['nombre'] ?></td>
                                <td><?= $cliente['apellido'] ?></td>
                                <td><?= $cliente['email'] ?></td>
                                <td><?= $cliente['telefono'] ?></td>

                                <!-- ESTADO -->
                                <td>
                                    <?= $cliente['baja'] == 'S' ? 'Baja' : 'Activo' ?>
                                </td>

                                <!-- ACCIONES -->
                                <td style="white-space: nowrap;">
                                    <div style="display:flex; gap:6px;">

                                        <a href="<?= base_url('editar_cliente/'.$cliente['id_persona']) ?>"
                                           class="btn btn-primary btn-sm">
                                            Editar
                                        </a>

                                        <?php if($cliente['baja'] == 'N'): ?>
                                            <a href="<?= base_url('baja_cliente/'.$cliente['id_persona']) ?>"
                                               class="btn btn-danger btn-sm">
                                                Dar de baja
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url('alta_cliente/'.$cliente['id_persona']) ?>"
                                                class="btn btn-success btn-sm">
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
    }

    // BUSCADOR
    const buscarCliente = document.getElementById('buscarCliente');
    const tablaClientes = document.getElementById('tablaClientes');

    if (buscarCliente && tablaClientes) {
        buscarCliente.addEventListener('keyup', function () {
            const texto = this.value.toLowerCase();
            const filas = tablaClientes.querySelectorAll('tbody tr');

            filas.forEach(function (fila) {
                fila.style.display = fila.textContent.toLowerCase().includes(texto) ? '' : 'none';
            });
        });
    }

    // TOASTS
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