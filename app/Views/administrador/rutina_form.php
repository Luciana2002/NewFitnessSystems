<?php
$esEdicion  = $esEdicion ?? false;
$rutina     = $rutina ?? [];
$diasSel    = array_map('intval', $diasSel ?? []);
$ejercicios = $ejercicios ?? [];
$accion     = $esEdicion
    ? base_url('rutinas/actualizar/' . ($rutina['id_rutina'] ?? ''))
    : base_url('rutinas/guardar');
$paraTodos  = ! $esEdicion || (int) ($rutina['para_todos'] ?? 1) === 1;
$clienteSel = $esEdicion && ! $paraTodos ? (int) ($rutina['id_cliente'] ?? 0) : 0;
?>

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

        <h1 style="margin-bottom:10px;"><?= $esEdicion ? 'Editar rutina' : 'Nueva rutina' ?></h1>
        <p style="color:#9a9a9a; margin-bottom:25px;">
            Cargá los ejercicios y elegí a quién se la asignás y qué días de la semana le corresponde.
        </p>

        <form action="<?= $accion ?>" method="post">

            <div class="dashboard-card" style="max-width:none; width:100%; padding:30px 35px; margin-bottom:30px;">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre de la rutina *</label>
                        <input type="text" name="nombre" class="form-control" required
                               value="<?= esc($rutina['nombre'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Descripción / indicaciones</label>
                        <input type="text" name="descripcion" class="form-control"
                               value="<?= esc($rutina['descripcion'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="dashboard-card" style="max-width:none; width:100%; padding:30px 35px; margin-bottom:30px;">
                <h2 style="font-size:20px; font-weight:800; margin-bottom:18px;">Asignación</h2>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label d-block">¿A quién?</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="asignacion" id="asigTodos" value="todos" <?= $paraTodos ? 'checked' : '' ?>>
                            <label class="form-check-label" for="asigTodos">Todos los clientes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="asignacion" id="asigCliente" value="cliente" <?= $paraTodos ? '' : 'checked' ?>>
                            <label class="form-check-label" for="asigCliente">Un cliente específico</label>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cliente</label>
                        <select name="id_cliente" id="selectorCliente" class="form-select" <?= $paraTodos ? 'disabled' : '' ?>>
                            <option value="">Seleccionar cliente…</option>
                            <?php foreach($clientes as $cli): ?>
                                <option value="<?= (int) $cli['id_persona'] ?>" <?= ((int) $cli['id_persona'] === $clienteSel) ? 'selected' : '' ?>>
                                    <?= esc($cli['apellido'] . ', ' . $cli['nombre']) ?> (<?= esc($cli['dni']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <hr style="border-color:#333;">

                <label class="form-label d-block" style="margin-bottom:10px;">Días de la semana *</label>
                <div style="display:flex; flex-wrap:wrap; gap:10px 22px;">
                    <?php foreach($dias as $num => $etiqueta): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="dias[]" value="<?= (int) $num ?>" id="dia<?= (int) $num ?>" <?= in_array((int) $num, $diasSel, true) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="dia<?= (int) $num ?>"><?= esc($etiqueta) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="dashboard-card" style="max-width:none; width:100%; padding:30px 35px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                    <h2 style="font-size:20px; font-weight:800; margin:0;">Ejercicios</h2>
                    <button type="button" id="btnAgregarEjercicio" class="btn btn-sm btn-success">
                        <i class="bi bi-plus-lg"></i> Agregar ejercicio
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark align-middle" id="tablaEjercicios">
                        <thead>
                            <tr>
                                <th style="min-width:180px;">Ejercicio</th>
                                <th style="width:90px;">Series</th>
                                <th style="width:120px;">Repeticiones</th>
                                <th style="width:100px;">Peso</th>
                                <th style="width:110px;">Descanso</th>
                                <th style="min-width:150px;">Observación</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoEjercicios">
                            <?php foreach($ejercicios as $ej): ?>
                                <tr>
                                    <td><input type="text" name="ejercicio_nombre[]" class="form-control" value="<?= esc($ej['nombre'] ?? '') ?>"></td>
                                    <td><input type="text" name="ejercicio_series[]" class="form-control" value="<?= esc($ej['series'] ?? '') ?>"></td>
                                    <td><input type="text" name="ejercicio_repeticiones[]" class="form-control" value="<?= esc($ej['repeticiones'] ?? '') ?>"></td>
                                    <td><input type="text" name="ejercicio_peso[]" class="form-control" value="<?= esc($ej['peso'] ?? '') ?>"></td>
                                    <td><input type="text" name="ejercicio_descanso[]" class="form-control" value="<?= esc($ej['descanso'] ?? '') ?>"></td>
                                    <td><input type="text" name="ejercicio_observacion[]" class="form-control" value="<?= esc($ej['observacion'] ?? '') ?>"></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger btnQuitarEjercicio">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="margin-top:25px; display:flex; gap:12px; align-items:center;">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-check-lg"></i> <?= $esEdicion ? 'Guardar cambios' : 'Guardar rutina' ?>
                </button>
                <a href="<?= base_url('rutinas') ?>" class="btn btn-outline-light btn-lg">Cancelar</a>
                <?php if($esEdicion): ?>
                    <a href="<?= base_url('rutinas/eliminar/' . $rutina['id_rutina']) ?>"
                       class="btn btn-outline-danger btn-lg"
                       style="margin-left:auto;"
                       onclick="return confirm('¿Seguro que querés eliminar esta rutina? Esta acción no se puede deshacer.');">
                        <i class="bi bi-trash"></i> Eliminar rutina
                    </a>
                <?php endif; ?>
            </div>
        </form>

    </section>

</main>

<template id="plantillaEjercicio">
    <tr>
        <td><input type="text" name="ejercicio_nombre[]" class="form-control"></td>
        <td><input type="text" name="ejercicio_series[]" class="form-control"></td>
        <td><input type="text" name="ejercicio_repeticiones[]" class="form-control"></td>
        <td><input type="text" name="ejercicio_peso[]" class="form-control"></td>
        <td><input type="text" name="ejercicio_descanso[]" class="form-control"></td>
        <td><input type="text" name="ejercicio_observacion[]" class="form-control"></td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger btnQuitarEjercicio">
                <i class="bi bi-x-lg"></i>
            </button>
        </td>
    </tr>
</template>

<script>
    const cuerpoEjercicios = document.getElementById('cuerpoEjercicios');
    const plantilla = document.getElementById('plantillaEjercicio');

    function agregarEjercicio() {
        var clon = plantilla.content.cloneNode(true);
        cuerpoEjercicios.appendChild(clon);
    }

    document.getElementById('btnAgregarEjercicio')?.addEventListener('click', agregarEjercicio);

    cuerpoEjercicios?.addEventListener('click', function (e) {
        var boton = e.target.closest('.btnQuitarEjercicio');
        if (boton) {
            boton.closest('tr').remove();
        }
    });

    if (cuerpoEjercicios.children.length === 0) {
        agregarEjercicio();
        agregarEjercicio();
        agregarEjercicio();
    }

    // Asignación
    var selectorCliente = document.getElementById('selectorCliente');
    var radios = document.querySelectorAll('input[name="asignacion"]');

    function actualizarAsignacion() {
        var valor = document.querySelector('input[name="asignacion"]:checked').value;
        var esCliente = valor === 'cliente';
        selectorCliente.disabled = !esCliente;
        selectorCliente.required = esCliente;
        if (!esCliente) {
            selectorCliente.value = '';
        }
    }

    radios.forEach(function (r) { r.addEventListener('change', actualizarAsignacion); });
    actualizarAsignacion();

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
        document.addEventListener('keydown', function(e) {
            if (e.key === "Escape") { cerrarSidebar(); }
        });
    }
</script>
