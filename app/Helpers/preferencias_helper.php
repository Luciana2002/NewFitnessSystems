<?php

use App\Models\PreferenciaUsuarioModel;

if (!function_exists('nfs_preferencias')) {
    /**
     * Devuelve las preferencias del usuario logueado.
     * Se cachean durante la petición para evitar consultas repetidas.
     *
     * @return array
     */
    function nfs_preferencias(): array
    {
        static $cache = null;

        if ($cache !== null) {
            return $cache;
        }

        $idUsuario = (int) session()->get('id_usuario');

        if ($idUsuario <= 0) {
            $cache = PreferenciaUsuarioModel::porDefecto();

            return $cache;
        }

        $cache = (new PreferenciaUsuarioModel())->obtener($idUsuario);

        return $cache;
    }
}
