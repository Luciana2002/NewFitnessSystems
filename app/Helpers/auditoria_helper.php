<?php

if (!function_exists('nfs_log')) {
    /**
     * Registra una acción en la bitácora (tabla Bitacora).
     * No interrumpe la operación principal si algo falla.
     *
     * @param string   $accion     ALTA | BAJA | EDICION | PRECIO | ...
     * @param string   $entidad    Cliente | Profesor | Sistema | Pago | Gasto | Usuario | Plantilla
     * @param int|null $idEntidad  id del registro afectado
     * @param string|null $detalle Descripción legible de lo que pasó
     */
    function nfs_log(string $accion, string $entidad, $idEntidad = null, ?string $detalle = null): void
    {
        try {
            $session = session();

            $usuario = $session->get('nombre_usuario');
            if (!$usuario) {
                $nombre = trim((string) $session->get('nombre') . ' ' . (string) $session->get('apellido'));
                $usuario = $nombre !== '' ? $nombre : 'sistema';
            }

            $db = \Config\Database::connect();

            $db->table('Bitacora')->insert([
                'fecha'          => date('Y-m-d\TH:i:s'),
                'id_usuario'     => $session->get('id_usuario'),
                'nombre_usuario' => $usuario,
                'accion'         => strtoupper($accion),
                'entidad'        => $entidad,
                'id_entidad'     => $idEntidad !== null ? (int) $idEntidad : null,
                'detalle'        => $detalle,
            ]);
        } catch (\Throwable $e) {
            // La auditoría nunca debe romper la operación principal.
        }
    }
}
