<main>

<section class="page-hero">
    <div class="container">
        <span class="section-label">Clases grupales</span>
        <h1>Horarios</h1>
        <p>Elegí el día y la clase que mejor se adapte a tu rutina.</p>
    </div>
</section>

<?php
$dias = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes"];

$horarios = [
    "8:30" => [
        "Lunes" => ["Body Pump", "#d63b4c"],
        "Martes" => ["Body Pump", "#d63b4c"],
        "Miércoles" => ["", ""],
        "Jueves" => ["Body Pump", "#d63b4c"],
        "Viernes" => ["Body Pump", "#d63b4c"],
    ],
    "10:00" => [
        "Lunes" => ["Power Jump", "#d98b45"],
        "Martes" => ["Power Jump", "#d98b45"],
        "Miércoles" => ["", ""],
        "Jueves" => ["Power Jump", "#d98b45"],
        "Viernes" => ["Power Jump", "#d98b45"],
    ],
    "11:30" => [
        "Lunes" => ["", ""],
        "Martes" => ["", ""],
        "Miércoles" => ["", ""],
        "Jueves" => ["", ""],
        "Viernes" => ["", ""],
    ],
    "12:30" => [
        "Lunes" => ["Funcional", "#006c9c"],
        "Martes" => ["Funcional", "#006c9c"],
        "Miércoles" => ["Funcional", "#006c9c"],
        "Jueves" => ["Funcional", "#006c9c"],
        "Viernes" => ["Funcional", "#006c9c"],
    ],
    "14:00" => [
        "Lunes" => ["Power Jump", "#d98b45"],
        "Martes" => ["Power Jump", "#d98b45"],
        "Miércoles" => ["", ""],
        "Jueves" => ["Power Jump", "#d98b45"],
        "Viernes" => ["Power Jump", "#d98b45"],
    ],
    "15:00" => [
        "Lunes" => ["Power Jump", "#d98b45"],
        "Martes" => ["Power Jump", "#d98b45"],
        "Miércoles" => ["", ""],
        "Jueves" => ["Power Jump", "#d98b45"],
        "Viernes" => ["Power Jump", "#d98b45"],
    ],
    "16:00" => [
        "Lunes" => ["Body Pump", "#d63b4c"],
        "Martes" => ["Body Pump", "#d63b4c"],
        "Miércoles" => ["", ""],
        "Jueves" => ["Body Pump", "#d63b4c"],
        "Viernes" => ["Body Pump", "#d63b4c"],
    ],
    "18:00" => [
        "Lunes" => ["Funcional", "#006c9c"],
        "Martes" => ["Funcional", "#006c9c"],
        "Miércoles" => ["Funcional", "#006c9c"],
        "Jueves" => ["Funcional", "#006c9c"],
        "Viernes" => ["Funcional", "#006c9c"],
    ],
    "19:00" => [
        "Lunes" => ["Artes marciales", "#c92f45"],
        "Martes" => ["", ""],
        "Miércoles" => ["Artes marciales", "#c92f45"],
        "Jueves" => ["", ""],
        "Viernes" => ["Artes marciales", "#c92f45"],
    ],
    "20:00" => [
        "Lunes" => ["", ""],
        "Martes" => ["", ""],
        "Miércoles" => ["Power Jump", "#d98b45"],
        "Jueves" => ["", ""],
        "Viernes" => ["", ""],
    ],
    "21:00" => [
        "Lunes" => ["Zumba", "#7b3fc9"],
        "Martes" => ["Power Jump", "#d98b45"],
        "Miércoles" => ["Zumba", "#7b3fc9"],
        "Jueves" => ["Power Jump", "#d98b45"],
        "Viernes" => ["Zumba", "#7b3fc9"],
    ],
];
?>

<section class="schedule-section">
    <div class="container">

        <div class="schedule-table-wrapper">
            <table class="schedule-table">

                <thead>
                    <tr>
                        <th>Hora</th>
                        <?php foreach ($dias as $dia): ?>
                            <th><?= $dia ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($horarios as $hora => $clases): ?>
                        <tr>

                            <td><?= $hora ?></td>

                            <?php foreach ($dias as $dia): ?>
                                <td>
                                    <?php if ($clases[$dia][0] != ""): ?>
                                        <span style="color:<?= $clases[$dia][1] ?>; font-weight:900;">
                                            <?= $clases[$dia][0] ?>
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