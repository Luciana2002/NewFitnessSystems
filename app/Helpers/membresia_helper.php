<?php

if (!function_exists('nfs_membresia_cliente')) {
    /**
     * Devuelve el estado de la membresía de un cliente.
     *
     * @return array{estado:string, activo:bool, vencida:bool, dias:?int, vencimiento:?string}
     */
    function nfs_membresia_cliente(int $idPersona): array
    {
        $db = \Config\Database::connect();

        $rows = $db->query(
            'SELECT su.fecha_vencimiento
               FROM Suscripcion su
               INNER JOIN Inscripcion i ON i.id_inscripcion = su.id_inscripcion
              WHERE i.id_persona = ? AND su.id_estado <> 3',
            [$idPersona]
        )->getResultArray();

        $max = null;

        foreach ($rows as $row) {
            $fv = $row['fecha_vencimiento'];

            if ($fv instanceof \DateTimeInterface) {
                $fv = $fv->format('Y-m-d');
            }

            if ($max === null || $fv > $max) {
                $max = $fv;
            }
        }

        if ($max === null) {
            return [
                'estado'      => 'sin_suscripcion',
                'activo'      => false,
                'vencida'     => false,
                'dias'        => null,
                'vencimiento' => null,
            ];
        }

        $hoy  = new \DateTime(date('Y-m-d'));
        $venc = new \DateTime($max);
        $dias = (int) $hoy->diff($venc)->format('%r%a');
        $activo = $dias >= 0;

        return [
            'estado'      => $activo ? 'activa' : 'vencida',
            'activo'      => $activo,
            'vencida'     => ! $activo,
            'dias'        => $dias,
            'vencimiento' => $max,
        ];
    }
}

if (!function_exists('nfs_dias_semana')) {
    /**
     * Mapa de número ISO de día (1=Lunes..7=Domingo) al nombre usado en la BD.
     */
    function nfs_dias_semana(): array
    {
        return [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miercoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sabado',
            7 => 'Domingo',
        ];
    }
}

if (!function_exists('nfs_dias_semana_etiquetas')) {
    /**
     * Etiquetas con acentos para mostrar en pantalla (1=Lunes..7=Domingo).
     */
    function nfs_dias_semana_etiquetas(): array
    {
        return [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];
    }
}

if (!function_exists('nfs_dias_semana_abrev')) {
    /**
     * Abreviaturas (1=Lunes..7=Domingo).
     */
    function nfs_dias_semana_abrev(): array
    {
        return [
            1 => 'Lun',
            2 => 'Mar',
            3 => 'Mié',
            4 => 'Jue',
            5 => 'Vie',
            6 => 'Sáb',
            7 => 'Dom',
        ];
    }
}

if (!function_exists('nfs_dia_semana_actual')) {
    /**
     * Número ISO del día actual (1=Lunes..7=Domingo).
     */
    function nfs_dia_semana_actual(): int
    {
        return (int) date('N');
    }
}

if (!function_exists('nfs_dias_etiquetas_texto')) {
    /**
     * Convierte una lista de números de día en un texto legible (ej: "Lun, Mié, Vie").
     */
    function nfs_dias_etiquetas_texto(array $dias, bool $abrev = true): string
    {
        $mapa = $abrev ? nfs_dias_semana_abrev() : nfs_dias_semana_etiquetas();
        $out  = [];

        foreach ($dias as $dia) {
            $dia = (int) $dia;
            if (isset($mapa[$dia])) {
                $out[] = $mapa[$dia];
            }
        }

        return implode(', ', $out);
    }
}
