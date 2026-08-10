<main>

<section class="page-hero">
    <div class="container">
        <span class="section-label">Clases grupales</span>
        <h1>Horarios</h1>
        <p>Elegí el día y la clase que mejor se adapte a tu rutina.</p>
    </div>
</section>

<?php
$horarios = $horarios ?? [];

$ordenDias = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];

$nombresDias = [
    'Lunes'    => 'Lunes',
    'Martes'   => 'Martes',
    'Miercoles'=> 'Miércoles',
    'Jueves'   => 'Jueves',
    'Viernes'  => 'Viernes',
    'Sabado'   => 'Sábado',
    'Domingo'  => 'Domingo',
];

$paletaSistemas = [
    1 => '#d63b4c', // BodyPump
    2 => '#d98b45', // PowerJump
    3 => '#006c9c', // Funcional
    4 => '#7b3fc9', // Zumba
    5 => '#c92f45', // Artes Marciales
    6 => '#0b8f70', // Gimnasio
];

$paletaGeneral = ['#e65c7a', '#4e9cff', '#2abf88', '#f2a33c', '#9b6bff', '#d44f6a', '#00b8c9', '#8fce00'];

$diasPresentes = [];
$celdas = [];
$horas = [];

foreach ($horarios as $h) {
    $dia = $h['dia_semana'];
    $hora = formatear_hora($h['hora_inicio']);

    $diasPresentes[$dia] = true;
    $celdas[$hora . '|' . $dia] = $h;
    $horas[$hora] = true;
}

$dias = array_values(array_filter($ordenDias, fn($d) => isset($diasPresentes[$d])));

if (empty($dias)) {
    $dias = array_slice($ordenDias, 0, 5);
}

$horas = array_keys($horas);
usort($horas, fn($a, $b) => strtotime($a) <=> strtotime($b));

if (!empty($horas)) {
    $inicioGrilla = strtotime('08:30');
    $finGrilla = strtotime(end($horas));

    $horas = [];

    for ($t = $inicioGrilla; $t <= $finGrilla; $t += 1800) {
        $horas[] = date('H:i', $t);
    }
}

$colorSistema = [];
$indicePaleta = 0;

foreach ($horarios as $h) {
    $id = $h['id_sistema'];

    if (isset($colorSistema[$id])) {
        continue;
    }

    $colorSistema[$id] = $h['color'] ?? ($paletaSistemas[$id] ?? $paletaGeneral[$indicePaleta % count($paletaGeneral)]);
    $indicePaleta++;
}
?>

<section class="schedule-section">
    <div class="container">

        <div class="schedule-table-wrapper">
            <table class="schedule-table">

                <thead>
                    <tr>
                        <th>Hora</th>
                        <?php foreach ($dias as $dia): ?>
                            <th><?= $nombresDias[$dia] ?? $dia ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($horas)): ?>
                        <tr>
                            <td colspan="<?= count($dias) + 1 ?>" class="text-center">
                                No hay horarios disponibles.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($horas as $hora): ?>
                        <?php
                        $hayClase = false;

                        foreach ($dias as $dia) {
                            if (isset($celdas[$hora . '|' . $dia])) {
                                $hayClase = true;
                                break;
                            }
                        }

                        if (!$hayClase) {
                            continue;
                        }
                        ?>
                        <tr>

                            <td><?= $hora ?></td>

                            <?php foreach ($dias as $dia): ?>
                                <?php $clase = $celdas[$hora . '|' . $dia] ?? null; ?>
                                <td>
                                    <?php if ($clase): ?>
                                        <span style="color:<?= $clase['color'] ?? ($colorSistema[$clase['id_sistema']] ?? '#ccc') ?>; font-weight:900;">
                                            <?= esc($clase['nombre_sistema']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>

                        </tr>
                    <?php endforeach; ?>

                </tbody>

            </table>
        </div>

        <div class="schedule-note">
            <p>Los horarios pueden modificarse según disponibilidad.</p>
        </div>

    </div>
</section>

</main>