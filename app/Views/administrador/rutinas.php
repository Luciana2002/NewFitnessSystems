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
            <h1 style="margin:0;">Rutinas</h1>

            <a href="<?= base_url('rutinas/nueva') ?>" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> Nueva rutina
            </a>
        </div>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="toast-custom success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="toast-custom error"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="table-responsive" style="background:#1b1b1b; padding:20px; border-left:5px solid #0b8f70;">
            <table class="table table-dark table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Rutina</th>
                        <th>Asignada a</th>
                        <th>Días</th>
                        <?php if(session()->get('id_rol') == 1): ?><th>Profesor</th><?php endif; ?>
                        <th>Ejercicios</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($rutinas)): ?>
                        <tr><td colspan="<?= session()->get('id_rol') == 1 ? 7 : 6 ?>" class="text-center">No hay rutinas cargadas.</td></tr>
                    <?php else: ?>
                        <?php foreach($rutinas as $rutina): ?>
                            <tr>
                                <td><?= formatear_fecha($rutina['fecha']) ?></td>
                                <td style="font-weight:600;"><?= esc($rutina['nombre']) ?></td>
                                <td>
                                    <?php if((int) $rutina['para_todos'] === 1): ?>
                                        <span class="badge bg-success">Todos los clientes</span>
                                    <?php else: ?>
                                        <?= esc($rutina['asignada_a']) ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($rutina['dias_texto'] ?: '-') ?></td>
                                <?php if(session()->get('id_rol') == 1): ?>
                                    <td><?= esc(trim(($rutina['profesor_apellido'] ?? '') . ', ' . ($rutina['profesor_nombre'] ?? ''), ', ')) ?></td>
                                <?php endif; ?>
                                <td><span class="badge bg-secondary"><?= (int) $rutina['cantidad_ejercicios'] ?></span></td>
                                <td style="white-space:nowrap;">
                                    <a href="<?= base_url('rutinas/ver/' . $rutina['id_rutina']) ?>" class="btn btn-sm btn-primary">Ver</a>
                                    <a href="<?= base_url('rutinas/editar/' . $rutina['id_rutina']) ?>" class="btn btn-sm btn-outline-light">Editar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
        document.addEventListener('keydown', function(e) {
            if (e.key === "Escape") { cerrarSidebar(); }
        });
    }

    const toasts = document.querySelectorAll('.toast-custom');
    toasts.forEach((toast, index) => {
        setTimeout(() => { toast.style.opacity = "1"; toast.style.transform = "translateY(0)"; }, 100);
        setTimeout(() => {
            toast.style.opacity = "0";
            toast.style.transform = "translateY(-20px)";
            setTimeout(() => toast.remove(), 400);
        }, 2800 + (index * 200));
    });
</script>
