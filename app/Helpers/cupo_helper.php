<?php

use App\Models\HorarioModel;

if (!function_exists('nfs_horario_tiene_cupo')) {
    /**
     * Indica si un horario todavía admite inscriptos nuevos.
     * Un cupo nulo o <= 0 significa sin límite.
     *
     * @param int      $idHorario
     * @param int|null $inscriptosActuales Si se pasa, evita recontar.
     * @return bool
     */
    function nfs_horario_tiene_cupo(int $idHorario, ?int $inscriptosActuales = null): bool
    {
        $model = new HorarioModel();
        $horario = $model->find($idHorario);

        if (! $horario || $horario['cupo_max'] === null || (int) $horario['cupo_max'] <= 0) {
            return true;
        }

        $inscriptos = $inscriptosActuales ?? $model->contarInscriptos($idHorario);

        return $inscriptos < (int) $horario['cupo_max'];
    }
}

if (!function_exists('nfs_horario_etiqueta_cupo')) {
    /**
     * Texto de cupo para mostrar en pantalla.
     *
     * @param array $horario Fila con cupo_max e inscriptos.
     * @return string
     */
    function nfs_horario_etiqueta_cupo(array $horario): string
    {
        $cupo = $horario['cupo_max'] ?? null;

        if ($cupo === null || (int) $cupo <= 0) {
            return 'Sin límite';
        }

        $inscriptos = (int) ($horario['inscriptos'] ?? 0);

        return $inscriptos . ' / ' . (int) $cupo;
    }
}

if (!function_exists('nfs_horario_label')) {
    /**
     * Etiqueta legible de un horario: "Lunes 08:00 - 09:00".
     *
     * @param array $horario Fila con dia_semana, hora_inicio y hora_fin.
     * @return string
     */
    function nfs_horario_label(array $horario): string
    {
        $dia = $horario['dia_semana'] ?? '';
        $inicio = isset($horario['hora_inicio']) ? substr((string) $horario['hora_inicio'], 0, 5) : '';
        $fin = isset($horario['hora_fin']) ? substr((string) $horario['hora_fin'], 0, 5) : '';

        return trim($dia . ' ' . $inicio . ' - ' . $fin);
    }
}

if (!function_exists('nfs_turno_label')) {
    /**
     * Etiqueta de un turno (sin día): "08:30 - 09:30".
     * Agrega "Mañana"/"Tarde"/"Noche" según el horario de inicio.
     *
     * @param array $turno Fila con hora_inicio y hora_fin.
     * @return string
     */
    function nfs_turno_label(array $turno): string
    {
        $inicio = isset($turno['hora_inicio']) ? substr((string) $turno['hora_inicio'], 0, 5) : '';
        $fin = isset($turno['hora_fin']) ? substr((string) $turno['hora_fin'], 0, 5) : '';

        $hora = (int) substr($inicio, 0, 2);

        if ($hora < 12) {
            $franja = 'Mañana';
        } elseif ($hora < 19) {
            $franja = 'Tarde';
        } else {
            $franja = 'Noche';
        }

        return $franja . ' ' . $inicio . ' - ' . $fin;
    }
}
