<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfiguracionModel extends Model
{
    protected $table = 'Configuracion';
    protected $primaryKey = 'clave';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';

    protected $allowedFields = [
        'clave',
        'valor',
    ];

    /**
     * Devuelve el valor de una clave de configuración.
     *
     * @param string $clave
     * @param mixed  $default
     * @return mixed
     */
    public function getValor(string $clave, $default = null)
    {
        $row = $this->find($clave);

        return $row['valor'] ?? $default;
    }

    /**
     * Guarda (crea o actualiza) el valor de una clave de configuración.
     *
     * @param string $clave
     * @param mixed  $valor
     * @return void
     */
    public function setValor(string $clave, $valor): void
    {
        if ($this->find($clave)) {
            $this->update($clave, ['valor' => $valor]);
        } else {
            $this->insert(['clave' => $clave, 'valor' => $valor]);
        }
    }
}
