<?php

if (!function_exists('formatear_fecha')) {
    function formatear_fecha($fecha): string
    {
        if (empty($fecha)) {
            return '-';
        }

        if ($fecha instanceof DateTime) {
            return $fecha->format('d/m/Y');
        }

        if (is_string($fecha)) {
            $timestamp = strtotime($fecha);

            return $timestamp ? date('d/m/Y', $timestamp) : $fecha;
        }

        return '-';
    }
}

if (!function_exists('formatear_hora')) {
    function formatear_hora($hora): string
    {
        if (empty($hora)) {
            return '-';
        }

        if ($hora instanceof DateTime) {
            return $hora->format('H:i');
        }

        if (is_string($hora)) {
            $timestamp = strtotime($hora);

            return $timestamp ? date('H:i', $timestamp) : $hora;
        }

        return '-';
    }
}

if (!function_exists('formatear_monto')) {
    function formatear_monto($monto): string
    {
        if ($monto === null || $monto === '') {
            return '-';
        }

        return '$ ' . number_format((float) $monto, 2, ',', '.');
    }
}
