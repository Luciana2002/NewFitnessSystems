<main class="dashboard-page">

    <!-- BOTÓN LATERAL -->
    <button type="button" id="sidebarOpen" class="sidebar-handle">
        <span>›</span>
    </button>

    <button type="button" id="sidebarClose" class="sidebar-close">
        ✕
    </button>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <?= view('layout/sidebar') ?>

    <section class="dashboard-content" style="padding:50px 70px;">

        <!-- TÍTULO + BOTÓN REGISTRAR -->
        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:15px;">
            <h1 style="margin:0;">Lista de Clientes</h1>

            <button type="button"
                    class="nfs-btn nfs-btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#modalRegistrarCliente">
                <i class="bi bi-person-plus"></i> Registrar cliente
            </button>
        </div>

        <!-- FILTROS (tarjeta desplegable) + ORDEN -->
        <style>
            .toolbar-container { display:flex; align-items:center; gap:12px; margin-bottom:20px; flex-wrap:wrap; }

            #buscarCliente {
                height:44px; border-radius:17px; font-size:14px; padding:4px 18px;
                width:280px; background:#1f1f1f; border:1px solid #444; color:#eee;
            }
            #buscarCliente:focus { box-shadow:none; border-color:#0b8f70; }
            #buscarCliente::placeholder { color:#777; }

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
        </style>

        <div class="toolbar-container">
            <input type="text" id="buscarCliente" class="form-control" placeholder="Buscar">

            <div class="dropdown-wrapper">
                <button type="button" id="btnToggleFilters" class="btn-filter-icon" title="Filtros">
                    <i class="bi bi-sliders"></i>
                </button>

                <div id="filterMenu" class="filter-dropdown-card" onclick="event.stopPropagation()">

                    <div class="menu-section-title">Disciplina</div>
                    <div class="mb-3">
                        <select id="filtroDisciplina" class="form-select">
                            <option value="">Todas las disciplinas</option>
                            <?php foreach($sistemasRegistro as $sistema): ?>
                                <option value="<?= esc($sistema['nombre_sistema']) ?>">
                                    <?= esc($sistema['nombre_sistema']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="menu-section-title">Estado</div>
                    <div class="mb-3">
                        <select id="filtroEstado" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="Al día">Al día</option>
                            <option value="Vencido">Vencido</option>
                        </select>
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

            <button type="button" id="btnSortPago" class="btn-sort-pill" title="Ordenar por último pago">
                <span id="sortPagoIcon" aria-hidden="true" style="display:inline-flex;">
                    <svg width="16" height="14" viewBox="0 0 16 14" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <rect class="linea-pago" x="1" y="0.5" width="14" height="3" rx="1.5" />
                        <rect class="linea-pago" x="3.5" y="5.5" width="9" height="3" rx="1.5" />
                        <rect class="linea-pago" x="5.5" y="10.5" width="5" height="3" rx="1.5" />
                    </svg>
                </span>
                <span id="sortPagoText">Último pago: Más reciente</span>
            </button>
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

        <!-- SCROLL HORIZONTAL SUPERIOR (accesible desde cualquier lugar de la lista) -->
        <div id="scrollTopClientes"
             style="position:sticky; top:0; z-index:20; overflow-x:auto; overflow-y:hidden; height:14px; background:#262626; display:none;">
            <div id="scrollTopInnerClientes" style="height:1px;"></div>
        </div>

        <!-- TABLA -->
        <div class="table-responsive" id="tablaScrollClientes" style="background:#1b1b1b; padding:25px; border-left:5px solid #0b8f70; overflow-x:auto;">
            <table class="table table-dark table-striped table-hover align-middle" id="tablaClientes">
                <thead>
                     <tr>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Sistema</th>
                        <th>Monto</th>
                        <th>Último pago</th>
                        <th>Estado</th>
                        <th style="text-align:center;"></th>
                    </tr>
                </thead>

                <tbody>
                    <?php if(!empty($clientes)): ?>
                        <?php foreach($clientes as $cliente): ?>

                            <?php
                                $sistemas = $cliente['sistemas'] ?? [];
                                $sistemas = array_values(array_filter($sistemas, function ($s) {
                                    return ($s['estado_suscripcion'] ?? '') !== 'Cancelada';
                                }));
                                $ultimoPago = $cliente['ultimo_pago'] ?? null;

                                $nombreSistema = $sistemas
                                    ? implode(', ', array_column($sistemas, 'nombre_sistema'))
                                    : '—';

                                $montoCuota = $sistemas
                                    ? array_sum(array_map(function ($s) {
                                        return (float) ($s['precio_mensualidad'] ?? 0);
                                    }, $sistemas))
                                    : null;

                                $fechasPago = array_values(array_filter(array_column($sistemas, 'ultimo_pago_sistema')));
                                $ultimoPagoSist = $fechasPago ? max($fechasPago) : $ultimoPago;

                                if (empty($sistemas)) {
                                    $estadoCuota = 'Sin suscripción';
                                    $badgeClass  = 'secondary';
                                } elseif (empty($ultimoPago)) {
                                    $estadoCuota = 'Sin pagos';
                                    $badgeClass  = 'warning';
                                } else {
                                    $hoyTs = strtotime(date('Y-m-d'));
                                    $vencidos = 0;

                                    foreach ($sistemas as $s) {
                                        $tsVenc = !empty($s['fecha_vencimiento'])
                                            ? strtotime((string) $s['fecha_vencimiento'])
                                            : null;

                                        if ($tsVenc && $tsVenc < $hoyTs) {
                                            $vencidos++;
                                        }
                                    }

                                    if ($vencidos > 0) {
                                        $estadoCuota = 'Vencido';
                                        $badgeClass  = 'danger';
                                    } else {
                                        $estadoCuota = 'Al día';
                                        $badgeClass  = 'success';
                                    }
                                }
                            ?>

                            <tr data-sistema="<?= esc(implode('|', array_column($sistemas, 'nombre_sistema'))) ?>"
                                data-estado="<?= esc($estadoCuota) ?>"
                                data-baja="<?= esc($cliente['baja'] ?? 'N') ?>"
                                data-ultimopago="<?= !empty($ultimoPagoSist) ? strtotime((string) $ultimoPagoSist) : 0 ?>"
                                data-monto="<?= $montoCuota !== null ? (float) $montoCuota : '' ?>">
                                <td><?= esc($cliente['dni']) ?></td>
                                <td><?= esc($cliente['nombre']) ?></td>
                                <td><?= esc($cliente['apellido']) ?></td>
                                <td><?= esc($nombreSistema) ?></td>
                                <td data-value="<?= $montoCuota !== null ? (float) $montoCuota : -1 ?>"><?= $montoCuota !== null ? '$ ' . number_format((float) $montoCuota, 2) : '—' ?></td>
                                <td data-value="<?= !empty($ultimoPagoSist) ? strtotime((string) $ultimoPagoSist) : 0 ?>"><?= !empty($ultimoPagoSist) ? date('d/m/Y', strtotime((string) $ultimoPagoSist)) : '—' ?></td>
                                <td>
                                    <span class="badge bg-<?= $badgeClass ?>">
                                        <?= esc($estadoCuota) ?>
                                    </span>
                                </td>
                                 <td style="white-space: nowrap; text-align:center;">
                                        <button type="button"
                                                class="btnVerMas"
                                                data-target="detalle-<?= $cliente['id_persona'] ?>"
                                                aria-expanded="false"
                                                title="Ver detalles"
                                                style="background:transparent; border:none; color:#0b8f70; font-size:22px; line-height:1; padding:2px 6px; cursor:pointer;">
                                            <i class="bi bi-chevron-down" style="font-weight:900; -webkit-text-stroke:1.6px currentColor; font-size:28px;"></i>
                                        </button>
                                </td>
                            </tr>

                            <tr id="detalle-<?= $cliente['id_persona'] ?>" class="fila-detalle" style="display:none;">
                                <td colspan="8">
                                    <div style="background:#111; padding:20px; border-left:4px solid #0b8f70; border-radius:8px;">
                                        <h5 style="margin-bottom:15px;">
                                            Información de <?= esc($cliente['nombre']) ?> <?= esc($cliente['apellido']) ?>
                                        </h5>

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <strong>Email:</strong>
                                                <?= !empty($cliente['email']) ? esc($cliente['email']) : '<span style="color:#d98b45;">Sin email</span>' ?>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <strong>Teléfono:</strong>
                                                <?= esc($cliente['telefono']) ?>
                                            </div>
                                        </div>

                                        <?php if(!empty($sistemas)): ?>
                                            <h6 style="margin:18px 0 10px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70;">
                                                Sistemas / suscripciones
                                            </h6>

                                            <?php if(count($sistemas) > 1): ?>
                                                <?php
                                                    $hoyTs = strtotime(date('Y-m-d'));

                                                    $alDia = count(array_filter($sistemas, function ($s) use ($hoyTs) {
                                                        $tsVenc = !empty($s['fecha_vencimiento'])
                                                            ? strtotime((string) $s['fecha_vencimiento'])
                                                            : null;

                                                        return $tsVenc && $tsVenc >= $hoyTs;
                                                    }));
                                                ?>
                                                <p class="mb-2" style="color:#cfcfcf;">
                                                    Sistemas al día: 
                                                    <strong style="color:<?= $alDia === count($sistemas) ? '#0b8f70' : '#d98b45' ?>;">
                                                        <?= $alDia ?>
                                                    </strong>
                                                    de <?= count($sistemas) ?>
                                                </p>
                                            <?php endif; ?>

                                            <div class="table-responsive">
                                                <table class="table table-dark table-sm align-middle mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Sistema</th>
                                                            <th>Último pago</th>
                                                            <th>Vencimiento</th>
                                                            <th>Mensualidad</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($sistemas as $sistema): ?>
                                                            <tr>
                                                                <td><?= esc($sistema['nombre_sistema']) ?></td>
                                                                <td>
                                                                    <?= !empty($sistema['ultimo_pago_sistema'])
                                                                        ? formatear_fecha($sistema['ultimo_pago_sistema'])
                                                                        : '<span style="color:#d98b45;">Sin pagos</span>' ?>
                                                                </td>
                                                                <td><?= formatear_fecha($sistema['fecha_vencimiento']) ?></td>
                                                                <td><?= formatear_monto($sistema['precio_mensualidad']) ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                    <?php if (count($sistemas) > 1): ?>
                                                        <?php
                                                            $totalMensualidad = array_sum(array_map(function ($s) {
                                                                return (float) ($s['precio_mensualidad'] ?? 0);
                                                            }, $sistemas));
                                                        ?>
                                                        <tfoot>
                                                            <tr style="border-top:2px solid #0b8f70;">
                                                                <td colspan="3" class="text-end" style="font-weight:bold;">Total</td>
                                                                <td style="font-weight:bold; color:#0b8f70;">
                                                                    <?= formatear_monto($totalMensualidad) ?>
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    <?php endif; ?>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <p class="mb-0 mt-3" style="color:#cfcfcf;">El cliente no tiene sistemas ni suscripciones registradas.</p>
                                        <?php endif; ?>

                                        <div style="margin-top:18px; text-align:right;">
                                            <?php if(session()->get('id_rol') == 1): ?>
                                                <a href="<?= base_url('editar_cliente/'.$cliente['id_persona']) ?>"
                                                   class="btn btn-primary btn-sm">
                                                    Editar
                                                </a>
                                            <?php endif; ?>

                                    <a href="<?= base_url('cliente_info/'.$cliente['id_persona']) ?>"
                                       class="nfs-btn nfs-btn-outline nfs-btn-sm" style="margin-left:6px;">
                                        Ver más
                                    </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                        <tr id="filtroVacioClientes" class="text-center" style="display:none;">
                            <td colspan="8">No hay clientes que coincidan con los filtros.</td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No hay registros.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </section>

    <!-- MODAL REGISTRAR CLIENTE -->
    <div class="modal fade" id="modalRegistrarCliente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="background:#1b1b1b; color:white; border:1px solid #0b8f70;">
                <div class="modal-header" style="border-bottom:1px solid #333;">
                    <h5 class="modal-title" style="color:#0b8f70;">Registrar nuevo cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1);"></button>
                </div>

                <form action="<?= base_url('registrar_cliente') ?>" method="post">
                    <div class="modal-body">
                        <h6 style="margin-bottom:15px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:14px;">
                            Datos del cliente
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
                            Primer pago
                        </h6>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Sistema *</label>
                                <select name="id_sistema" id="sistemaRegistro" class="form-control" required>
                                    <option value="">Seleccionar sistema...</option>
                                    <?php foreach($sistemasRegistro as $sistema): ?>
                                        <option value="<?= $sistema['id_sistema'] ?>"
                                                data-precio="<?= esc($sistema['precio']) ?>">
                                            <?= esc($sistema['nombre_sistema']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3" id="horarioRegistroWrap" style="display:none;">
                                <label class="form-label">Turno *</label>
                                <select name="id_horario" id="horarioRegistro" class="form-control" disabled>
                                    <option value="">Elegí un sistema primero...</option>
                                </select>
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
                                <label class="form-label">Monto</label>
                                <input type="number" step="0.01" min="0" name="monto"
                                       id="montoRegistro" class="form-control"
                                       placeholder="Se carga automático">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer" style="border-top:1px solid #333;">
                        <button type="button" class="nfs-btn nfs-btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="nfs-btn nfs-btn-primary">Registrar cliente y primer pago</button>
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
    const buscarCliente = document.getElementById('buscarCliente');
    const btnSortAz = document.getElementById('btnSortAz');
    const btnSortPago = document.getElementById('btnSortPago');

    const filtroDisciplina = document.getElementById('filtroDisciplina');
    const filtroEstado = document.getElementById('filtroEstado');
    const filtroMontoMin = document.getElementById('filtroMontoMin');
    const filtroMontoMax = document.getElementById('filtroMontoMax');

    const tablaClientes = document.getElementById('tablaClientes');

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
        if (!tablaClientes) return;

        const texto = (buscarCliente?.value || '').toLowerCase().trim();
        const disciplina = filtroDisciplina?.value || '';
        const estado = filtroEstado?.value || '';
        const montoMin = filtroMontoMin?.value !== '' && filtroMontoMin?.value !== undefined ? parseFloat(filtroMontoMin.value) : null;
        const montoMax = filtroMontoMax?.value !== '' && filtroMontoMax?.value !== undefined ? parseFloat(filtroMontoMax.value) : null;

        let visibles = 0;

        Array.from(tablaClientes.tBodies[0].children).forEach(function (fila) {
            if (fila.tagName !== 'TR' || fila.classList.contains('fila-detalle')) return;

            const celdas = fila.children;
            // Busca solo en nombre, apellido o DNI (los demás campos tienen filtro propio)
            const busqueda = (celdas[1]?.textContent || '') + ' '
                + (celdas[2]?.textContent || '') + ' '
                + (celdas[0]?.textContent || '');
            const coincideTexto = !texto || busqueda.toLowerCase().includes(texto);

            const sistemasFila = (fila.dataset.sistema || '').split('|').filter(Boolean);
            const coincideDisciplina = !disciplina || sistemasFila.includes(disciplina);

            // "Al día" / "Vencido": los estados extra (Sin pagos, Sin suscripción) también
            // se consideran vencidos para que el filtro no los pierda
            const estadoFila = fila.dataset.estado || '';
            let coincideEstado = true;
            if (estado === 'Al día') coincideEstado = estadoFila === 'Al día';
            else if (estado === 'Vencido') coincideEstado = estadoFila !== 'Al día';

            const monto = fila.dataset.monto !== '' && fila.dataset.monto !== undefined
                ? parseFloat(fila.dataset.monto) : null;
            const coincideMonto = (montoMin === null || (monto !== null && monto >= montoMin))
                && (montoMax === null || (monto !== null && monto <= montoMax));

            const visible = coincideTexto && coincideDisciplina
                && coincideEstado && coincideMonto;

            fila.style.display = visible ? '' : 'none';
            if (visible) visibles++;

            colapsarDetalle(fila);
        });

        const vacio = document.getElementById('filtroVacioClientes');
        if (vacio) vacio.style.display = visibles === 0 ? 'table-row' : 'none';
    }

    // Búsqueda por nombre, apellido o DNI; filtra al instante
    buscarCliente?.addEventListener('input', aplicarFiltros);

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
            [filtroDisciplina, filtroEstado, filtroMontoMin, filtroMontoMax]
                .forEach(el => { if (el) el.value = ''; });
            aplicarFiltros();
        });
    }

    // ===== BOTONES DE ORDEN (siempre activos) =====
    // 1) A-Z / Z-A  -> orden alfabético por nombre
    // 2) Último pago -> Más reciente / Menos reciente
    // Los estados arrancan "sin aplicar": el primer clic aplica el rótulo visible.
    let ordenAzAplicado = false;
    let ordenPagoAplicado = false;

    btnSortAz?.addEventListener('click', function () {
        ordenAzAplicado = !ordenAzAplicado;

        const text = document.getElementById('sortAzText');
        const icon = this.querySelector('i');
        if (text) text.textContent = ordenAzAplicado ? 'A - Z' : 'Z - A';
        if (icon) icon.className = ordenAzAplicado ? 'bi bi-arrow-down' : 'bi bi-arrow-up';

        ordenarClientes(1, 'texto', ordenAzAplicado);
        aplicarFiltros();
    });

    // Icono de 3 líneas apiladas (embudo): más reciente = punta abajo
    // (línea superior larga), menos reciente = al revés (pirámide).
    function pintarIconoPago(reciente) {
        const icon = document.getElementById('sortPagoIcon');
        if (!icon) return;
        const lineas = icon.querySelectorAll('rect');
        // más reciente: 14, 9, 5 | menos reciente: 5, 9, 14
        const anchos = reciente ? [14, 9, 5] : [5, 9, 14];
        lineas.forEach(function (linea, i) {
            const w = anchos[i];
            linea.setAttribute('width', w);
            linea.setAttribute('x', (16 - w) / 2);
        });
    }

    btnSortPago?.addEventListener('click', function () {
        ordenPagoAplicado = !ordenPagoAplicado;

        const text = document.getElementById('sortPagoText');
        if (text) text.textContent = ordenPagoAplicado ? 'Último pago: Más reciente' : 'Último pago: Menos reciente';

        // Más reciente = fecha más reciente primero (desc)
        pintarIconoPago(ordenPagoAplicado);
        ordenarClientes(5, 'fecha', !ordenPagoAplicado);
        aplicarFiltros();
    });

    function ordenarClientes(indice, tipo, asc) {
        const tbody = tablaClientes.tBodies[0];
        const filas = Array.from(tbody.children)
            .filter(f => f.tagName === 'TR' && !f.classList.contains('fila-detalle') && f.id !== 'filtroVacioClientes');

        const parejas = filas.map(f => {
            const detalle = f.nextElementSibling && f.nextElementSibling.classList.contains('fila-detalle')
                ? f.nextElementSibling : null;
            return { fila: f, detalle: detalle };
        });

        parejas.sort((a, b) => {
            const celdaA = a.fila.children[indice];
            const celdaB = b.fila.children[indice];
            let valA, valB;

            if (tipo === 'numero' || tipo === 'fecha') {
                valA = parseFloat(celdaA.dataset.value || '0') || 0;
                valB = parseFloat(celdaB.dataset.value || '0') || 0;
                return asc ? valA - valB : valB - valA;
            }

            valA = (celdaA.textContent || '').trim().toLowerCase();
            valB = (celdaB.textContent || '').trim().toLowerCase();
            return asc ? valA.localeCompare(valB, 'es', { numeric: true }) : valB.localeCompare(valA, 'es', { numeric: true });
        });

        parejas.forEach(p => {
            tbody.appendChild(p.fila);
            if (p.detalle) tbody.appendChild(p.detalle);
        });

        const vacio = document.getElementById('filtroVacioClientes');
        if (vacio) tbody.appendChild(vacio);
    }

    aplicarFiltros();

    // DESPLEGAR INFORMACIÓN
    const botonesVerMas = document.querySelectorAll('.btnVerMas');

    botonesVerMas.forEach(function(boton) {
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
    const tablaScroll = document.getElementById('tablaScrollClientes');
    const scrollTop = document.getElementById('scrollTopClientes');
    const scrollTopInner = document.getElementById('scrollTopInnerClientes');

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

    // MONTO AUTOMÁTICO Y TURNOS SEGÚN SISTEMA
    const selectSistema = document.getElementById('sistemaRegistro');
    const inputMonto = document.getElementById('montoRegistro');
    const selectHorario = document.getElementById('horarioRegistro');
    const horarioWrap = document.getElementById('horarioRegistroWrap');
    const horariosPorSistema = <?= json_encode($horariosPorSistema ?? [], JSON_UNESCAPED_UNICODE) ?>;
    const accesosLibres = <?= json_encode($sistemasAccesoLibre ?? [], JSON_UNESCAPED_UNICODE) ?>;

    function poblarHorarios(idSistema) {
        if (!selectHorario) return;

        selectHorario.innerHTML = '';

        const esLibre = accesosLibres.indexOf(parseInt(idSistema, 10)) !== -1;

        if (horarioWrap) {
            horarioWrap.style.display = esLibre ? 'none' : '';
        }

        if (esLibre) {
            selectHorario.disabled = true;
            selectHorario.required = false;
            selectHorario.innerHTML = '<option value="">Acceso libre (cualquier horario)</option>';
            return;
        }

        const horarios = horariosPorSistema[idSistema] || [];

        if (!idSistema) {
            selectHorario.disabled = true;
            selectHorario.required = false;
            selectHorario.innerHTML = '<option value="">Elegí un sistema primero...</option>';
            return;
        }

        if (horarios.length === 0) {
            selectHorario.disabled = true;
            selectHorario.required = false;
            selectHorario.innerHTML = '<option value="">Sin turnos disponibles</option>';
            return;
        }

        selectHorario.disabled = false;
        selectHorario.required = true;
        selectHorario.innerHTML = '<option value="">Seleccionar turno...</option>';

        horarios.forEach(function(h) {
            const opt = document.createElement('option');
            opt.value = h.id_horario;
            const cupo = h.cupo_max === null ? 'sin límite' : (h.inscriptos + '/' + h.cupo_max);
            opt.textContent = h.label + ' (' + cupo + ')' + (h.lleno ? ' — lleno' : '');
            if (h.lleno) {
                opt.disabled = true;
            }
            selectHorario.appendChild(opt);
        });
    }

    if (selectSistema && inputMonto) {
        selectSistema.addEventListener('change', function() {
            const opcion = this.options[this.selectedIndex];
            inputMonto.value = opcion && opcion.dataset.precio ? opcion.dataset.precio : '';
            poblarHorarios(this.value);
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
