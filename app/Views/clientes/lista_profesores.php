<main class="dashboard-page">

    <button type="button" id="sidebarOpen" class="sidebar-handle">
        <span>›</span>
    </button>

    <button type="button" id="sidebarClose" class="sidebar-close">
        ✕
    </button>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <?= view('back/layout/sidebar') ?>

    <section class="dashboard-content" style="padding:50px 70px;">

        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:15px;">
            <h1 style="margin:0;">Lista de Profesores</h1>
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
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if(!empty($profesores)): ?>
                        <?php foreach($profesores as $profesor): ?>
                            <?php $sistemas = $profesor['sistemas'] ?? []; ?>

                            <tr>
                                <td><?= esc($profesor['dni']) ?></td>
                                <td><?= esc($profesor['nombre']) ?></td>
                                <td><?= esc($profesor['apellido']) ?></td>
                                <td>
                                    <?php if($profesor['baja'] == 'S'): ?>
                                        <span class="badge bg-danger">Dado de baja</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($profesor['telefono']) ?></td>
                                <td style="white-space: nowrap;">
                                    <div style="display:flex; gap:6px;">
                                        <a href="<?= base_url('cliente_info/'.$profesor['id_persona']) ?>"
                                           class="btn btn-outline-success btn-sm">
                                            Ver más
                                        </a>

                                        <a href="<?= base_url('editar_cliente/'.$profesor['id_persona']) ?>"
                                           class="btn btn-primary btn-sm">
                                            Editar
                                        </a>

                                        <?php if($profesor['baja'] == 'N'): ?>
                                            <a href="<?= base_url('baja_cliente/'.$profesor['id_persona']) ?>"
                                               class="btn btn-danger btn-sm">
                                                Dar de baja
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url('alta_cliente/'.$profesor['id_persona']) ?>"
                                               class="btn btn-success btn-sm">
                                                Activar
                                            </a>
                                        <?php endif; ?>

                                        <button type="button"
                                                class="btn btn-success btn-sm btnVerMas"
                                                data-target="detalle-<?= $profesor['id_persona'] ?>">
                                            +
                                        </button>
                                    </div>
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
                                                <strong>Email:</strong>
                                                <?= esc($profesor['email'] ?? 'Sin email') ?>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <strong>Usuario:</strong>
                                                <?= !empty($profesor['nombre_usuario']) ? esc($profesor['nombre_usuario']) : 'No tiene usuario registrado' ?>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <strong>Rol:</strong>
                                                <?= esc($profesor['rol'] ?? 'Profesor') ?>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <strong>Último pago:</strong>
                                                <?= formatear_fecha($profesor['ultimo_pago'] ?? null) ?>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <strong>Estado persona:</strong>
                                                <?= ($profesor['baja'] ?? 'N') == 'S' ? 'Dado de baja' : 'Activo' ?>
                                            </div>
                                        </div>

                                        <?php if(!empty($sistemas)): ?>
                                            <h6 style="margin:18px 0 10px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70;">
                                                Sistemas / suscripciones
                                            </h6>

                                            <div class="table-responsive">
                                                <table class="table table-dark table-sm align-middle mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Sistema</th>
                                                            <th>Inscripción</th>
                                                            <th>Inicio suscripción</th>
                                                            <th>Vencimiento</th>
                                                            <th>Estado</th>
                                                            <th>Mensualidad</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($sistemas as $sistema): ?>
                                                            <?php
                                                                $estadoSus = $sistema['estado_suscripcion'] ?? 'Sin suscripción';

                                                                $susBadge = 'secondary';

                                                                if ($estadoSus == 'Activa') {
                                                                    $susBadge = 'success';
                                                                } elseif ($estadoSus == 'Vencida') {
                                                                    $susBadge = 'danger';
                                                                } elseif ($estadoSus == 'Cancelada') {
                                                                    $susBadge = 'warning';
                                                                }
                                                            ?>
                                                            <tr>
                                                                <td><?= esc($sistema['nombre_sistema']) ?></td>
                                                                <td><?= formatear_fecha($sistema['fecha_inscripcion']) ?></td>
                                                                <td><?= formatear_fecha($sistema['fecha_inicio']) ?></td>
                                                                <td><?= formatear_fecha($sistema['fecha_vencimiento']) ?></td>
                                                                <td>
                                                                    <span class="badge bg-<?= $susBadge ?>">
                                                                        <?= esc($estadoSus) ?>
                                                                    </span>
                                                                </td>
                                                                <td><?= formatear_monto($sistema['precio_mensualidad']) ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <p class="mb-0 mt-3" style="color:#cfcfcf;">El profesor no tiene sistemas ni suscripciones registradas.</p>
                                        <?php endif; ?>
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
    const botonesVerMas = document.querySelectorAll('.btnVerMas');

    botonesVerMas.forEach(function(boton) {
        boton.addEventListener('click', function() {
            const idDetalle = this.getAttribute('data-target');
            const filaDetalle = document.getElementById(idDetalle);

            if (filaDetalle.style.display === 'none' || filaDetalle.style.display === '') {
                filaDetalle.style.display = 'table-row';
                this.textContent = '-';
            } else {
                filaDetalle.style.display = 'none';
                this.textContent = '+';
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
