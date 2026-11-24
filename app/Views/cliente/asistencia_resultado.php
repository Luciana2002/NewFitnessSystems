<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resultado del ingreso</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="asistencia-resultado">

<?php
    $estado = $estado ?? 'error';

    $presentacion = [
        'ok'        => ['clase' => 'ok',       'icono' => 'bi-check-circle-fill',        'titulo' => 'Ingreso confirmado'],
        'duplicado' => ['clase' => 'duplicado','icono' => 'bi-clock-history',            'titulo' => 'Ingreso ya registrado'],
        'vencida'   => ['clase' => 'vencida',  'icono' => 'bi-exclamation-octagon-fill', 'titulo' => 'Cuota vencida'],
        'invalido'  => ['clase' => 'vencida',  'icono' => 'bi-qr-code',                  'titulo' => 'QR inválido'],
        'error'     => ['clase' => 'vencida',  'icono' => 'bi-x-octagon-fill',           'titulo' => 'No se pudo registrar'],
    ];

    $vista = $presentacion[$estado] ?? $presentacion['error'];
?>

<main class="asistencia-pantalla <?= esc($vista['clase']) ?>">

    <div class="asistencia-icono">
        <i class="bi <?= esc($vista['icono']) ?>"></i>
    </div>

    <h1 class="asistencia-titulo"><?= esc($vista['titulo']) ?></h1>

    <?php if (! empty($nombre)): ?>
        <p class="asistencia-nombre"><?= esc($nombre) ?></p>
    <?php endif; ?>

    <p class="asistencia-mensaje"><?= esc($mensaje ?? '') ?></p>

    <?php if (! empty($vencimiento)): ?>
        <?php
            $dias = $dias ?? null;
            $detalleVencimiento = 'Vence: ' . date('d/m/Y', strtotime($vencimiento));

            if ($dias !== null && (int) $dias < 0) {
                $detalleVencimiento = 'Venció el ' . date('d/m/Y', strtotime($vencimiento))
                    . ' (hace ' . abs((int) $dias) . ' días)';
            }
        ?>
        <div class="asistencia-dato">
            <span>Situación de la cuota</span>
            <strong><?= esc($detalleVencimiento) ?></strong>
        </div>
    <?php endif; ?>

    <?php if (! empty($disciplinas) && is_array($disciplinas)): ?>
        <div class="asistencia-dato">
            <span>Disciplinas vigentes</span>
            <strong><?= esc(implode(', ', $disciplinas)) ?></strong>
        </div>
    <?php endif; ?>

    <div class="asistencia-dato">
        <span>Fecha y hora</span>
        <strong>
            <?= ! empty($fecha) ? esc(date('d/m/Y', strtotime($fecha))) : esc(date('d/m/Y')) ?>
            &nbsp;·&nbsp;
            <?= esc($hora ?? date('H:i:s')) ?>
        </strong>
    </div>

    <div class="asistencia-acciones">
        <a class="nfs-btn nfs-btn-light asistencia-volver" href="<?= base_url('usuario_logueado') ?>">
            <i class="bi bi-house-door"></i> Volver a mi panel
        </a>
        <a class="nfs-btn nfs-btn-outline asistencia-volver" href="<?= base_url('cliente/asistencias') ?>">
            <i class="bi bi-clock-history"></i> Ver mi historial de asistencias
        </a>
    </div>

</main>

</body>
</html>
