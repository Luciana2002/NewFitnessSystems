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

        <input type="text" id="buscarPago" class="form-control"
               placeholder="Buscar por cliente, sistema o medio..."
               style="max-width:380px; margin-bottom:30px;">

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
                            <tr>
                                <td><?= formatear_fecha($pago['fecha_pago']) ?></td>
                                <td><?= esc($pago['nombre']) ?> <?= esc($pago['apellido']) ?></td>
                                <td><?= esc($pago['dni']) ?></td>
                                <td><?= esc($pago['nombre_sistema']) ?></td>
                                <td><?= formatear_monto($pago['monto']) ?></td>
                                <td><?= esc($pago['medio_pago']) ?></td>
                                <td><?= esc($pago['cobrado_por']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay pagos registrados.</td>
                        </tr>
                    <?php endif; ?>
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

    // BUSCADOR
    const buscarPago = document.getElementById('buscarPago');
    const tablaPagos = document.getElementById('tablaPagos');

    if (buscarPago && tablaPagos) {
        buscarPago.addEventListener('keyup', function () {
            const texto = this.value.toLowerCase();
            const filas = tablaPagos.querySelectorAll('tbody tr');

            filas.forEach(function (fila) {
                const coincide = fila.textContent.toLowerCase().includes(texto);
                fila.style.display = coincide ? '' : 'none';
            });
        });
    }

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
