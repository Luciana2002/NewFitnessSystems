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
            <h1 style="margin:0;">Horarios</h1>
        </div>

        <input type="text" id="buscarHorario" class="form-control"
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

        <?php
        $ordenDias = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];
        $porDia = [];

        foreach ($horarios as $horario) {
            $porDia[$horario['dia_semana']][] = $horario;
        }
        ?>

        <!-- SCROLL HORIZONTAL SUPERIOR -->
        <div id="scrollTopHorarios"
             style="position:sticky; top:0; z-index:20; overflow-x:auto; overflow-y:hidden; height:14px; background:#262626; display:none;">
            <div id="scrollTopInnerHorarios" style="height:1px;"></div>
        </div>

        <div class="table-responsive" id="tablaScrollHorarios" style="background:#1b1b1b; padding:25px; border-left:5px solid #0b8f70; overflow-x:auto;">
            <table class="table table-dark table-striped table-hover align-middle" id="tablaHorarios">
                <thead>
                    <tr>
                        <th style="background:#3a3f47; color:#fff; font-size:15px; letter-spacing:.5px; text-transform:uppercase;">Sistema</th>
                        <th style="background:#3a3f47; color:#fff; font-size:15px; letter-spacing:.5px; text-transform:uppercase;">Inicio</th>
                        <th style="background:#3a3f47; color:#fff; font-size:15px; letter-spacing:.5px; text-transform:uppercase;">Fin</th>
                        <th style="background:#3a3f47; color:#fff; font-size:15px; letter-spacing:.5px; text-transform:uppercase;">Acciones</th>
                    </tr>
                </thead>

                <?php if (empty($horarios)): ?>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center">No hay horarios registrados.</td>
                        </tr>
                    </tbody>
                <?php else: ?>

                    <?php foreach ($ordenDias as $dia): ?>
                        <?php if (empty($porDia[$dia])) continue; ?>

                        <tbody class="seccionDia" data-dia="<?= $dia ?>">
                            <tr>
                                <th colspan="4" class="text-center"
                                    style="background:linear-gradient(90deg,#0fb892,#0b8f70); color:#fff; text-transform:uppercase; letter-spacing:2px; font-size:17px; font-weight:800; padding:12px 12px; border-bottom:3px solid #14ffc0;">
                                    <?= $dia ?>
                                </th>
                            </tr>

                            <?php foreach ($porDia[$dia] as $horario): ?>
                                <tr>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            <button type="button"
                                                    class="btnColorSistema"
                                                    data-id="<?= $horario['id_sistema'] ?>"
                                                    data-color="<?= esc($horario['color'] ?? '#0b8f70') ?>"
                                                    title="Cambiar color del sistema"
                                                    style="width:26px; height:26px; border-radius:50%; border:2px solid #fff; background:<?= esc($horario['color'] ?? '#0b8f70') ?>; cursor:pointer; padding:0; flex-shrink:0;"></button>
                                            <span style="font-weight:600;"><?= esc($horario['nombre_sistema']) ?></span>
                                        </div>
                                    </td>
                                    <td><?= formatear_hora($horario['hora_inicio']) ?></td>
                                    <td><?= formatear_hora($horario['hora_fin']) ?></td>
                                    <td style="white-space: nowrap;">
                                        <button type="button"
                                                class="btn btn-primary btn-sm btnEditarHorario"
                                                data-id="<?= $horario['id_horario'] ?>"
                                                data-sistema="<?= $horario['id_sistema'] ?>"
                                                data-dia="<?= esc($horario['dia_semana']) ?>"
                                                data-inicio="<?= esc(formatear_hora($horario['hora_inicio'])) ?>"
                                                data-fin="<?= esc(formatear_hora($horario['hora_fin'])) ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditarHorario">
                                            Editar
                                        </button>

                                        <a href="<?= base_url('eliminar_horario/'.$horario['id_horario']) ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('¿Desea eliminar este horario?');">
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    <?php endforeach; ?>

                    <tbody id="sinResultados" style="display:none;">
                        <tr>
                            <td colspan="4" class="text-center">Sin resultados para la búsqueda.</td>
                        </tr>
                    </tbody>

                <?php endif; ?>
            </table>

            <input type="color" id="colorPickerGlobal" style="display:none;">
        </div>
    </section>

    <!-- MODAL EDITAR HORARIO -->
    <div class="modal fade" id="modalEditarHorario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background:#1b1b1b; color:white; border:1px solid #0b8f70;">
                <div class="modal-header" style="border-bottom:1px solid #333;">
                    <h5 class="modal-title" style="color:#0b8f70;">Editar horario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1);"></button>
                </div>

                <form id="formEditarHorario" action="" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Sistema *</label>
                            <select name="id_sistema" id="sistemaHorario" class="form-control" required>
                                <option value="">Seleccionar sistema...</option>
                                <?php foreach($sistemas as $sistema): ?>
                                    <option value="<?= $sistema['id_sistema'] ?>">
                                        <?= esc($sistema['nombre_sistema']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Día de la semana *</label>
                            <select name="dia_semana" id="diaHorario" class="form-control" required>
                                <?php foreach(['Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo'] as $dia): ?>
                                    <option value="<?= $dia ?>"><?= $dia ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Hora inicio *</label>
                                    <input type="time" name="hora_inicio" id="inicioHorario" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Hora fin *</label>
                                    <input type="time" name="hora_fin" id="finHorario" class="form-control" required>
                                </div>
                            </div>
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

    // BUSCADOR (por sección de día)
    const buscarHorario = document.getElementById('buscarHorario');
    const tablaHorarios = document.getElementById('tablaHorarios');
    const sinResultados = document.getElementById('sinResultados');

    if (buscarHorario && tablaHorarios) {
        buscarHorario.addEventListener('keyup', function () {
            const texto = this.value.toLowerCase();
            const secciones = tablaHorarios.querySelectorAll('tbody.seccionDia');

            let coincidencias = 0;

            secciones.forEach(function (seccion) {
                const coincide = seccion.textContent.toLowerCase().includes(texto);
                seccion.style.display = coincide ? '' : 'none';
                if (coincide) coincidencias++;
            });

            if (sinResultados) {
                sinResultados.style.display = coincidencias === 0 ? '' : 'none';
            }
        });
    }

    // SCROLL HORIZONTAL SUPERIOR
    const tablaScroll = document.getElementById('tablaScrollHorarios');
    const scrollTop = document.getElementById('scrollTopHorarios');
    const scrollTopInner = document.getElementById('scrollTopInnerHorarios');

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

    // MODAL EDITAR: cargar datos del horario
    const formEditarHorario = document.getElementById('formEditarHorario');
    const botonesEditar = document.querySelectorAll('.btnEditarHorario');

    botonesEditar.forEach(function(boton) {
        boton.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const idSistema = this.getAttribute('data-sistema');
            const dia = this.getAttribute('data-dia');
            const inicio = this.getAttribute('data-inicio');
            const fin = this.getAttribute('data-fin');

            formEditarHorario.action = '<?= base_url('actualizar_horario/') ?>' + id;
            document.getElementById('sistemaHorario').value = idSistema;
            document.getElementById('diaHorario').value = dia;
            document.getElementById('inicioHorario').value = inicio;
            document.getElementById('finHorario').value = fin;
        });
    });

    // COLOR DEL SISTEMA
    const colorPickerGlobal = document.getElementById('colorPickerGlobal');
    let sistemaColorActivo = null;

    document.querySelectorAll('.btnColorSistema').forEach(function(boton) {
        boton.addEventListener('click', function() {
            sistemaColorActivo = this.getAttribute('data-id');
            colorPickerGlobal.value = this.getAttribute('data-color') || '#0b8f70';
            colorPickerGlobal.click();
        });
    });

    if (colorPickerGlobal) {
        colorPickerGlobal.addEventListener('change', function() {
            const color = this.value;
            const id = sistemaColorActivo;

            if (!id) return;

            const form = new FormData();
            form.append('color', color);

            fetch('<?= base_url('cambiar_color_sistema/') ?>' + id, {
                method: 'POST',
                body: form
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    document.querySelectorAll('.btnColorSistema[data-id="' + id + '"]').forEach(function(b) {
                        b.setAttribute('data-color', color);
                        b.style.background = color;
                    });
                } else {
                    alert('No se pudo guardar el color');
                }
            })
            .catch(() => alert('Error al guardar el color'));
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
