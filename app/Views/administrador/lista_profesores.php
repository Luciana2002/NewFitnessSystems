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

        <!-- FILTROS (tarjeta desplegable) + ORDEN -->
        <style>
            .toolbar-container { display:flex; align-items:center; gap:12px; margin-bottom:20px; flex-wrap:wrap; }

            #buscarProfesor {
                height:44px; border-radius:17px; font-size:14px; padding:4px 18px;
                width:280px; background:#1f1f1f; border:1px solid #444; color:#eee;
            }
            #buscarProfesor:focus { box-shadow:none; border-color:#0b8f70; }
            #buscarProfesor::placeholder { color:#777; }

            .btn-filter-icon {
                background-color:#0b8f70; color:#ffffff; border:none; border-radius:14px;
                width:44px; height:44px; display:flex; align-items:center; justify-content:center;
                font-size:20px; cursor:pointer; transition:background-color 0.2s;
            }
            .btn-filter-icon:hover { background-color:#097a61; }

            .btn-sort-pill {
                background-color:#e9e8e2; color:#111; border:none; border-radius:14px;
                padding:0 18px; height:44px; font-weight:600; font-size:14px;
                display:flex; align-items:center; gap:8px; cursor:pointer; transition:background-color 0.2s;
            }
            .btn-sort-pill:hover { background-color:#dcdbd5; }
            .btn-sort-pill:disabled {
                background-color:#3a3a3a; color:#7a7a7a;
                cursor:not-allowed; opacity:1;
            }
            .btn-sort-pill:disabled:hover { background-color:#3a3a3a; }

            .dropdown-wrapper { position:relative; }

            .filter-dropdown-card {
                position:absolute; top:55px; left:0;
                background-color:#1f1f1f; border-radius:16px;
                box-shadow:0 4px 20px rgba(0,0,0,0.5);
                padding:20px; width:300px; z-index:1000;
                border:1px solid #333;
                display:none;
                animation:fadeInFiltros 0.2s ease-out;
            }
            @keyframes fadeInFiltros {
                from { opacity:0; transform:translateY(-10px); }
                to { opacity:1; transform:translateY(0); }
            }

            .menu-section-title { font-size:12px; color:#9a9a9a; margin-bottom:8px; font-weight:500; }

            .filter-dropdown-card .form-control,
            .filter-dropdown-card .form-select {
                background:#2a2a2a; border:1px solid #444; color:#eee;
                font-size:13px; border-radius:8px; padding:8px 12px;
            }
            .filter-dropdown-card .form-control:focus,
            .filter-dropdown-card .form-select:focus {
                box-shadow:none; border-color:#0b8f70;
            }
            .filter-dropdown-card .form-control::placeholder { color:#777; }

            .btn-apply { background-color:#0b8f70; color:white; border-radius:20px; font-weight:bold; }
            .btn-apply:hover { background-color:#097a61; color:white; }
            .btn-clear {
                background:transparent; color:#eee; border:1px solid #555;
                border-radius:20px; font-weight:bold;
            }
            .btn-clear:hover { background:#333; color:#fff; }

            th { white-space:nowrap; }
        </style>

        <div class="toolbar-container">
            <input type="text" id="buscarProfesor" class="form-control" placeholder="Buscar">

            <div class="dropdown-wrapper">
                <button type="button" id="btnToggleFilters" class="btn-filter-icon" title="Filtros">
                    <i class="bi bi-sliders"></i>
                </button>

                <div id="filterMenu" class="filter-dropdown-card" onclick="event.stopPropagation()">

                    <div class="menu-section-title">Estado</div>
                    <div class="mb-3">
                        <select id="filtroEstado" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="Activo">Activo</option>
                            <option value="Dado de baja">Dado de baja</option>
                        </select>
                    </div>

                    <div class="menu-section-title">Cuenta de usuario</div>
                    <div class="mb-3">
                        <select id="filtroUsuario" class="form-select">
                            <option value="">Cualquier cuenta</option>
                            <option value="con">Con usuario</option>
                            <option value="sin">Sin usuario</option>
                        </select>
                    </div>

                    <div class="menu-section-title">Email</div>
                    <div class="mb-3">
                        <select id="filtroEmail" class="form-select">
                            <option value="">Cualquier email</option>
                            <option value="con">Con email</option>
                            <option value="sin">Sin email</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-apply w-100" id="btnAplicarFiltros">Aplicar</button>
                        <button type="button" class="btn btn-clear w-100" id="btnLimpiarFiltros">Limpiar</button>
                    </div>
                </div>
            </div>

            <button type="button" id="btnSortProfesores" class="btn-sort-pill" title="Orden alfabético">
                <i id="sortIconProfesores" class="bi bi-arrow-down"></i>
                <span id="sortTextProfesores">A - Z</span>
            </button>
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
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th style="text-align:center;"></th>
                    </tr>
                </thead>

                <tbody>
                    <?php if(!empty($profesores)): ?>
                        <?php foreach($profesores as $profesor): ?>
                            <tr data-estado="<?= $profesor['baja'] == 'S' ? 'Dado de baja' : 'Activo' ?>"
                                data-usuario="<?= !empty($profesor['nombre_usuario']) ? 'con' : 'sin' ?>"
                                data-email="<?= !empty($profesor['email']) ? 'con' : 'sin' ?>">
                                <td data-value="<?= esc($profesor['dni']) ?>"><?= esc($profesor['dni']) ?></td>
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
                        <tr id="filtroVacioProfesores" class="text-center" style="display:none;">
                            <td colspan="6">No hay profesores que coincidan con los filtros.</td>
                        </tr>
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

    // ===== FILTROS (tarjeta desplegable) + BÚSQUEDA + ORDEN =====
    const filtroMenu = document.getElementById('filterMenu');
    const btnToggleFilters = document.getElementById('btnToggleFilters');
    const btnAplicarFiltros = document.getElementById('btnAplicarFiltros');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');
    const btnSortProfesores = document.getElementById('btnSortProfesores');
    const buscarProfesor = document.getElementById('buscarProfesor');

    const filtroEstado = document.getElementById('filtroEstado');
    const filtroUsuario = document.getElementById('filtroUsuario');
    const filtroEmail = document.getElementById('filtroEmail');
    const tablaProfesores = document.getElementById('tablaProfesores');

    // Abrir / cerrar tarjeta
    function toggleDropdown(e) {
        e.stopPropagation();
        if (!filtroMenu) return;
        filtroMenu.style.display = filtroMenu.style.display === 'block' ? 'none' : 'block';
    }

    if (btnToggleFilters) btnToggleFilters.addEventListener('click', toggleDropdown);

    document.addEventListener('click', function (e) {
        if (filtroMenu && filtroMenu.style.display === 'block'
            && !filtroMenu.contains(e.target)
            && e.target !== btnToggleFilters && !btnToggleFilters.contains(e.target)) {
            filtroMenu.style.display = 'none';
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && filtroMenu) filtroMenu.style.display = 'none';
    });

    function colapsarDetalle(fila) {
        const detalle = fila.nextElementSibling;
        if (detalle && detalle.classList.contains('fila-detalle')) {
            detalle.style.display = 'none';
            const boton = fila.querySelector('.btnVerMas');
            const icono = boton && boton.querySelector('i');
            if (boton) boton.setAttribute('aria-expanded', 'false');
            if (icono) icono.className = 'bi bi-chevron-down';
        }
    }

    function aplicarFiltros() {
        if (!tablaProfesores) return;

        const texto = (buscarProfesor?.value || '').toLowerCase().trim();
        const estado = filtroEstado?.value || '';
        const usuario = filtroUsuario?.value || '';
        const email = filtroEmail?.value || '';
        let visibles = 0;

        Array.from(tablaProfesores.tBodies[0].children).forEach(function (fila) {
            if (fila.tagName !== 'TR' || fila.classList.contains('fila-detalle')) return;

            const celdas = fila.children;
            // Busca solo en nombre, apellido o DNI (los demás campos tienen filtro propio)
            const busqueda = (celdas[1]?.textContent || '') + ' '
                + (celdas[2]?.textContent || '') + ' '
                + (celdas[0]?.textContent || '');
            const coincideTexto = !texto || busqueda.toLowerCase().includes(texto);
            const coincideEstado = !estado || (fila.dataset.estado || '') === estado;
            const coincideUsuario = !usuario || (fila.dataset.usuario || 'sin') === usuario;
            const coincideEmail = !email || (fila.dataset.email || 'sin') === email;
            const visible = coincideTexto && coincideEstado
                && coincideUsuario && coincideEmail;

            fila.style.display = visible ? '' : 'none';
            if (visible) visibles++;

            colapsarDetalle(fila);
        });

        const vacio = document.getElementById('filtroVacioProfesores');
        if (vacio) vacio.style.display = visibles === 0 ? 'table-row' : 'none';
    }

    // Búsqueda por nombre, apellido o DNI; filtra al instante
    buscarProfesor?.addEventListener('input', aplicarFiltros);

    // Aplicar (cierra la tarjeta como en el ejemplo)
    if (btnAplicarFiltros) {
        btnAplicarFiltros.addEventListener('click', function () {
            aplicarFiltros();
            if (filtroMenu) filtroMenu.style.display = 'none';
        });
    }

    // Enter dentro de la tarjeta también aplica
    filtroMenu?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            aplicarFiltros();
            filtroMenu.style.display = 'none';
        }
    });

    // Limpiar
    if (btnLimpiarFiltros) {
        btnLimpiarFiltros.addEventListener('click', function () {
            [filtroEstado, filtroUsuario, filtroEmail]
                .forEach(el => { if (el) el.value = ''; });
            aplicarFiltros();
        });
    }

    // Botón A-Z (siempre activo): orden alfabético por nombre
    // Primer clic aplica A-Z (el rótulo visible); después alterna.
    let ordenAscProf = false;

    btnSortProfesores?.addEventListener('click', function () {
        ordenAscProf = !ordenAscProf;

        const text = document.getElementById('sortTextProfesores');
        const icon = document.getElementById('sortIconProfesores');
        if (text) text.textContent = ordenAscProf ? 'A - Z' : 'Z - A';
        if (icon) icon.className = ordenAscProf ? 'bi bi-arrow-down' : 'bi bi-arrow-up';

        ordenarProfesores(1, 'texto', ordenAscProf);
        aplicarFiltros();
    });

    function ordenarProfesores(indice, tipo, asc) {
        const tbody = tablaProfesores.tBodies[0];
        const filas = Array.from(tbody.children)
            .filter(f => f.tagName === 'TR' && !f.classList.contains('fila-detalle') && f.id !== 'filtroVacioProfesores');

        const parejas = filas.map(f => {
            const detalle = f.nextElementSibling && f.nextElementSibling.classList.contains('fila-detalle')
                ? f.nextElementSibling : null;
            return { fila: f, detalle: detalle };
        });

        parejas.sort((a, b) => {
            const celdaA = a.fila.children[indice];
            const celdaB = b.fila.children[indice];

            if (tipo === 'numero' || tipo === 'fecha') {
                const valA = parseFloat(celdaA.dataset.value || '0') || 0;
                const valB = parseFloat(celdaB.dataset.value || '0') || 0;
                return asc ? valA - valB : valB - valA;
            }

            const valA = (celdaA.textContent || '').trim().toLowerCase();
            const valB = (celdaB.textContent || '').trim().toLowerCase();
            return asc ? valA.localeCompare(valB, 'es', { numeric: true }) : valB.localeCompare(valA, 'es', { numeric: true });
        });

        parejas.forEach(p => {
            tbody.appendChild(p.fila);
            if (p.detalle) tbody.appendChild(p.detalle);
        });

        const vacio = document.getElementById('filtroVacioProfesores');
        if (vacio) tbody.appendChild(vacio);
    }

    aplicarFiltros();

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
