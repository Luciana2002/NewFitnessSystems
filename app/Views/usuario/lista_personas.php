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

        <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:30px;">
            <h1 style="margin:0;">Lista de Personas</h1>

            <input type="text" id="buscarPersona" class="form-control"
                   placeholder="Buscar persona..."
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
            <table class="table table-dark table-striped table-hover" id="tablaPersonas">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Sistema/s</th>
                        <th>Cuota</th>
                        <th>Último pago</th>
                        <th>Estado</th>
                        <th>Ver más</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if(!empty($personas)): ?>
                        <?php foreach($personas as $persona): ?>
                            <tr>
                                <td><?= esc($persona['nombre']) ?></td>
                                <td><?= esc($persona['apellido']) ?></td>
                                <td><?= esc($persona['sistemas'] ?? 'Sin sistema') ?></td>
                                <td>
                                    $<?= number_format($persona['cuota'] ?? 0, 0, ',', '.') ?>
                                </td>
                                <td>
                                    <?= !empty($persona['fecha_ultimo_pago']) 
                                        ? date('d/m/Y', strtotime($persona['fecha_ultimo_pago'])) 
                                        : 'Sin pagos' ?>
                                </td>
                                <td>
                                    <?php if(($persona['estado_pago'] ?? '') == 'Pagada'): ?>
                                        <span class="badge bg-success">Pagada</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Vencida</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm btnVerMas">
                                        Ver más +
                                    </button>
                                </td>
                            </tr>

                            <tr class="detallePersona" style="display:none;">
                                <td colspan="7">
                                    <div style="background:#111; padding:22px; border-left:4px solid #0b8f70;">
                                        <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:15px 30px;">

                                            <div>
                                                <strong>DNI:</strong>
                                                <?= esc($persona['dni'] ?? '-') ?>
                                            </div>

                                            <div>
                                                <strong>Email:</strong>
                                                <?= esc($persona['email'] ?? '-') ?>
                                            </div>

                                            <div>
                                                <strong>Teléfono:</strong>
                                                <?= esc($persona['telefono'] ?? '-') ?>
                                            </div>

                                            <div>
                                                <strong>Fecha inscripción:</strong>
                                                <?= !empty($persona['fecha_inscripcion']) 
                                                    ? date('d/m/Y', strtotime($persona['fecha_inscripcion'])) 
                                                    : 'Sin inscripción' ?>
                                            </div>

                                            <div>
                                                <strong>Usuario:</strong>
                                                <?= !empty($persona['nombre_usuario']) 
                                                    ? esc($persona['nombre_usuario']) 
                                                    : 'No registrado' ?>
                                            </div>

                                            <div>
                                                <strong>Estado persona:</strong>
                                                <?= ($persona['baja'] ?? 'N') == 'S' ? 'Baja' : 'Activa' ?>
                                            </div>

                                        </div>

                                        <div style="margin-top:20px; display:flex; gap:10px; flex-wrap:wrap;">
                                            <a href="<?= base_url('personas/editar/'.$persona['id_persona']) ?>" class="btn btn-primary btn-sm">
                                                Editar información
                                            </a>

                                            <?php if(($persona['baja'] ?? 'N') == 'N'): ?>
                                                <a href="<?= base_url('personas/baja/'.$persona['id_persona']) ?>" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('¿Seguro que querés dar de baja a esta persona?')">
                                                    Dar de baja
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= base_url('personas/alta/'.$persona['id_persona']) ?>" 
                                                   class="btn btn-success btn-sm">
                                                    Activar
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay personas registradas</td>
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
    }

    const buscarPersona = document.getElementById('buscarPersona');
    const tablaPersonas = document.getElementById('tablaPersonas');

    if (buscarPersona && tablaPersonas) {
        buscarPersona.addEventListener('keyup', function () {
            const texto = this.value.toLowerCase();
            const filas = tablaPersonas.querySelectorAll('tbody tr');

            filas.forEach(function (fila) {
                fila.style.display = fila.textContent.toLowerCase().includes(texto) ? '' : 'none';
            });
        });
    }

    document.querySelectorAll('.btnVerMas').forEach(function(boton) {
        boton.addEventListener('click', function() {
            const filaDetalle = this.closest('tr').nextElementSibling;

            if (filaDetalle.style.display === 'none' || filaDetalle.style.display === '') {
                filaDetalle.style.display = 'table-row';
                this.textContent = 'Ver menos -';
            } else {
                filaDetalle.style.display = 'none';
                this.textContent = 'Ver más +';
            }
        });
    });
</script>