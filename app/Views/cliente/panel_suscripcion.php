<!-- DEBUG-CLIENTE-PANEL-SUSCRIPCION-20251021 --><main class="dashboard-page">

    <button type="button" id="sidebarOpen" class="sidebar-handle">
        <span>Ã¢â¬Âº</span>
    </button>

    <button type="button" id="sidebarClose" class="sidebar-close">
        Ã¢Åâ¢
    </button>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <?= view('layout/sidebar') ?>

    <section class="dashboard-content" style="padding:50px 70px;">

        <h1 style="margin-bottom:25px;">Mi suscripción</h1>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="toast-custom success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="toast-custom error"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <?php
        $estado   = $membresia['estado'];
        $activo   = $membresia['activo'];
        $dias     = $membresia['dias'];
        $vence    = $membresia['vencimiento'];

        if ($estado === 'sin_suscripcion') {
            $badgeClass = 'badge bg-secondary';
            $badgeTxt   = 'Sin suscripción';
            $resumen    = 'Todavía no tenés ningún sistema activo. ConsultÃ¡ en recepción.';
        } elseif ($activo) {
            $badgeClass = $dias <= 5 ? 'badge bg-warning text-dark' : 'badge bg-success';
            $badgeTxt   = $dias <= 5 ? 'Por vencer' : 'Al día';
            $resumen    = $dias === 0
                ? 'Tu cuota vence hoy (' . formatear_fecha($vence) . ').'
                : 'Tu cuota vence en ' . $dias . ' día' . ($dias == 1 ? '' : 's') . ' (' . formatear_fecha($vence) . ').';
        } else {
            $badgeClass = 'badge bg-danger';
            $badgeTxt   = 'Vencida';
            $resumen    = 'Tu cuota venció hace ' . abs((int) $dias) . ' día' . (abs((int) $dias) == 1 ? '' : 's') . ' (' . formatear_fecha($vence) . ').';
        }
        ?>

        <div class="dashboard-card" style="max-width:none; width:100%; padding:30px 35px; margin-bottom:30px;">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap;">
                <div>
                    <div class="menu-section-title" style="margin-bottom:6px;">Estado de tu cuota</div>
                    <div style="font-size:22px; font-weight:800; margin-bottom:8px;"><?= $resumen ?></div>
                    <?php if($vence): ?>
                        <div style="color:#9a9a9a; font-size:14px;">Vencimiento: <?= formatear_fecha($vence) ?></div>
                    <?php endif; ?>
                </div>
                <span class="<?= $badgeClass ?>" style="font-size:16px; padding:12px 22px;"><?= $badgeTxt ?></span>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:20px; margin-bottom:40px;">
            <?php if(empty($suscripciones)): ?>
                <div class="dashboard-card" style="max-width:none;">No hay sistemas registrados.</div>
            <?php else: ?>
                <?php foreach($suscripciones as $sus): ?>
                    <?php
                    $esCancelada = ($sus['estado_suscripcion'] === 'Cancelada');
                    $vencida = !$esCancelada && !empty($sus['fecha_vencimiento']) && strtotime((string) $sus['fecha_vencimiento']) < strtotime(date('Y-m-d'));
                    ?>
                    <div class="dashboard-card" style="max-width:none;">
                        <div style="font-size:19px; font-weight:800; margin-bottom:10px;"><?= esc($sus['nombre_sistema']) ?></div>
                        <p style="margin-bottom:6px;"><strong>Estado:</strong>
                            <?php if($esCancelada): ?>
                                <span class="badge bg-secondary">Cancelada</span>
                            <?php elseif($vencida): ?>
                                <span class="badge bg-danger">Vencida</span>
                            <?php else: ?>
                                <span class="badge bg-success">Activa</span>
                            <?php endif; ?>
                        </p>
                        <p style="margin-bottom:6px;"><strong>Inicio:</strong> <?= formatear_fecha($sus['fecha_inicio']) ?></p>
                        <p style="margin-bottom:6px;"><strong>Vencimiento:</strong> <?= formatear_fecha($sus['fecha_vencimiento']) ?></p>
                        <p style="margin-bottom:10px;"><strong>Precio:</strong> <?= formatear_monto($sus['precio_mensualidad']) ?></p>
                        <?php if(!$esCancelada): ?>
                            <button type="button" class="nfs-btn nfs-btn-outline-danger nfs-btn-sm btn-baja"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalBaja"
                                    data-id-inscripcion="<?= (int) $sus['id_inscripcion'] ?>"
                                    data-sistema="<?= esc($sus['nombre_sistema']) ?>">
                                <i class="bi bi-x-circle"></i> Darse de baja
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <h2 style="font-size:26px; font-weight:800; margin-bottom:18px;">Historial de pagos</h2>

        <div class="table-responsive" style="background:#1b1b1b; padding:20px; border-left:5px solid #0b8f70; margin-bottom:40px;">
            <table class="table table-dark table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Sistema</th>
                        <th>Monto</th>
                        <th>Medio</th>
                        <th>Cobrado por</th>
                        <th>Comprobante</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($pagos)): ?>
                        <tr><td colspan="6" class="text-center">No hay pagos registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach($pagos as $pago): ?>
                            <tr>
                                <td><?= formatear_fecha($pago['fecha_pago']) ?></td>
                                <td><?= esc($pago['nombre_sistema']) ?></td>
                                <td><?= formatear_monto($pago['monto']) ?></td>
                                <td><?= esc($pago['medio_pago']) ?></td>
                                <td><?= esc($pago['cobrado_por']) ?></td>
                                <td>
                                    <a class="btn btn-sm btn-success"
                                       href="<?= base_url('cliente/comprobante/' . $pago['id_pago']) ?>"
                                       target="_blank">
                                        <i class="bi bi-file-earmark-pdf"></i> PDF
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <h2 style="font-size:26px; font-weight:800; margin-bottom:18px;">Mis asistencias</h2>

        <div class="table-responsive" style="background:#1b1b1b; padding:20px; border-left:5px solid #0b8f70;">
            <table class="table table-dark table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Registrado por</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($asistencias)): ?>
                        <tr><td colspan="3" class="text-center">Todavía no registraste ingresos.</td></tr>
                    <?php else: ?>
                        <?php foreach($asistencias as $asis): ?>
                            <tr>
                                <td><?= formatear_fecha($asis['fecha']) ?></td>
                                <td><?= formatear_hora($asis['hora']) ?></td>
                                <td><?= esc($asis['registrado_por'] ?? '-') ?></td>
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

    var modalBaja = document.getElementById('modalBaja');
    if (modalBaja) {
        modalBaja.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var idInscripcion = button.getAttribute('data-id-inscripcion');
            var sistema = button.getAttribute('data-sistema') || '';
            document.getElementById('modalBajaSistema').textContent = sistema;
            var form = document.getElementById('formBaja');
            form.action = '<?= base_url('cliente/baja_sistema/') ?>' + idInscripcion;
            document.getElementById('motivoBaja').value = '';
        });
    }
