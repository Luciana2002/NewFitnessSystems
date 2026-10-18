<?php

namespace App\Models;

use CodeIgniter\Model;

class BitacoraModel extends Model
{
    protected $table = 'Bitacora';
    protected $primaryKey = 'id_log';

    protected $allowedFields = [
        'fecha',
        'id_usuario',
        'nombre_usuario',
        'accion',
        'entidad',
        'id_entidad',
        'detalle',
    ];

    /**
     * Lista los registros de bitácora aplicando los filtros recibidos.
     *
     * @param array $filtros entidad, accion, usuario, desde, hasta, buscar
     * @param int   $limite
     * @return array
     */
    public function getRegistros(array $filtros = [], int $limite = 300): array
    {
        $builder = $this->orderBy('fecha', 'DESC')->orderBy('id_log', 'DESC');

        if (!empty($filtros['entidad'])) {
            $builder->where('entidad', $filtros['entidad']);
        }

        if (!empty($filtros['accion'])) {
            $builder->where('accion', $filtros['accion']);
        }

        if (!empty($filtros['usuario'])) {
            $builder->where('id_usuario', (int) $filtros['usuario']);
        }

        if (!empty($filtros['desde'])) {
            $builder->where('fecha >=', $filtros['desde'] . 'T00:00:00');
        }

        if (!empty($filtros['hasta'])) {
            $builder->where('fecha <=', $filtros['hasta'] . 'T23:59:59');
        }

        if (!empty($filtros['buscar'])) {
            $builder->groupStart()
                ->like('detalle', $filtros['buscar'])
                ->orLike('nombre_usuario', $filtros['buscar'])
                ->orLike('entidad', $filtros['buscar'])
                ->groupEnd();
        }

        return $builder->limit($limite)->findAll();
    }

    /**
     * Lista de usuarios que aparecen en la bitácora (para el filtro).
     *
     * @return array
     */
    public function getUsuarios(): array
    {
        return $this->select('id_usuario, nombre_usuario')
            ->where('id_usuario IS NOT NULL')
            ->groupBy('id_usuario, nombre_usuario')
            ->orderBy('nombre_usuario', 'ASC')
            ->findAll();
    }

    /**
     * @return array
     */
    public function getEntidades(): array
    {
        return $this->select('entidad')
            ->groupBy('entidad')
            ->orderBy('entidad', 'ASC')
            ->findAll();
    }
}
