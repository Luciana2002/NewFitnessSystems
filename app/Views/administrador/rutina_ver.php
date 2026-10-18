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
            <h1 style="margin:0;"><?= esc($rutina['nombre']) ?></h1>
            <div style="display:flex; gap:10px;">
                <a href="<?= base_url('rutinas/editar/' . $rutina['id_rutina']) ?>" class="btn btn-success">
                    <i class="bi bi-pencil-square"></i> Editar
                </a>
                <a href="<?= base_url('rutinas') ?>" class="btn btn-outline-light">Volver</a>
            </div>
        </div>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="toast-custom success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <p style="color:#9a9a9a; margin-bottom:25px;">
            Asignada a:
            <?php if((int) $rutina['para_todos'] === 1): ?>
                <span class="badge bg-success">Todos los clientes</span>
            <?php else: ?>
                <strong style="color:#fff;"><?= esc(trim(($rutina['cliente_apellido'] ?? '') . ', ' . ($rutina['cliente_nombre'] ?? ''), ', ')) ?></strong>
            <?php endif; ?>
            &nbsp;|&nbsp; Días: <strong style="color:#fff;"><?= esc($dias_texto ?: '-') ?></strong>
            &nbsp;|&nbsp; Fecha: <?= formatear_fecha($rutina['fecha']) ?>
        </p>

        <?php if(!empty($rutina['descripcion'])): ?>
            <div class="dashboard-card" style="max-width:none; width:100%; margin-bottom:30px;">
                <?= nl2br(esc($rutina['descripcion'])) ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive" style="background:#1b1b1b; padding:20px; border-left:5px solid #0b8f70;">
            <table class="table table-dark table-striped align-middle">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Ejercicio</th>
                        <th>Series</th>
                        <th>Repeticiones</th>
                        <th>Peso</th>
                        <th>Descanso</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($ejercicios)): ?>
                        <tr><td colspan="7" class="text-center">Sin ejercicios cargados.</td></tr>
                    <?php else: ?>
                        <?php foreach($ejercicios as $i => $ej): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td style="font-weight:600;"><?= esc($ej['nombre']) ?></td>
                                <td><?= esc($ej['series'] ?? '-') ?></td>
                                <td><?= esc($ej['repeticiones'] ?? '-') ?></td>
                                <td><?= esc($ej['peso'] ?? '-') ?></td>
                                <td><?= esc($ej['descanso'] ?? '-') ?></td>
                                <td><?= esc($ej['observacion'] ?? '-') ?></td>
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