</script>

<div class="modal fade" id="modalBaja" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background:#1b1b1b; color:#fff; border:1px solid #262626;">
      <div class="modal-header" style="border-bottom:1px solid #262626;">
        <h5 class="modal-title">Dar de baja suscripción</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="formBaja" method="post" action="#">
        <div class="modal-body">
          <p style="margin-bottom:12px;">¿Estás seguro de darte de baja?</p>
          <p style="margin-bottom:10px; color:#b0b0b0; font-size:14px;">Por favor, contanos el motivo de esta decisión para mejorar nuestro servicio.</p>
          <label for="motivoBaja" class="form-label">Motivo (opcional)</label>
          <textarea class="form-control" id="motivoBaja" name="motivo" rows="4" style="background:#111; color:#fff; border:1px solid #262626;" placeholder="Ej.: Horarios, precio, mudanza, etc."></textarea>
        </div>
        <div class="modal-footer" style="border-top:1px solid #262626;">
          <button type="button" class="nfs-btn nfs-btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="nfs-btn nfs-btn-danger">Sí, dar de baja</button>
        </div>
      </form>
    </div>
  </div>
</div>






<h2 style="font-size:26px; font-weight:800; margin-bottom:18px;">Preguntas frecuentes</h2>
<div class="dashboard-card" style="max-width:none; padding:30px 35px; margin-bottom:60px;">
  <div class="accordion" id="faqAccordion" style="--bs-accordion-bg:#1b1b1b; --bs-accordion-color:#fff; --bs-accordion-border-color:#262626;">
    <div class="accordion-item" style="background:#1b1b1b; color:#fff; border:1px solid #262626;">
      <h2 class="accordion-header">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" style="background:#1b1b1b; color:#fff;">
          ¿Cómo puedo darme de baja de un sistema?
        </button>
      </h2>
      <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
        <div class="accordion-body" style="background:#161616;">
          Desde "Mi suscripci?nos a mejorar.
        </div>
      </div>
    </div>
    <div class="accordion-item" style="background:#1b1b1b; color:#fff; border:1px solid #262626;">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" style="background:#1b1b1b; color:#fff;">
          ¿Cuándo vence mi cuota?
        </button>
      </h2>
      <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
        <div class="accordion-body" style="background:#161616;">
          Podés ver el estado y la fecha de vencimiento en el resumen de "Mi suscripción".
        </div>
      </div>
    </div>
    <div class="accordion-item" style="background:#1b1b1b; color:#fff; border:1px solid #262626;">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" style="background:#1b1b1b; color:#fff;">
          ¿Dónde veo mi QR para ingresar?
        </button>
      </h2>
      <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
        <div class="accordion-body" style="background:#161616;">
          En el menú lateral tenés "Mi QR", ahí aparece tu código para el check-in.
        </div>
      </div>
    </div>
    <div class="accordion-item" style="background:#1b1b1b; color:#fff; border:1px solid #262626;">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" style="background:#1b1b1b; color:#fff;">
          ¿Puedo ver mis rutinas?
        </button>
      </h2>
      <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
        <div class="accordion-body" style="background:#161616;">
          Sí, desde "Mis rutinas" podés ver los ejercicios asignados por día.
        </div>
      </div>
    </div>
  </div>
</div>

