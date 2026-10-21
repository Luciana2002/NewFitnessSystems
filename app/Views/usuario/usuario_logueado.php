<?php
$idRol  = (int) session()->get('id_rol');
$nombre = (string) session()->get('nombre');

$horaActual = (int) date('G');

if ($horaActual < 12) {
    $saludo = 'Buenos días';
} elseif ($horaActual < 20) {
    $saludo = 'Buenas tardes';
} else {
    $saludo = 'Buenas noches';
}

$diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
$meses = [
    1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
    5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
    9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
];

$fechaLarga = $diasSemana[(int) date('w')] . ' ' . (int) date('j') . ' de ' . $meses[(int) date('n')] . ' de ' . date('Y');

$panelNombre = match ($idRol) {
    1 => 'Panel de administración',
    2 => 'Panel del profesor',
    default => 'Mi panel',
};

$ctaUrl = match ($idRol) {
    1 => base_url('pagos'),
    2 => base_url('rutinas/nueva'),
    default => base_url('cliente/suscripcion'),
};

$ctaTexto = match ($idRol) {
    1 => 'Registrar pago',
    2 => 'Nueva rutina',
    default => 'Ver suscripción',
};

$ctaIcono = match ($idRol) {
    1 => 'bi-cash-coin',
    2 => 'bi-plus-circle',
    default => 'bi-card-list',
};
?>

<main class="dashboard-page home-page">

    

</main>

<script>
    const toasts = document.querySelectorAll('.toast-custom');

    toasts.forEach((toast, index) => {
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        }, 100);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-20px)';
            setTimeout(() => toast.remove(), 400);
        }, 2800 + (index * 200));
    });
</script>