<main class="dashboard-page" style="align-items:flex-start;">

    <button type="button" id="sidebarOpen" class="sidebar-handle">
        <span>›</span>
    </button>

    <button type="button" id="sidebarClose" class="sidebar-close">
        ✕
    </button>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <?= view('layout/sidebar') ?>

    <section class="dashboard-content" style="padding:50px 70px;">
        <div style="display:flex; align-items:center; gap:15px; margin-bottom:30px;">
            <a href="<?= base_url('clientes') ?>" class="btn btn-outline-success btn-sm">← Volver</a>
            <h1 style="margin:0;">Editar Cliente</h1>
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

        <form action="<?= base_url('actualizar_cliente/'.$cliente['id_persona']) ?>" method="post"
              style="background:#1b1b1b; padding:35px 40px; border-left:5px solid #0b8f70; max-width:1100px;">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:22px 30px;">

                <div>
                    <label style="color:white;">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?= esc($cliente['nombre']) ?>">
                </div>

                <div>
                    <label style="color:white;">Apellido</label>
                    <input type="text" name="apellido" class="form-control" value="<?= esc($cliente['apellido']) ?>">
                </div>

                <div style="grid-column:1 / 3;">
                    <label style="color:white;">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= esc($cliente['email']) ?>">
                </div>

                <div>
                    <label style="color:white;">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="<?= esc($cliente['telefono']) ?>">
                </div>

                <div>
                    <label style="color:white;">DNI</label>
                    <input type="text" name="dni" class="form-control" value="<?= esc($cliente['dni']) ?>">
                </div>

                <?php if(session()->get('id_rol') == 1): ?>
                    <div>
                        <label style="color:white;">Rol</label>
                        <select name="id_rol" class="form-control">
                            <option value="1" <?= ($cliente['id_rol'] ?? '') == 1 ? 'selected' : '' ?>>Administrador</option>
                            <option value="2" <?= ($cliente['id_rol'] ?? '') == 2 ? 'selected' : '' ?>>Profesor</option>
                            <option value="3" <?= ($cliente['id_rol'] ?? '') == 3 ? 'selected' : '' ?>>Cliente</option>
                        </select>
                    </div>
                <?php else: ?>
                    <?php
                        $rolActual = (int) ($cliente['id_rol'] ?? 3);

                        if ($rolActual == 1) {
                            $nombreRol = 'Administrador';
                        } elseif ($rolActual == 2) {
                            $nombreRol = 'Profesor';
                        } else {
                            $nombreRol = 'Cliente';
                        }
                    ?>
                    <div>
                        <label style="color:white;">Rol</label>
                        <input type="text" class="form-control" value="<?= esc($nombreRol) ?>" disabled>
                    </div>
                <?php endif; ?>

            </div>

            <?php if(!empty($usuario)): ?>
                <h4 style="margin:30px 0 20px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:16px;">
                    Datos del usuario
                </h4>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:22px 30px;">
                    <div>
                        <label style="color:white;">Nombre de usuario</label>
                        <input type="text" name="nombre_usuario" class="form-control" value="<?= esc($usuario['nombre_usuario']) ?>">
                    </div>

                    <div>
                        <label style="color:white;">Contraseña (dejá vacío para mantener la actual)</label>
                        <input type="password" name="pass" class="form-control" placeholder="••••••••">
                    </div>
                </div>
            <?php else: ?>
                <p style="color:#cfcfcf; margin-top:25px;">
                    Esta persona no tiene cuenta de usuario. Podés registrarla desde el panel.
                </p>
            <?php endif; ?>

            <?php
                $sistemasActuales = $sistemas ?? [];
                $idsActuales = array_map(function ($s) {
                    return (int) $s['id_sistema'];
                }, $sistemasActuales);
            ?>

            <h4 style="margin:30px 0 20px; text-transform:uppercase; letter-spacing:1px; color:#0b8f70; font-size:16px;">
                Sistemas
            </h4>

            <?php if(!empty($sistemasActuales)): ?>
                <div class="table-responsive">
                    <table class="table table-dark table-sm align-middle mb-3">
                        <thead>
                            <tr>
                                <th>Sistema</th>
                                <th>Inscripción</th>
                                <th>Vencimiento</th>
                                <th>Estado</th>
                                <th>Activo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($sistemasActuales as $sistema): ?>
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
                                    <td><?= formatear_fecha($sistema['fecha_vencimiento']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $susBadge ?>"><?= esc($estadoSus) ?></span>
                                    </td>
                                    <td class="text-start">
                                        <label class="switch" title="Encendido: activo — Apagado: dado de baja">
                                            <input type="checkbox" name="sistema_activo[]"
                                                   value="<?= $sistema['id_inscripcion'] ?>"
                                                   <?= $estadoSus === 'Cancelada' ? '' : 'checked' ?>>
                                            <span class="slider"></span>
                                        </label>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="color:#cfcfcf;">Este cliente no tiene sistemas inscriptos.</p>
            <?php endif; ?>

            <div style="max-width:400px;">
                <label style="color:white;">Agregar sistema</label>
                <select name="agregar_sistema" class="form-control">
                    <option value="">— Seleccionar sistema —</option>
                    <?php foreach($sistemasDisponibles as $sistema): ?>
                        <?php if(!in_array((int) $sistema['id_sistema'], $idsActuales, true)): ?>
                            <option value="<?= $sistema['id_sistema'] ?>">
                                <?= esc($sistema['nombre_sistema']) ?> — <?= formatear_monto($sistema['precio']) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-top:30px;">
                <div style="display:flex; gap:30px; flex-wrap:wrap; align-items:center;">
                    <button type="submit" class="btn-main">Guardar cambios</button>
                    <a href="<?= base_url('clientes') ?>" class="btn-main" style="background:#555;">Cancelar</a>
                </div>

                <div style="margin-top:22px; padding-top:20px; border-top:1px solid #333;">
                    <label style="color:white; display:block; margin-bottom:8px;">Estado de la cuenta</label>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <label class="switch" title="Encendido: activo — Apagado: dado de baja">
                            <input type="checkbox" name="persona_activa" value="1" id="personaActiva"
                                   <?= ($cliente['baja'] ?? 'N') != 'S' ? 'checked' : '' ?>>
                            <span class="slider"></span>
                        </label>
                        <span id="estadoPersona" style="color:#cfcfcf;">
                            <?= ($cliente['baja'] ?? 'N') == 'S' ? 'Dado de baja' : 'Activo' ?>
                        </span>
                    </div>
                </div>
            </div>

        </form>
    </section>

</main>

<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        vertical-align: middle;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .switch .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #555;
        border-radius: 24px;
        transition: 0.3s;
    }

    .switch .slider::before {
        content: "";
        position: absolute;
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: #fff;
        border-radius: 50%;
        transition: 0.3s;
    }

    .switch input:checked + .slider {
        background-color: #dc3545;
    }

    .switch input:checked + .slider::before {
        transform: translateX(20px);
    }
</style>

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

    const personaActiva = document.getElementById('personaActiva');
    const estadoPersona = document.getElementById('estadoPersona');

    if (personaActiva && estadoPersona) {
        personaActiva.addEventListener('change', function () {
            estadoPersona.textContent = this.checked ? 'Activo' : 'Dado de baja';
        });
    }
</script>
