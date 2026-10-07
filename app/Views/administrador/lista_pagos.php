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
            <h1 style="margin:0;">Pagos</h1>

            <button type="button"
                    class="btn btn-success"
                    data-bs-toggle="modal"
                    data-bs-target="#modalRegistrarPago">
                <i class="bi bi-cash-coin"></i> Registrar nuevo pago
            </button>
        </div>

        <!-- FILTROS (tarjeta desplegable) + ORDEN -->
        <style>
            .toolbar-container { display:flex; align-items:center; gap:12px; margin-bottom:20px; flex-wrap:wrap; }

            #buscarPago {
                height:44px; border-radius:17px; font-size:14px; padding:4px 18px;
                width:280px; background:#1f1f1f; border:1px solid #444; color:#eee;
            }
            #buscarPago:focus { box-shadow:none; border-color:#0b8f70; }
            #buscarPago::placeholder { color:#777; }

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

            /* Calendario de 1 fecha o rango: se despliega hacia la DERECHA de la tarjeta */
            .fecha-cal {
                display:none; position:absolute; top:55px; left:calc(100% + 10px);
                width:262px; padding:12px; z-index:1010;
                background:#2a2a2a; border:1px solid #444; border-radius:10px;
                box-shadow:0 6px 24px rgba(0,0,0,0.6);
            }
            .fecha-cal.fecha-cal-izq {
                left:auto; right:calc(100% + 10px);
            }
            .fecha-cal-head {
                display:flex; align-items:center; justify-content:space-between;
                margin-bottom:8px; font-weight:600; font-size:13px; color:#eee;
            }
            .fecha-cal-nav {
                background:transparent; border:none; color:#0b8f70;
                font-size:20px; line-height:1; cursor:pointer; padding:0 8px;
            }
            .fecha-cal-nav:hover { color:#fff; }
            .fecha-cal-grid {
                display:grid; grid-template-columns:repeat(7, 1fr); gap:3px;
            }
            .fecha-cal-dow {
                font-size:11px; color:#9a9a9a; text-align:center; margin-bottom:4px;
            }
            .fecha-cal-dia {
                background:#1f1f1f; border:1px solid transparent; border-radius:6px;
                color:#eee; font-size:12px; padding:6px 0; text-align:center;
                cursor:pointer; transition:background 0.15s;
            }
            .fecha-cal-dia:hover { background:#333; }
            .fecha-cal-dia.vacio { background:transparent; border:none; cursor:default; }
            .fecha-cal-dia.rango { background:#0b8f70; border-color:#0b8f70; color:#fff; }
            .fecha-cal-dia.extremo { background:#097a61; border-color:#0b8f70; color:#fff; font-weight:700; }
            .fecha-cal-acciones {
                display:flex; align-items:center; justify-content:space-between;
                gap:8px; margin-top:10px;
            }
            .fecha-cal-rango-text { font-size:12px; color:#9a9a9a; }
            .fecha-cal-limpiar {
                background:transparent; color:#eee; border:1px solid #555;
                border-radius:14px; font-size:12px; padding:3px 12px; cursor:pointer;
            }
            .fecha-cal-limpiar:hover { background:#333; color:#fff; }

            th { white-space:nowrap; }
        </style>

        <div class="toolbar-container">
            <input type="text" id="buscarPago" class="form-control" placeholder="Buscar">

            <div class="dropdown-wrapper">
                <button type="button" id="btnToggleFilters" class="btn-filter-icon" title="Filtros">
                    <i class="bi bi-sliders"></i>
                </button>

                <div id="filterMenu" class="filter-dropdown-card" onclick="event.stopPropagation()">

                    <div class="menu-section-title">Disciplina</div>
                    <div class="mb-3">
                        <select id="filtroDisciplina" class="form-select">
                            <option value="">Todas las disciplinas</option>
                            <?php foreach($sistemasPagos as $sistema): ?>
                                <option value="<?= esc($sistema) ?>">
                                    <?= esc($sistema) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="menu-section-title">Medio de pago</div>
                    <div class="mb-3">
                        <select id="filtroMedio" class="form-select">
                            <option value="">Todos los medios</option>
                            <?php foreach($mediosPago as $medio): ?>
                                <option value="<?= esc($medio['descripcion']) ?>">
                                    <?= esc($medio['descripcion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="menu-section-title">Cobrado por</div>
                    <div class="mb-3">
                        <select id="filtroCobrado" class="form-select">
                            <option value="">Cualquier persona</option>
                            <?php foreach($cobradoresPagos as $cobrador): ?>
                                <option value="<?= esc($cobrador) ?>">
                                    <?= esc($cobrador) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="menu-section-title">Fecha</div>
                    <div class="mb-3">
                        <input type="text" id="filtroFecha" class="form-control" readonly
                               placeholder="Seleccionar fecha o rango..."
                               style="cursor:pointer;"
                               title="Clic para elegir 1 fecha o un rango">
                    </div>

                    <div id="fechaCalendario" class="fecha-cal">
                            <div class="fecha-cal-head">
                                <button type="button" class="fecha-cal-nav" id="fechaCalAnterior">&lsaquo;</button>
                                <span id="fechaCalTitulo"></span>
                                <button type="button" class="fecha-cal-nav" id="fechaCalSiguiente">&rsaquo;</button>
                            </div>
                            <div class="fecha-cal-grid fecha-cal-dow">
                                <span>L</span><span>M</span><span>M</span><span>J</span><span>V</span><span>S</span><span>D</span>
                            </div>
                            <div class="fecha-cal-grid" id="fechaCalDias"></div>
                            <div class="fecha-cal-acciones">
                                <span id="fechaCalRango" class="fecha-cal-rango-text"></span>
                                <button type="button" class="fecha-cal-limpiar" id="fechaCalLimpiar">Limpiar</button>
                            </div>
                    </div>

                    <div class="menu-section-title">Monto</div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <input type="number" id="filtroMontoMin" class="form-control" placeholder="Desde" min="0" step="0.01">
                        </div>
                        <div class="col-6">
                            <input type="number" id="filtroMontoMax" class="form-control" placeholder="Hasta" min="0" step="0.01">
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-apply w-100" id="btnAplicarFiltros">Aplicar</button>
                        <button type="button" class="btn btn-clear w-100" id="btnLimpiarFiltros">Limpiar</button>
                    </div>
                </div>
            </div>

            <button type="button" id="btnSortAz" class="btn-sort-pill" title="Orden alfabético">
                <i class="bi bi-arrow-down"></i>
                <span id="sortAzText">A - Z</span>
            </button>

            <button type="button" id="btnSortFecha" class="btn-sort-pill" title="Ordenar por fecha de pago">
                <span id="sortFechaIcon" aria-hidden="true" style="display:inline-flex;">
                    <svg width="16" height="14" viewBox="0 0 16 14" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <rect class="linea-fecha" x="1" y="0.5" width="14" height="3" rx="1.5" />
                        <rect class="linea-fecha" x="3.5" y="5.5" width="9" height="3" rx="1.5" />
                        <rect class="linea-fecha" x="5.5" y="10.5" width="5" height="3" rx="1.5" />
                    </svg>
                </span>
                <span id="sortFechaText">Fecha: Más reciente</span>
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
        <div id="scrollTopPagos"
             style="position:sticky; top:0; z-index:20; overflow-x:auto; overflow-y:hidden; height:14px; background:#262626; display:none;">
            <div id="scrollTopInnerPagos" style="height:1px;"></div>
        </div>

        <div class="table-responsive" id="tablaScrollPagos" style="background:#1b1b1b; padding:25px; border-left:5px solid #0b8f70; overflow-x:auto;">
            <table class="table table-dark table-striped table-hover align-middle" id="tablaPagos">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>DNI</th>
                        <th>Sistema</th>
                        <th>Monto</th>
                        <th>Medio de pago</th>
                        <th>Cobrado por</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if(!empty($pagos)): ?>
                        <?php foreach($pagos as $pago): ?>
                            <tr data-sistema="<?= esc($pago['nombre_sistema']) ?>"
                                data-medio="<?= esc($pago['medio_pago']) ?>"
                                data-cobrado="<?= esc($pago['cobrado_por']) ?>"
                                data-fecha="<?= strtotime((string)$pago['fecha_pago']) ?>"
                                data-fdia="<?= date('Y-m-d', strtotime((string)$pago['fecha_pago'])) ?>"
                                data-monto="<?= (float)$pago['monto'] ?>">
                                <td><?= formatear_fecha($pago['fecha_pago']) ?></td>
                                <td><?= esc($pago['nombre']) ?> <?= esc($pago['apellido']) ?></td>
                                <td><?= esc($pago['dni']) ?></td>
                                <td><?= esc($pago['nombre_sistema']) ?></td>
                                <td><?= formatear_monto($pago['monto']) ?></td>
                                <td><?= esc($pago['medio_pago']) ?></td>
                                <td><?= esc($pago['cobrado_por']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr id="filtroVacioPagos" class="text-center" <?= !empty($pagos) ? 'style="display:none;"' : '' ?>>
                        <td colspan="7">No hay pagos registrados.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <style>
        .dropdown-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1050;
            background: #262626;
            border: 1px solid #0b8f70;
            border-radius: 6px;
            max-height: 200px;
            overflow-y: auto;
            display: none;
        }

        .dropdown-list .item {
            padding: 8px 12px;
            cursor: pointer;
            color: #fff;
        }

        .dropdown-list .item:hover,
        .dropdown-list .item.active {
            background: #0b8f70;
            color: #fff;
        }
    </style>

    <!-- MODAL REGISTRAR PAGO -->
    <div class="modal fade" id="modalRegistrarPago" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="background:#1b1b1b; color:white; border:1px solid #0b8f70;">
                <div class="modal-header" style="border-bottom:1px solid #333;">
                    <h5 class="modal-title" style="color:#0b8f70;">Registrar nuevo pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1);"></button>
                </div>

                <form action="<?= base_url('registrar_pago') ?>" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cliente *</label>
                                <div class="position-relative" id="pagoClienteWrapper">
                                    <input type="text" id="pagoClienteInput" class="form-control"
                                           placeholder="Escribí el nombre del cliente..."
                                           autocomplete="off" required>
                                    <input type="hidden" name="id_persona" id="pagoClienteId">
                                    <div id="pagoClienteList" class="dropdown-list"></div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sistema *</label>
                                <select name="id_sistema" id="pagoSistema" class="form-control" required>
                                    <option value="">Seleccionar cliente primero...</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Monto *</label>
                                <input type="number" step="0.01" min="0" name="monto"
                                       id="pagoMonto" class="form-control" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Medio de pago *</label>
                                <select name="id_medio_pago" class="form-control" required>
                                    <option value="">Seleccionar medio...</option>
                                    <?php foreach($mediosPago as $medio): ?>
                                        <option value="<?= $medio['id_medio_pago'] ?>">
                                            <?= esc($medio['descripcion']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fecha</label>
                                <input type="date" name="fecha_pago" class="form-control"
                                       value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer" style="border-top:1px solid #333;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Registrar pago</button>
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
    const buscarPago = document.getElementById('buscarPago');
    const btnSortAz = document.getElementById('btnSortAz');
    const btnSortFecha = document.getElementById('btnSortFecha');

    const filtroDisciplina = document.getElementById('filtroDisciplina');
    const filtroMedio = document.getElementById('filtroMedio');
    const filtroCobrado = document.getElementById('filtroCobrado');
    const filtroMontoMin = document.getElementById('filtroMontoMin');
    const filtroMontoMax = document.getElementById('filtroMontoMax');

    const filtroFecha = document.getElementById('filtroFecha');
    const fechaCalendario = document.getElementById('fechaCalendario');
    const fechaCalTitulo = document.getElementById('fechaCalTitulo');
    const fechaCalDias = document.getElementById('fechaCalDias');
    const fechaCalRango = document.getElementById('fechaCalRango');

    // Selección del calendario: null o timestamps (segundos).
    // Solo desde = 1 fecha exacta; desde + hasta = rango.
    let calDesde = null;
    let calHasta = null;
    let calAnio = null;
    let calMes = null;

    const tablaPagos = document.getElementById('tablaPagos');

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

    function aplicarFiltros() {
        if (!tablaPagos) return;

        const texto = (buscarPago?.value || '').toLowerCase().trim();
        const disciplina = filtroDisciplina?.value || '';
        const medio = filtroMedio?.value || '';
        const cobrado = filtroCobrado?.value || '';
        const montoMin = filtroMontoMin?.value !== '' && filtroMontoMin?.value !== undefined ? parseFloat(filtroMontoMin.value) : null;
        const montoMax = filtroMontoMax?.value !== '' && filtroMontoMax?.value !== undefined ? parseFloat(filtroMontoMax.value) : null;

        let visibles = 0;

        Array.from(tablaPagos.tBodies[0].children).forEach(function (fila) {
            if (fila.tagName !== 'TR' || fila.id === 'filtroVacioPagos') return;

            const celdas = fila.children;
            // Busca solo en cliente (nombre y apellido) o DNI
            const busqueda = (celdas[1]?.textContent || '') + ' ' + (celdas[2]?.textContent || '');
            const coincideTexto = !texto || busqueda.toLowerCase().includes(texto);

            const coincideDisciplina = !disciplina || (fila.dataset.sistema || '') === disciplina;
            const coincideMedio = !medio || (fila.dataset.medio || '') === medio;
            const coincideCobrado = !cobrado || (fila.dataset.cobrado || '') === cobrado;

            // Comparación por fecha 'Y-m-d' (evita el desfase de zona horaria servidor/navegador)
            const fdia = fila.dataset.fdia || '';
            const desdeDia = calDesde !== null ? isoDia(calDesde) : null;
            const hastaDia = calDesde !== null ? isoDia(calHasta !== null ? calHasta : calDesde) : null;
            const coincideFecha = (desdeDia === null || fdia >= desdeDia)
                && (hastaDia === null || fdia <= hastaDia);

            const monto = fila.dataset.monto !== '' && fila.dataset.monto !== undefined
                ? parseFloat(fila.dataset.monto) : null;
            const coincideMonto = (montoMin === null || (monto !== null && monto >= montoMin))
                && (montoMax === null || (monto !== null && monto <= montoMax));

            const visible = coincideTexto && coincideDisciplina
                && coincideMedio && coincideCobrado
                && coincideFecha && coincideMonto;

            fila.style.display = visible ? '' : 'none';
            if (visible) visibles++;
        });

        const vacio = document.getElementById('filtroVacioPagos');
        if (vacio) {
            vacio.style.display = visibles === 0 ? 'table-row' : 'none';
            vacio.children[0].textContent = tablaPagos.querySelectorAll('tbody tr').length <= 1
                ? 'No hay pagos registrados.' : 'No se encontraron pagos.';
        }
    }

    // Búsqueda general (cualquier dato de la fila); filtra al instante
    buscarPago?.addEventListener('input', aplicarFiltros);

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

    // ===== CALENDARIO: 1 fecha o rango =====
    const MESES = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    function formatearDiaFiltro(ts) {
        const d = new Date(ts * 1000);
        return String(d.getDate()).padStart(2, '0') + '/' + String(d.getMonth() + 1).padStart(2, '0') + '/' + d.getFullYear();
    }

    function isoDia(ts) {
        const d = new Date(ts * 1000);
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    function textoRangoFiltro() {
        if (calDesde === null) return '';
        if (calHasta === null || calDesde === calHasta) return formatearDiaFiltro(calDesde);
        return formatearDiaFiltro(calDesde) + ' – ' + formatearDiaFiltro(calHasta);
    }

    function actualizarCampoFecha() {
        if (filtroFecha) filtroFecha.value = textoRangoFiltro();
        if (fechaCalRango) fechaCalRango.textContent = textoRangoFiltro();
    }

    function pintarCalendario() {
        if (!fechaCalDias || calAnio === null) return;

        if (fechaCalTitulo) fechaCalTitulo.textContent = MESES[calMes] + ' ' + calAnio;

        const primerDia = new Date(calAnio, calMes, 1);
        const diasEnMes = new Date(calAnio, calMes + 1, 0).getDate();
        // getDay(): 0=domingo...6=sábado  ->  empezamos en lunes
        const huecos = (primerDia.getDay() + 6) % 7;

        fechaCalDias.innerHTML = '';

        for (let i = 0; i < huecos; i++) {
            const vacio = document.createElement('span');
            vacio.className = 'fecha-cal-dia vacio';
            fechaCalDias.appendChild(vacio);
        }

        const hoy = new Date();
        const hoyTs = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate()).getTime() / 1000;

        for (let dia = 1; dia <= diasEnMes; dia++) {
            const ts = new Date(calAnio, calMes, dia).getTime() / 1000;
            const celda = document.createElement('span');
            celda.className = 'fecha-cal-dia';
            celda.textContent = dia;
            if (ts === hoyTs) celda.style.borderColor = '#0b8f70';

            const esDesde = calDesde !== null && ts === calDesde;
            const esHasta = calHasta !== null && ts === calHasta;
            const enRango = calDesde !== null && calHasta !== null && ts > calDesde && ts < calHasta;

            if (esDesde || esHasta) celda.classList.add('extremo');
            else if (enRango) celda.classList.add('rango');

            celda.addEventListener('click', function () {
                if (calDesde === null || (calDesde !== null && calHasta !== null)) {
                    // Primer clic (o reinicio tras rango completo): 1 fecha
                    calDesde = ts;
                    calHasta = null;
                } else if (ts < calDesde) {
                    // Segundo clic anterior al inicio: invierte el rango
                    calHasta = calDesde;
                    calDesde = ts;
                } else {
                    // Segundo clic: completa el rango
                    calHasta = ts;
                }
                actualizarCampoFecha();
                pintarCalendario();
                aplicarFiltros();
            });

            fechaCalDias.appendChild(celda);
        }
    }

    function navegarCalendario(paso) {
        if (calAnio === null) return;
        calMes += paso;
        if (calMes > 11) { calMes = 0; calAnio++; }
        if (calMes < 0) { calMes = 11; calAnio--; }
        pintarCalendario();
    }

    if (filtroFecha) {
        filtroFecha.addEventListener('click', function (e) {
            e.stopPropagation();
            if (!fechaCalendario) return;
            const abrir = fechaCalendario.style.display !== 'block';
            fechaCalendario.style.display = abrir ? 'block' : 'none';
            if (abrir) {
                const base = calDesde !== null ? new Date(calDesde * 1000) : new Date();
                calAnio = base.getFullYear();
                calMes = base.getMonth();
                pintarCalendario();

                // Se abre hacia la DERECHA de la tarjeta; si no hay lugar, hacia la izquierda
                fechaCalendario.classList.remove('fecha-cal-izq');
                fechaCalendario.style.top = '';
                fechaCalendario.style.bottom = '';

                const card = document.getElementById('filterMenu');
                const rCard = card ? card.getBoundingClientRect()
                                   : fechaCalendario.parentElement.getBoundingClientRect();
                const rCampo = filtroFecha.getBoundingClientRect();
                // Alinea el calendario con el campo de fecha dentro de la tarjeta
                fechaCalendario.style.top = (rCampo.top - rCard.top) + 'px';

                const r = fechaCalendario.getBoundingClientRect();
                if (r.right > window.innerWidth - 10) {
                    fechaCalendario.classList.add('fecha-cal-izq');
                }
                // Si se pasa del piso de la ventana, lo sube
                const r2 = fechaCalendario.getBoundingClientRect();
                if (r2.bottom > window.innerHeight - 10) {
                    const exceso = r2.bottom - (window.innerHeight - 10);
                    const topActual = parseFloat(fechaCalendario.style.top) || 0;
                    fechaCalendario.style.top = Math.max(0, topActual - exceso) + 'px';
                }
            }
        });
    }

    document.getElementById('fechaCalAnterior')?.addEventListener('click', function (e) {
        e.stopPropagation();
        navegarCalendario(-1);
    });

    document.getElementById('fechaCalSiguiente')?.addEventListener('click', function (e) {
        e.stopPropagation();
        navegarCalendario(1);
    });

    document.getElementById('fechaCalLimpiar')?.addEventListener('click', function (e) {
        e.stopPropagation();
        calDesde = null;
        calHasta = null;
        actualizarCampoFecha();
        pintarCalendario();
        aplicarFiltros();
    });

    // Cerrar el calendario al clicar fuera de él (pero dentro de la tarjeta)
    document.addEventListener('click', function (e) {
        if (fechaCalendario && fechaCalendario.style.display === 'block'
            && !fechaCalendario.contains(e.target)
            && e.target !== filtroFecha) {
            fechaCalendario.style.display = 'none';
        }
    });

    // Limpiar
    if (btnLimpiarFiltros) {
        btnLimpiarFiltros.addEventListener('click', function () {
            [filtroDisciplina, filtroMedio, filtroCobrado, filtroMontoMin, filtroMontoMax]
                .forEach(el => { if (el) el.value = ''; });
            calDesde = null;
            calHasta = null;
            actualizarCampoFecha();
            if (fechaCalendario) fechaCalendario.style.display = 'none';
            aplicarFiltros();
        });
    }

    // ===== BOTONES DE ORDEN (siempre activos) =====
    // 1) A-Z / Z-A  -> orden alfabético por cliente
    // 2) Fecha       -> Más reciente / Menos reciente
    let ordenAzAplicado = false;
    let ordenFechaAplicado = false;

    btnSortAz?.addEventListener('click', function () {
        ordenAzAplicado = !ordenAzAplicado;

        const text = document.getElementById('sortAzText');
        const icon = this.querySelector('i');
        if (text) text.textContent = ordenAzAplicado ? 'A - Z' : 'Z - A';
        if (icon) icon.className = ordenAzAplicado ? 'bi bi-arrow-down' : 'bi bi-arrow-up';

        ordenarPagos(1, 'texto', ordenAzAplicado);
        aplicarFiltros();
    });

    // Icono de 3 líneas apiladas (embudo): más reciente = punta abajo
    function pintarIconoFecha(reciente) {
        const icon = document.getElementById('sortFechaIcon');
        if (!icon) return;
        const lineas = icon.querySelectorAll('rect');
        const anchos = reciente ? [14, 9, 5] : [5, 9, 14];
        lineas.forEach(function (linea, i) {
            const w = anchos[i];
            linea.setAttribute('width', w);
            linea.setAttribute('x', (16 - w) / 2);
        });
    }

    btnSortFecha?.addEventListener('click', function () {
        ordenFechaAplicado = !ordenFechaAplicado;

        const text = document.getElementById('sortFechaText');
        if (text) text.textContent = ordenFechaAplicado ? 'Fecha: Más reciente' : 'Fecha: Menos reciente';

        pintarIconoFecha(ordenFechaAplicado);
        ordenarPagos(0, 'fecha', !ordenFechaAplicado);
        aplicarFiltros();
    });

    function ordenarPagos(indice, tipo, asc) {
        const tbody = tablaPagos.tBodies[0];
        const filas = Array.from(tbody.children)
            .filter(f => f.tagName === 'TR' && f.id !== 'filtroVacioPagos');

        filas.sort((a, b) => {
            const celdaA = a.children[indice];
            const celdaB = b.children[indice];

            if (tipo === 'numero' || tipo === 'fecha') {
                const valA = tipo === 'fecha'
                    ? (parseFloat(a.dataset.fecha || '0') || 0)
                    : (parseFloat(celdaA.dataset.value || '0') || 0);
                const valB = tipo === 'fecha'
                    ? (parseFloat(b.dataset.fecha || '0') || 0)
                    : (parseFloat(celdaB.dataset.value || '0') || 0);
                return asc ? valA - valB : valB - valA;
            }

            const valA = (celdaA.textContent || '').trim().toLowerCase();
            const valB = (celdaB.textContent || '').trim().toLowerCase();
            return asc ? valA.localeCompare(valB, 'es', { numeric: true }) : valB.localeCompare(valA, 'es', { numeric: true });
        });

        filas.forEach(f => tbody.appendChild(f));

        const vacio = document.getElementById('filtroVacioPagos');
        if (vacio) tbody.appendChild(vacio);
    }

    aplicarFiltros();

    // SCROLL HORIZONTAL SUPERIOR
    const tablaScroll = document.getElementById('tablaScrollPagos');
    const scrollTop = document.getElementById('scrollTopPagos');
    const scrollTopInner = document.getElementById('scrollTopInnerPagos');

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

    // MODAL REGISTRAR PAGO: búsqueda de cliente + sistemas según cliente + monto automático
    const clientesData = <?= json_encode(array_map(function ($c) {
        return [
            'id'       => $c['id_persona'],
            'nombre'   => $c['nombre'].' '.$c['apellido'],
            'sistemas' => $c['sistemas']
        ];
    }, $clientes), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    const pagoClienteInput = document.getElementById('pagoClienteInput');
    const pagoClienteId = document.getElementById('pagoClienteId');
    const pagoClienteList = document.getElementById('pagoClienteList');
    const pagoSistema = document.getElementById('pagoSistema');
    const pagoMonto = document.getElementById('pagoMonto');

    function poblarSistemasCliente(cliente) {
        pagoSistema.innerHTML = '<option value="">Seleccionar sistema...</option>';
        pagoMonto.value = '';

        if (!cliente || !Array.isArray(cliente.sistemas)) return;

        cliente.sistemas.forEach(function (s) {
            const opt = document.createElement('option');
            opt.value = s.id_sistema;
            opt.dataset.precio = s.precio_mensualidad || '';
            opt.textContent = s.nombre_sistema;
            pagoSistema.appendChild(opt);
        });
    }

    function mostrarClientes() {
        const texto = pagoClienteInput.value.trim().toLowerCase();

        if (texto === '') {
            pagoClienteList.style.display = 'none';
            return;
        }

        const filtrados = clientesData.filter(function (c) {
            return c.nombre.toLowerCase().includes(texto);
        }).slice(0, 10);

        pagoClienteList.innerHTML = '';

        if (filtrados.length === 0) {
            pagoClienteList.style.display = 'none';
            return;
        }

        filtrados.forEach(function (c) {
            const div = document.createElement('div');
            div.className = 'item';
            div.textContent = c.nombre;

            div.addEventListener('mousedown', function (e) {
                e.preventDefault();
                pagoClienteInput.value = c.nombre;
                pagoClienteId.value = c.id;
                poblarSistemasCliente(c);
                pagoClienteList.style.display = 'none';
            });

            pagoClienteList.appendChild(div);
        });

        pagoClienteList.style.display = 'block';
    }

    if (pagoClienteInput && pagoClienteId && pagoClienteList && pagoSistema && pagoMonto) {
        pagoClienteInput.addEventListener('input', function () {
            pagoClienteId.value = '';
            mostrarClientes();
        });

        pagoClienteInput.addEventListener('focus', mostrarClientes);

        pagoClienteInput.addEventListener('blur', function () {
            setTimeout(function () {
                pagoClienteList.style.display = 'none';
            }, 150);
        });

        pagoSistema.addEventListener('change', function () {
            const opcion = this.options[this.selectedIndex];
            pagoMonto.value = opcion && opcion.dataset.precio ? opcion.dataset.precio : '';
        });
    }
</script>
