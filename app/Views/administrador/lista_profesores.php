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
            <h1 style="margin:0;">Lista de Profesores</h1>

            <button type="button"
                    class="btn btn-success"
                    data-bs-toggle="modal"
                    data-bs-target="#modalRegistrarProfesor">
                <i class="bi bi-person-plus"></i> Registrar profesor
            </button>
        </div>

        <input type="text" id="buscarProfesor" class="form-control"
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

        <!-- SCROLL HORIZONTAL SUPERIOR -->
        <div id="scrollTopProfesores"
             style="position:sticky; top:0; z-index:20; overflow-x:auto; overflow-y:hidden; height:14px; background:#262626; display:none;">
            <div id="scrollTopInnerProfesores" style="height:1px;"></div>
        </div>

        <div class="table-responsive" id="tablaScrollProfesores" style="background:#1b1b1b; padding:25px; border-left:5px solid #0b8f70; overflow-x:auto;">
            <table class="table table-dark table-striped table-hover align-middle" id="tablaProfesores">
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Estado</th>
                        <th>Teléfono</th>
                        <th style="text-align:center;"></th>
                    </tr>
                </thead>

                <tbody>
                    <?php if(!empty($profesores)): ?>
                        <?php foreach($profesores as $profesor): ?>
                            <tr>
                                <td><?= esc($profesor['dni']) ?></td>
                                <td><?= esc($profesor['nombre']) ?></td>
                                <td><?= esc($profesor['apellido']) ?></td>
                                <td><?= esc($profesor['telefono']) ?></td>
                                <td>
                                    <?php if($profesor['baja'] == 'S'): ?>
                                        <span class="badge bg-danger">Dado de baja</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php endif; ?>
                                </td>
                                <td style="white-space: nowrap; text-align:center;">
                                    <button type="button"
                                            class="btnVerMas"
                                            data-target="detalle-<?= $profesor['id_persona'] ?>"
                                            aria-expanded="false"
                                            title="Ver detalles"
                                            style="background:transparent; border:none; color:#0b8f70; font-size:22px; line-height:1; padding:2px 6px; cursor:pointer;">
                                        <i class="bi bi-chevron-down" style="font-weight:900; -webkit-text-stroke:1.6px currentColor; font-size:28px;"></i>
                                    </button>
                                </td>
                            </tr>

                            <tr id="detalle-<?= $profesor['id_persona'] ?>" class="fila-detalle" style="display:none;">
                                <td colspan="6">
                                    <div style="background:#111; padding:20px; border-left:4px solid #0b8f70; border-radius:8px;">
                                        <h5 style="margin-bottom:15px;">
                                            Información de <?= esc($profesor['nombre']) ?> <?= esc($profesor['apellido']) ?>
                                        </h5>

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <strong>Nombre usuario:</strong>
                                                <?= !empty($profesor['nombre_usuario']) ? esc($profesor['nombre_usuario']) : '<span style="color:#d98b45;">Sin usuario</span>' ?>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <strong>Email:</strong>
                                                <?= !empty($profesor['email']) ? esc($profesor['email']) : '<span style="color:#d98b45;">Sin email</span>' ?>
                                            </div>
                                        </div>

                                        <div style="margin-top:18px; text-align:right;">
                                            <a href="<?= base_url('editar_profesor/'.$profesor['id_persona']) ?>"
                                               class="btn btn-primary btn-sm">
                                                Editar
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay profesores registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- MODAL REGISTRAR PROFESOR -->
    <div class="modal fade" id="modalRegistrarProfesor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="background:#1b1b1b; color:white; border:1px solid #0b8f70;">
                <div class="modal-header" style="border-bottom:1px solid #333;">
                    <h5 class="modal-title" style="color:#0b8f70;">Registrar nuevo profesor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1);"></button>
                </div>

                <form action="<?= base_url('registrar_profesor') ?>" method="post">
                    <div class="modal-body">
                        <h6 style="margin-bottom:15px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:14px;">
                            Datos del profesor
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre *</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Apellido *</label>
                                <input type="text" name="apellido" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono *</label>
                                <input type="text" name="telefono" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">DNI *</label>
                                <input type="text" name="dni" class="form-control" required>
                            </div>
                        </div>

                        <h6 style="margin:20px 0 15px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:14px;">
                            Cuenta de usuario
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre de usuario *</label>
                                <input type="text" name="usuario" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contraseña *</label>
                                <input type="password" name="pass" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer" style="border-top:1px solid #333;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Registrar profesor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

    // BUSCADOR
    const buscarProfesor = document.getElementById('buscarProfesor');
    const tablaProfesores = document.getElementById('tablaProfesores');

    if (buscarProfesor && tablaProfesores) {
        buscarProfesor.addEventListener('keyup', function () {
            const texto = this.value.toLowerCase();
            const filas = tablaProfesores.querySelectorAll('tbody tr');

            filas.forEach(function (fila) {
                if (fila.classList.contains('fila-detalle')) {
                    return;
                }

                const coincide = fila.textContent.toLowerCase().includes(texto);
                fila.style.display = coincide ? '' : 'none';

                const siguiente = fila.nextElementSibling;
                if (siguiente && siguiente.classList.contains('fila-detalle')) {
                    siguiente.style.display = 'none';
                }
            });
        });
    }

    // DESPLEGAR INFORMACIÓN
    const botonesVerMasProf = document.querySelectorAll('.btnVerMas');

    botonesVerMasProf.forEach(function(boton) {
        boton.addEventListener('click', function() {
            const idDetalle = this.getAttribute('data-target');
            const filaDetalle = document.getElementById(idDetalle);
            const icono = this.querySelector('i');

            if (filaDetalle.style.display === 'none' || filaDetalle.style.display === '') {
                filaDetalle.style.display = 'table-row';
                icono.className = 'bi bi-chevron-up';
                this.setAttribute('aria-expanded', 'true');
            } else {
                filaDetalle.style.display = 'none';
                icono.className = 'bi bi-chevron-down';
                this.setAttribute('aria-expanded', 'false');
            }
        });
    });

    // SCROLL HORIZONTAL SUPERIOR
    const tablaScroll = document.getElementById('tablaScrollProfesores');
    const scrollTop = document.getElementById('scrollTopProfesores');
    const scrollTopInner = document.getElementById('scrollTopInnerProfesores');

    function sincronizarScrollSuperior() {
        if (!tablaScroll || !scrollTop || !scrollTopInner) return;

        scrollTopInner.style.width = tablaScroll.scrollWidth + 'px';

        if (tablaScroll.scrollWidth > tablaScroll.clientWidth) {
            scrollTop.style.display = 'block';
        } else {
            scrollTop.style.display = 'none';
        }
    }

    if (tablaScroll && scrollTop) {
        tablaScroll.addEventListener('scroll', function() {
            scrollTop.scrollLeft = tablaScroll.scrollLeft;
        });

        scrollTop.addEventListener('scroll', function() {
            tablaScroll.scrollLeft = scrollTop.scrollLeft;
        });

        window.addEventListener('resize', sincronizarScrollSuperior);
        sincronizarScrollSuperior();
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
