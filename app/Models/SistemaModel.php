<?php

namespace App\Models;

use CodeIgniter\Model;

class SistemaModel extends Model
{
    protected $table = 'Sistema';
    protected $primaryKey = 'id_sistema';

    protected $allowedFields = [
        'nombre_sistema',
        'baja'
    ];

    /**
     * Devuelve los sistemas con su precio de mensualidad vigente.
     *
     * @param bool $soloActivos Filtra los sistemas dados de baja.
     * @return array
     */
    public function getSistemasConPrecio($soloActivos = true)
    {
        $db = \Config\Database::connect();

        $sql = "
            SELECT
                s.id_sistema,
                s.nombre_sistema,
                s.baja,
                (
                    SELECT TOP 1 me.precio
                    FROM Mensualidad me
                    WHERE me.id_sistema = s.id_sistema
                    ORDER BY me.fecha_vigencia DESC
                ) AS precio
            FROM Sistema s
        ";

        if ($soloActivos) {
            $sql .= " WHERE s.baja = 'N'";
        }

        $sql .= " ORDER BY s.nombre_sistema";

        return $db->query($sql)->getResultArray();
    }
}