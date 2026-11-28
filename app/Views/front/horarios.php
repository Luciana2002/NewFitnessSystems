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
    'Lunes'     => 'Lunes',
    'Martes'    => 'Martes',
    'Miercoles' => 'Miércoles',
    'Jueves'    => 'Jueves',
    'Viernes'   => 'Viernes',
    'Sabado'    => 'Sábado',
    'Domingo'   => 'Domingo',
];

$paletaSistemas = [
    1 => '#e52421', // BodyPump
    2 => '#f5c518', // PowerJump
    3 => '#1e88e5', // Funcional
    4 => '#76c043', // Zumba
    5 => '#9c27b0', // Artes Marciales
    6 => '#0b8f70', // Gimnasio
];

$paletaGeneral = ['#e65c7a', '#4e9cff', '#2abf88', '#f2a33c', '#9b6bff', '#d44f6a', '#00b8c9', '#8fce00'];

$diasPresentes = [];
$celdas        = [];
$horasReales   = [];
$disciplinas   = [];
$colorSistema  = [];
$indicePaleta  = 0;

foreach ($horarios as $horario) {
    $dia        = $horario['dia_semana'];
    $horaInicio = formatear_hora($horario['hora_inicio']);
    $horaFin    = formatear_hora($horario['hora_fin']);
    $idSistema  = (int) $horario['id_sistema'];

    if (! isset($colorSistema[$idSistema])) {
        $colorSistema[$idSistema] = $horario['color']
            ?: ($paletaSistemas[$idSistema] ?? $paletaGeneral[$indicePaleta % count($paletaGeneral)]);
        $indicePaleta++;
    }

    $color = $colorSistema[$idSistema];

    $diasPresentes[$dia] = true;
    $horasReales[$horaInicio] = true;

    // Varias clases pueden compartir la misma hora y día: se acumulan en un array.
    $celdas[$horaInicio][$dia][] = [
        'id_sistema' => $idSistema,
        'nombre'     => $horario['nombre_sistema'],
        'inicio'     => $horaInicio,
        'fin'        => $horaFin,
        'color'      => $color,
    ];

    $disciplinas[$idSistema] = [
        'id'     => $idSistema,
        'nombre' => $horario['nombre_sistema'],
        'color'  => $color,
    ];
}

$dias = array_values(array_filter($ordenDias, static function ($dia) use ($diasPresentes) {
    return isset($diasPresentes[$dia]);
}));

if (empty($dias)) {
    $dias = array_slice($ordenDias, 0, 5);
}

$horasReales = array_keys($horasReales);
usort($horasReales, static function ($a, $b) {
    return strtotime($a) <=> strtotime($b);
});

$horas = [];

if (! empty($horasReales)) {
    $inicioGrilla = strtotime(min($horasReales));
    $finGrilla    = strtotime(max($horasReales));
    $slots        = [];

    for ($t = $inicioGrilla; $t <= $finGrilla; $t += 1800) {
        $slots[date('H:i', $t)] = true;
    }

    // Incluye horas reales que no caen exactamente en el slot de 30 minutos.
    foreach ($horasReales as $hora) {
        $slots[$hora] = true;
    }

    $horas = array_keys($slots);
    usort($horas, static function ($a, $b) {
        return strtotime($a) <=> strtotime($b);
    });
}

$disciplinas = array_values($disciplinas);
usort($disciplinas, static function ($a, $b) {
    return strcasecmp($a['nombre'], $b['nombre']);
});
?>

<section class="schedule-section">
    <div class="container">

        <div class="schedule-filtros" role="group" aria-label="Filtrar clases por disciplina">
            <button type="button" class="schedule-filtro activa" data-sistema="todas">
                Todas
            </button>

            <?php foreach ($disciplinas as $disciplina): ?>
                <button type="button" class="schedule-filtro"
                        data-sistema="<?= (int) $disciplina['id'] ?>"
                        data-color="<?= esc($disciplina['color']) ?>">
                    <span class="schedule-filtro-dot"></span>
                    <?= esc($disciplina['nombre']) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="schedule-table-wrapper">
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <?php foreach ($dias as $dia): ?>
                            <th><?= esc($nombresDias[$dia] ?? $dia) ?></th>
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
                                if (! empty($celdas[$hora][$dia])) {
                                    $hayClase = true;
                                    break;
                                }
                            }

                            if (! $hayClase) {
                                continue;
                            }
                        ?>
                        <tr>
                            <td><?= esc($hora) ?></td>

                            <?php foreach ($dias as $dia): ?>
                                <td>
                                    <?php if (! empty($celdas[$hora][$dia])): ?>
                                        <?php foreach ($celdas[$hora][$dia] as $clase): ?>
                                            <span class="schedule-clase"
                                                  data-id-sistema="<?= (int) $clase['id_sistema'] ?>"
                                                  data-color="<?= esc($clase['color']) ?>">
                                                <span class="schedule-clase-nombre">
                                                    <?= esc($clase['nombre']) ?>
                                                </span>
                                                <span class="schedule-clase-rango">
                                                    <?= esc($clase['inicio']) ?> - <?= esc($clase['fin']) ?>
                                                </span>
                                            </span>
                                        <?php endforeach; ?>
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

<script>
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

    const elementosConColor = document.querySelectorAll('[data-color]');

    elementosConColor.forEach(function (elemento) {
        const color = elemento.dataset.color || '#0b8f70';

        elemento.style.setProperty('--schedule-color', color);
        elemento.classList.toggle('color-claro', esColorClaro(color));
    });

    const filtrosDisciplina = document.querySelectorAll('.schedule-filtro');
    const clasesHorario = document.querySelectorAll('.schedule-clase');
    const filasHorario = document.querySelectorAll('.schedule-table tbody tr');

    filtrosDisciplina.forEach(function (filtro) {
        filtro.addEventListener('click', function () {
            const sistema = this.dataset.sistema;

            filtrosDisciplina.forEach(function (otro) {
                otro.classList.toggle('activa', otro === filtro);
            });

            clasesHorario.forEach(function (clase) {
                const mostrar = sistema === 'todas' || clase.dataset.idSistema === sistema;
                clase.classList.toggle('oculto', !mostrar);
            });

            filasHorario.forEach(function (fila) {
                if (! fila.querySelector('.schedule-clase')) {
                    return;
                }

                const visibles = fila.querySelectorAll('.schedule-clase:not(.oculto)');
                fila.classList.toggle('oculto', visibles.length === 0);
            });
        });
    });
</script>
