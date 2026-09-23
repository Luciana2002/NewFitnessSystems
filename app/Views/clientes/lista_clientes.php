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
                    class="btn btn-success"
                    data-bs-toggle="modal"
                    data-bs-target="#modalRegistrarCliente">
                <i class="bi bi-person-plus"></i> Registrar cliente
            </button>
        </div>

        <!-- BUSCADOR -->
        <input type="text" id="buscarCliente" class="form-control"
               placeholder="Buscar..."
               style="max-width:320px; margin-bottom:30px;">

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

                                $primerSistema = $sistemas[0] ?? null;
                                $nombreSistema = $primerSistema['nombre_sistema'] ?? '—';
                                $montoCuota    = $primerSistema['precio_mensualidad'] ?? null;
                                $ultimoPagoSist= $primerSistema['ultimo_pago_sistema']
                                                 ?? $primerSistema['ultimo_pago']
                                                 ?? $ultimoPago;

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

                            <tr>
                                <td><?= esc($cliente['dni']) ?></td>
                                <td><?= esc($cliente['nombre']) ?></td>
                                <td><?= esc($cliente['apellido']) ?></td>
                                <td><?= esc($nombreSistema) ?></td>
                                <td><?= $montoCuota !== null ? '$ ' . number_format((float) $montoCuota, 2) : '—' ?></td>
                                <td><?= !empty($ultimoPagoSist) ? date('d/m/Y', strtotime((string) $ultimoPagoSist)) : '—' ?></td>
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
                                               class="btn btn-outline-success btn-sm" style="margin-left:6px;">
                                                Ver más
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay registros.</td>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Registrar cliente y primer pago</button>
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
    const buscarCliente = document.getElementById('buscarCliente');
    const tablaClientes = document.getElementById('tablaClientes');

    if (buscarCliente && tablaClientes) {
        buscarCliente.addEventListener('keyup', function () {
            const texto = this.value.toLowerCase();
            const filas = tablaClientes.querySelectorAll('tbody tr');

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

    // MONTO AUTOMÁTICO SEGÚN SISTEMA
    const selectSistema = document.getElementById('sistemaRegistro');
    const inputMonto = document.getElementById('montoRegistro');

    if (selectSistema && inputMonto) {
        selectSistema.addEventListener('change', function() {
            const opcion = this.options[this.selectedIndex];
            inputMonto.value = opcion && opcion.dataset.precio ? opcion.dataset.precio : '';
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
