<main class="dashboard-page">

    <button type="button" id="sidebarOpen" class="sidebar-handle">
        <span>›</span>
    </button>

    <button type="button" id="sidebarClose" class="sidebar-close">
        ✕
    </button>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <?= view('layout/sidebar') ?>

    <section class="dashboard-content" style="padding:50px 70px;">

        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:15px;">
            <h1 style="margin:0;">Sistemas</h1>

            <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#modalAgregarSistema">
                <i class="bi bi-plus-lg"></i> Agregar sistema
            </button>
        </div>

        <input type="text" id="buscarSistema" class="form-control"
               placeholder="Buscar..."
               style="max-width:320px; margin-bottom:30px;">

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

        <div class="table-responsive" id="tablaScrollSistemas" style="background:#1b1b1b; padding:25px; border-left:5px solid #0b8f70; overflow-x:auto;">
            <table class="table table-dark table-striped table-hover align-middle" id="tablaSistemas">
                <thead>
                    <tr>
                        <th>Sistema</th>
                        <th>Mensualidad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    <?php if(!empty($sistemas)): ?>
                        <?php foreach($sistemas as $sistema): ?>
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <span style="width:22px; height:22px; border-radius:50%; border:2px solid #fff; background:<?= esc($sistema['color'] ?? '#0b8f70') ?>; flex-shrink:0;"></span>
                                        <?= esc($sistema['nombre_sistema']) ?>
                                    </div>
                                </td>
                                <td><?= formatear_monto($sistema['precio']) ?></td>
                                <td>
                                    <?php if($sistema['baja'] == 'S'): ?>
                                        <span class="badge bg-danger">Dado de baja</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <label class="switch"
                                           title="Encendido: activo — Apagado: dado de baja"
                                           style="margin-bottom:0;">
                                        <input type="checkbox"
                                               class="toggle-estado"
                                               data-url-activar="<?= base_url('alta_sistema/'.$sistema['id_sistema']) ?>"
                                               data-url-desactivar="<?= base_url('baja_sistema/'.$sistema['id_sistema']) ?>"
                                               <?= $sistema['baja'] == 'N' ? 'checked' : '' ?>>
                                        <span class="slider"></span>
                                    </label>
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn btn-primary btn-sm btnEditarSistema"
                                            data-id="<?= $sistema['id_sistema'] ?>"
                                            data-nombre="<?= esc($sistema['nombre_sistema']) ?>"
                                            data-precio="<?= esc($sistema['precio']) ?>"
                                            data-color="<?= esc($sistema['color'] ?? '#0b8f70') ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditarSistema">
                                        Editar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay sistemas registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- MODAL AGREGAR SISTEMA -->
    <div class="modal fade" id="modalAgregarSistema" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background:#1b1b1b; color:white; border:1px solid #0b8f70;">
                <div class="modal-header" style="border-bottom:1px solid #333;">
                    <h5 class="modal-title" style="color:#0b8f70;">Agregar sistema</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1);"></button>
                </div>

                <form action="<?= base_url('guardar_sistema') ?>" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del sistema *</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mensualidad (precio) *</label>
                            <input type="number" step="0.01" min="0" name="precio" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Color del sistema</label>
                            <input type="color" name="color" class="form-control form-control-color" value="#0b8f70" style="height:44px; padding:5px;">
                        </div>
                    </div>

                    <div class="modal-footer" style="border-top:1px solid #333;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Agregar sistema</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR SISTEMA -->
    <div class="modal fade" id="modalEditarSistema" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background:#1b1b1b; color:white; border:1px solid #0b8f70;">
                <div class="modal-header" style="border-bottom:1px solid #333;">
                    <h5 class="modal-title" style="color:#0b8f70;">Editar sistema</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1);"></button>
                </div>

                <form id="formEditarSistema" action="" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del sistema *</label>
                            <input type="text" name="nombre" id="nombreSistema" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mensualidad (precio) *</label>
                            <input type="number" step="0.01" min="0" name="precio" id="precioSistema" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Color del sistema</label>
                            <input type="color" name="color" id="colorSistema" class="form-control form-control-color" value="#0b8f70" style="height:44px; padding:5px;">
                        </div>
                    </div>

                    <div class="modal-footer" style="border-top:1px solid #333;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</main>

<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        vertical-align: middle;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .switch .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #555;
        border-radius: 24px;
        transition: 0.3s;
    }

    .switch .slider::before {
        content: "";
        position: absolute;
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: #fff;
        border-radius: 50%;
        transition: 0.3s;
    }

    .switch input:checked + .slider {
        background-color: #dc3545;
    }

    .switch input:checked + .slider::before {
        transform: translateX(20px);
    }
</style>

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

    // BUSCADOR
    const buscarSistema = document.getElementById('buscarSistema');
    const tablaSistemas = document.getElementById('tablaSistemas');

    if (buscarSistema && tablaSistemas) {
        buscarSistema.addEventListener('keyup', function () {
            const texto = this.value.toLowerCase();
            const filas = tablaSistemas.querySelectorAll('tbody tr');

            filas.forEach(function (fila) {
                const coincide = fila.textContent.toLowerCase().includes(texto);
                fila.style.display = coincide ? '' : 'none';
            });
        });
    }

    <!-- MODAL EDITAR: cargar datos del sistema -->
    const formEditarSistema = document.getElementById('formEditarSistema');
    const botonesEditar = document.querySelectorAll('.btnEditarSistema');

    botonesEditar.forEach(function(boton) {
        boton.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            const precio = this.getAttribute('data-precio');
            const color = this.getAttribute('data-color');

            formEditarSistema.action = '<?= base_url('actualizar_sistema/') ?>' + id;
            document.getElementById('nombreSistema').value = nombre;
            document.getElementById('precioSistema').value = precio;
            document.getElementById('colorSistema').value = color;
        });
    });

    // TOGGLE ACTIVAR / DESACTIVAR
    document.querySelectorAll('.toggle-estado').forEach(function(chk) {
        chk.addEventListener('change', function() {
            const url = this.checked
                ? this.getAttribute('data-url-activar')
                : this.getAttribute('data-url-desactivar');

            if (url) window.location.href = url;
        });
    });

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
