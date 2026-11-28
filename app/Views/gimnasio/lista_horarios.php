<main class="dashboard-page">

    <button type="button" id="sidebarOpen" class="sidebar-handle">
        <span>›</span>
    </button>

    <button type="button" id="sidebarClose" class="sidebar-close">
        ✕
    </button>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <?= view('layout/sidebar') ?>

    <?php
        $ordenDias = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];

        $nombresDias = [
            'Lunes'     => 'Lunes',
            'Martes'    => 'Martes',
            'Miercoles' => 'Miércoles',
            'Jueves'    => 'Jueves',
            'Viernes'   => 'Viernes',
            'Sabado'    => 'Sábado',
            'Domingo'   => 'Domingo',
        ];

        $porDia = [];

        foreach ($horarios as $horario) {
            $porDia[$horario['dia_semana']][] = $horario;
        }

        foreach ($porDia as &$listaDia) {
            usort($listaDia, static function ($a, $b) {
                $horaA = strtotime($a['hora_inicio']);
                $horaB = strtotime($b['hora_inicio']);

                if ($horaA === $horaB) {
                    return strcasecmp($a['nombre_sistema'], $b['nombre_sistema']);
                }

                return $horaA <=> $horaB;
            });
        }
        unset($listaDia);
    ?>

    <section class="dashboard-content">

        <div class="horarios-cabecera">
            <h1>Horarios</h1>

            <button type="button" class="nfs-btn nfs-btn-primary"
                    data-bs-toggle="modal" data-bs-target="#modalAgregarHorario">
                <i class="bi bi-plus-lg"></i> Agregar horario
            </button>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="toast-custom success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="toast-custom error">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <div class="horarios-tabs" role="tablist" aria-label="Filtrar horarios por día">
            <button type="button" class="horarios-tab activa" data-dia="todos"
                    role="tab" aria-selected="true">
                Todos
                <span class="horarios-tab-count"><?= count($horarios) ?></span>
            </button>

            <?php foreach ($ordenDias as $dia): ?>
                <button type="button" class="horarios-tab" data-dia="<?= esc($dia) ?>"
                        role="tab" aria-selected="false">
                    <?= esc($nombresDias[$dia]) ?>
                    <span class="horarios-tab-count"><?= count($porDia[$dia] ?? []) ?></span>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="table-responsive horarios-tabla-wrap">
            <table class="table table-dark table-striped table-hover align-middle" id="tablaHorarios">

                <?php if (empty($horarios)): ?>
                    <tbody data-dia="todos">
                        <tr>
                            <td colspan="5" class="text-center">No hay horarios registrados.</td>
                        </tr>
                    </tbody>
                <?php else: ?>

                    <?php foreach ($ordenDias as $dia): ?>
                        <?php if (empty($porDia[$dia])) continue; ?>

                        <tbody class="seccionDia" data-dia="<?= esc($dia) ?>">
                            <tr>
                                <th colspan="5" class="horarios-dia-header">
                                    <?= esc($nombresDias[$dia]) ?>
                                </th>
                            </tr>

                            <tr>
                                <th class="horarios-col-header">Sistema</th>
                                <th class="horarios-col-header">Inicio</th>
                                <th class="horarios-col-header">Fin</th>
                                <th class="horarios-col-header">Inscriptos / cupo</th>
                                <th class="horarios-col-header">Acciones</th>
                            </tr>

                            <?php foreach ($porDia[$dia] as $horario): ?>
                                <tr>
                                    <td>
                                        <div class="horarios-sistema">
                                            <button type="button"
                                                    class="btnColorSistema"
                                                    data-id="<?= (int) $horario['id_sistema'] ?>"
                                                    data-color="<?= esc($horario['color'] ?? '#0b8f70') ?>"
                                                    title="Cambiar color del sistema"></button>
                                            <span class="horarios-sistema-nombre">
                                                <?= esc($horario['nombre_sistema']) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td><?= formatear_hora($horario['hora_inicio']) ?></td>
                                    <td><?= formatear_hora($horario['hora_fin']) ?></td>
                                    <td><?= esc(nfs_horario_etiqueta_cupo($horario)) ?></td>
                                    <td>
                                        <div class="horarios-acciones">
                                            <button type="button"
                                                    class="nfs-btn nfs-btn-primary nfs-btn-sm btnEditarHorario"
                                                    data-id="<?= (int) $horario['id_horario'] ?>"
                                                    data-sistema="<?= (int) $horario['id_sistema'] ?>"
                                                    data-dia="<?= esc($horario['dia_semana']) ?>"
                                                    data-inicio="<?= esc($horario['hora_inicio']) ?>"
                                                    data-fin="<?= esc($horario['hora_fin']) ?>"
                                                    data-cupo="<?= esc($horario['cupo_max']) ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEditarHorario">
                                                Editar
                                            </button>

                                            <button type="button"
                                                    class="nfs-btn nfs-btn-secondary nfs-btn-sm btnDuplicarHorario"
                                                    data-sistema="<?= (int) $horario['id_sistema'] ?>"
                                                    data-dia="<?= esc($horario['dia_semana']) ?>"
                                                    data-inicio="<?= esc($horario['hora_inicio']) ?>"
                                                    data-fin="<?= esc($horario['hora_fin']) ?>"
                                                    data-cupo="<?= esc($horario['cupo_max']) ?>">
                                                <i class="bi bi-copy"></i> Duplicar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    <?php endforeach; ?>

                <?php endif; ?>
            </table>

            <input type="color" id="colorPickerGlobal" class="horarios-color-picker-oculto">
        </div>

    </section>

    <!-- MODAL AGREGAR HORARIO -->
    <div class="modal fade horarios-modal" id="modalAgregarHorario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content horarios-modal-content">
                <div class="modal-header horarios-modal-header">
                    <h5 class="modal-title horarios-modal-title">Agregar horario</h5>
                    <button type="button" class="btn-close horarios-btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="formAgregarHorario" action="<?= base_url('guardar_horario') ?>" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="sistemaAgregar">Sistema *</label>
                            <select id="sistemaAgregar" name="id_sistema" class="form-control horarios-input" required>
                                <option value="">Seleccionar sistema...</option>
                                <?php foreach ($sistemasActivos as $sistema): ?>
                                    <option value="<?= (int) $sistema['id_sistema'] ?>">
                                        <?= esc($sistema['nombre_sistema']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <span class="form-label">Días de la semana *</span>

                            <div class="horarios-dias-checks">
                                <label class="horarios-dia-check horarios-dia-check-todos">
                                    <input type="checkbox" id="checkTodosDias">
                                    Toda la semana
                                </label>

                                <?php foreach ($ordenDias as $dia): ?>
                                    <label class="horarios-dia-check">
                                        <input type="checkbox"
                                               class="horarios-dia-input"
                                               name="dias[]"
                                               value="<?= esc($dia) ?>">
                                        <?= esc($nombresDias[$dia]) ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>

                            <small class="horarios-help">Seleccioná uno o más días para crear el mismo horario.</small>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label" for="inicioAgregar">Hora inicio *</label>
                                    <input type="time" id="inicioAgregar" name="hora_inicio"
                                           class="form-control horarios-input" required>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label" for="finAgregar">Hora fin *</label>
                                    <input type="time" id="finAgregar" name="hora_fin"
                                           class="form-control horarios-input" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="cupoAgregar">Cupo máximo de inscriptos</label>
                            <input type="number" id="cupoAgregar" name="cupo_max"
                                   class="form-control horarios-input" min="0" step="1" placeholder="Sin límite">
                            <small class="horarios-help">Dejalo vacío para no poner límite. Ej: 20 por turno.</small>
                        </div>
                    </div>

                    <div class="modal-footer horarios-modal-footer">
                        <button type="button" class="nfs-btn nfs-btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="nfs-btn nfs-btn-primary">Guardar horarios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR HORARIO -->
    <div class="modal fade horarios-modal" id="modalEditarHorario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content horarios-modal-content">
                <div class="modal-header horarios-modal-header">
                    <h5 class="modal-title horarios-modal-title">Editar horario</h5>
                    <button type="button" class="btn-close horarios-btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="formEditarHorario" action="" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="sistemaHorario">Sistema *</label>
                            <select id="sistemaHorario" name="id_sistema" class="form-control horarios-input" required>
                                <option value="">Seleccionar sistema...</option>
                                <?php foreach ($sistemasActivos as $sistema): ?>
                                    <option value="<?= (int) $sistema['id_sistema'] ?>">
                                        <?= esc($sistema['nombre_sistema']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="diaHorario">Día de la semana *</label>
                            <select id="diaHorario" name="dia_semana" class="form-control horarios-input" required>
                                <?php foreach ($ordenDias as $dia): ?>
                                    <option value="<?= esc($dia) ?>"><?= esc($nombresDias[$dia]) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label" for="inicioHorario">Hora inicio *</label>
                                    <input type="time" id="inicioHorario" name="hora_inicio"
                                           class="form-control horarios-input" required>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label" for="finHorario">Hora fin *</label>
                                    <input type="time" id="finHorario" name="hora_fin"
                                           class="form-control horarios-input" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="cupoHorario">Cupo máximo de inscriptos</label>
                            <input type="number" id="cupoHorario" name="cupo_max"
                                   class="form-control horarios-input" min="0" step="1" placeholder="Sin límite">
                            <small class="horarios-help">Dejalo vacío para no poner límite. Ej: 20 por turno.</small>
                        </div>
                    </div>

                    <div class="modal-footer horarios-modal-footer">
                        <a id="btnEliminarHorario" href="#"
                           class="nfs-btn nfs-btn-danger nfs-btn-sm horarios-btn-eliminar">
                            Eliminar
                        </a>
                        <button type="button" class="nfs-btn nfs-btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="nfs-btn nfs-btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</main>

<script>
    const aHoraInput = (valor) => (valor || '').toString().slice(0, 5);

    // SIDEBAR
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
        document.addEventListener('keydown', function (evento) {
            if (evento.key === 'Escape') {
                cerrarSidebar();
            }
        });
    }

    // PESTAÑAS POR DÍA
    const pestañasHorarios = document.querySelectorAll('.horarios-tab');
    const seccionesHorarios = document.querySelectorAll('#tablaHorarios tbody[data-dia]');

    pestañasHorarios.forEach(function (pestaña) {
        pestaña.addEventListener('click', function () {
            const dia = this.dataset.dia;

            pestañasHorarios.forEach(function (otra) {
                const activa = otra === pestaña;
                otra.classList.toggle('activa', activa);
                otra.setAttribute('aria-selected', activa ? 'true' : 'false');
            });

            seccionesHorarios.forEach(function (seccion) {
                seccion.classList.toggle('oculto', dia !== 'todos' && seccion.dataset.dia !== dia);
            });
        });
    });

    // MODAL AGREGAR: días múltiples
    const formAgregarHorario = document.getElementById('formAgregarHorario');
    const checkTodosDias = document.getElementById('checkTodosDias');
    const checkDias = document.querySelectorAll('.horarios-dia-input');

    if (checkTodosDias) {
        checkTodosDias.addEventListener('change', function () {
            checkDias.forEach(function (check) {
                check.checked = checkTodosDias.checked;
            });
        });
    }

    checkDias.forEach(function (check) {
        check.addEventListener('change', function () {
            if (!checkTodosDias) return;

            const todosMarcados = Array.prototype.every.call(checkDias, function (item) {
                return item.checked;
            });

            checkTodosDias.checked = todosMarcados;
        });
    });

    if (formAgregarHorario) {
        formAgregarHorario.addEventListener('submit', function (evento) {
            if (!document.querySelector('.horarios-dia-input:checked')) {
                evento.preventDefault();
                window.alert('Seleccioná al menos un día.');
            }
        });
    }

    // BOTÓN DUPLICAR
    const modalAgregar = document.getElementById('modalAgregarHorario');

    document.querySelectorAll('.btnDuplicarHorario').forEach(function (boton) {
        boton.addEventListener('click', function () {
            const datos = this.dataset;

            document.getElementById('sistemaAgregar').value = datos.sistema;
            document.getElementById('inicioAgregar').value = aHoraInput(datos.inicio);
            document.getElementById('finAgregar').value = aHoraInput(datos.fin);
            document.getElementById('cupoAgregar').value = datos.cupo || '';

            checkDias.forEach(function (check) {
                check.checked = check.value === datos.dia;
            });

            if (checkTodosDias) {
                checkTodosDias.checked = false;
            }

            if (modalAgregar && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(modalAgregar).show();
            }
        });
    });

    // MODAL EDITAR: cargar datos del horario
    const formEditarHorario = document.getElementById('formEditarHorario');
    const btnEliminarHorario = document.getElementById('btnEliminarHorario');

    document.querySelectorAll('.btnEditarHorario').forEach(function (boton) {
        boton.addEventListener('click', function () {
            const datos = this.dataset;

            formEditarHorario.action = '<?= base_url('actualizar_horario/') ?>' + datos.id;
            btnEliminarHorario.href = '<?= base_url('eliminar_horario/') ?>' + datos.id;

            document.getElementById('sistemaHorario').value = datos.sistema;
            document.getElementById('diaHorario').value = datos.dia;
            document.getElementById('inicioHorario').value = aHoraInput(datos.inicio);
            document.getElementById('finHorario').value = aHoraInput(datos.fin);
            document.getElementById('cupoHorario').value = datos.cupo || '';
        });
    });

    if (btnEliminarHorario) {
        btnEliminarHorario.addEventListener('click', function (evento) {
            if (! window.confirm('¿Desea eliminar este horario?')) {
                evento.preventDefault();
            }
        });
    }

    // COLOR DEL SISTEMA
    const esColorClaro = function (color) {
        if (!color || color.charAt(0) !== '#') return false;

        let hex = color.slice(1);

        if (hex.length === 3) {
            hex = hex.split('').map(function (caracter) {
                return caracter + caracter;
            }).join('');
        }

        if (!/^[0-9a-f]{6}$/i.test(hex)) return false;

        const rojo   = parseInt(hex.slice(0, 2), 16);
        const verde  = parseInt(hex.slice(2, 4), 16);
        const azul   = parseInt(hex.slice(4, 6), 16);
        const brillo = (0.299 * rojo + 0.587 * verde + 0.114 * azul) / 255;

        return brillo > 0.72;
    };

    const colorPickerGlobal = document.getElementById('colorPickerGlobal');
    let sistemaColorActivo = null;

    const botonesColor = document.querySelectorAll('.btnColorSistema');

    botonesColor.forEach(function (boton) {
        const color = boton.dataset.color || '#0b8f70';

        boton.style.backgroundColor = color;
        boton.classList.toggle('color-claro', esColorClaro(color));

        boton.addEventListener('click', function () {
            sistemaColorActivo = this.dataset.id;
            colorPickerGlobal.value = this.dataset.color || '#0b8f70';
            colorPickerGlobal.click();
        });
    });

    if (colorPickerGlobal) {
        colorPickerGlobal.addEventListener('change', function () {
            const color = this.value;
            const id = sistemaColorActivo;

            if (! id) return;

            const form = new FormData();
            form.append('color', color);

            fetch('<?= base_url('cambiar_color_sistema/') ?>' + id, {
                method: 'POST',
                body: form
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    document.querySelectorAll('.btnColorSistema[data-id="' + id + '"]').forEach(function (boton) {
                        boton.dataset.color = color;
                        boton.style.backgroundColor = color;
                        boton.classList.toggle('color-claro', esColorClaro(color));
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

    toasts.forEach(function (toast, indice) {
        setTimeout(function () {
            toast.classList.add('visible');
        }, 100);

        setTimeout(function () {
            toast.classList.remove('visible');
            toast.classList.add('saliendo');
            setTimeout(function () {
                toast.remove();
            }, 400);
        }, 2500 + (indice * 200));
    });
</script>
