<?php

namespace App\Models;

use CodeIgniter\Model;

class GastoModel extends Model
{
    protected $table = 'Gasto';
    protected $primaryKey = 'id_gasto';

    protected $allowedFields = [
        'fecha',
        'categoria',
        'monto',
        'detalle',
        'id_usuario',
        'baja',
    ];

    /**
     * Categorías sugeridas para los gastos.
     *
     * @return array
     */
    public function getCategorias(): array
    {
        return [
            'Alquiler',
            'Sueldos',
            'Mantenimiento',
            'Servicios',
            'Impuestos',
            'Insumos',
            'Marketing',
            'Otros',
        ];
    }

    /**
     * Lista los gastos (no dados de baja) de un período YYYY-MM.
     *
     * @param string $mes
     * @param array  $excluirCategorias Categorías a omitir (ej. Sueldos, que se calcula por liquidación).
     * @return array
     */
    public function getGastosMes(string $mes, array $excluirCategorias = []): array
    {
        $inicio = $mes . '-01';
        $fin    = date('Y-m-t', strtotime($inicio));

        $builder = $this->select('
                Gasto.id_gasto,
                Gasto.fecha,
                Gasto.categoria,
                Gasto.monto,
                Gasto.detalle,
                Usuario.nombre_usuario AS cargado_por_nombre,
                Persona.nombre AS cargado_por_persona,
                Persona.apellido AS cargado_por_apellido
            ')
            ->join('Usuario', 'Usuario.id_usuario = Gasto.id_usuario', 'left')
            ->join('Persona', 'Persona.id_persona = Usuario.id_persona', 'left')
            ->where('Gasto.baja', 'N')
            ->where('Gasto.fecha >=', $inicio)
            ->where('Gasto.fecha <=', $fin);

        if (!empty($excluirCategorias)) {
            $builder->whereNotIn('Gasto.categoria', $excluirCategorias);
        }

        return $builder
            ->orderBy('Gasto.fecha', 'DESC')
            ->orderBy('Gasto.id_gasto', 'DESC')
            ->findAll();
    }

    /**
     * Totales de gastos por categoría para un período YYYY-MM.
     *
     * @param string $mes
     * @param array  $excluirCategorias
     * @return array
     */
    public function getTotalesPorCategoria(string $mes, array $excluirCategorias = []): array
    {
        $inicio = $mes . '-01';
        $fin    = date('Y-m-t', strtotime($inicio));

        $builder = $this->select('
                Gasto.categoria,
                SUM(Gasto.monto) AS total
            ')
            ->where('Gasto.baja', 'N')
            ->where('Gasto.fecha >=', $inicio)
            ->where('Gasto.fecha <=', $fin);

        if (!empty($excluirCategorias)) {
            $builder->whereNotIn('Gasto.categoria', $excluirCategorias);
        }

        return $builder
            ->groupBy('Gasto.categoria')
            ->orderBy('total', 'DESC')
            ->findAll();
    }

    /**
     * Total de gastos de un período YYYY-MM.
     *
     * @param string $mes
     * @param array  $excluirCategorias
     * @return float
     */
    public function getTotalMes(string $mes, array $excluirCategorias = []): float
    {
        $inicio = $mes . '-01';
        $fin    = date('Y-m-t', strtotime($inicio));

        $builder = $this->selectSum('monto', 'total')
            ->where('baja', 'N')
            ->where('fecha >=', $inicio)
            ->where('fecha <=', $fin);

        if (!empty($excluirCategorias)) {
            $builder->whereNotIn('categoria', $excluirCategorias);
        }

        $row = $builder->first();

        return (float) ($row['total'] ?? 0);
    }
}
