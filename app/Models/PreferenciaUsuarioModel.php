<?php

namespace App\Models;

use CodeIgniter\Model;

class PreferenciaUsuarioModel extends Model
{
    protected $table = 'Preferencia_usuario';
    protected $primaryKey = 'id_preferencia';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_usuario',
        'tema',
        'notif_email',
        'notif_dias',
    ];

    /**
     * Valores permitidos para cada preferencia.
     */
    public const TEMAS = ['dark', 'light'];

    /**
     * Preferencias por defecto.
     */
    public static function porDefecto(): array
    {
        return [
            'tema'        => 'dark',
            'notif_email' => 1,
            'notif_dias'  => 7,
        ];
    }

    /**
     * Devuelve las preferencias de un usuario, creándolas si no existen.
     *
     * @param int $idUsuario
     * @return array
     */
    public function obtener(int $idUsuario): array
    {
        $fila = $this->where('id_usuario', $idUsuario)->first();

        if (!$fila) {
            $datos = self::porDefecto();
            $datos['id_usuario'] = $idUsuario;

            $this->insert($datos);

            return $datos;
        }

        return $fila;
    }

    /**
     * Guarda (crea o actualiza) las preferencias de un usuario.
     *
     * @param int   $idUsuario
     * @param array $datos
     * @return void
     */
    public function guardar(int $idUsuario, array $datos): void
    {
        $fila = $this->where('id_usuario', $idUsuario)->first();

        if ($fila) {
            $this->update($fila['id_preferencia'], $datos);
        } else {
            $datos['id_usuario'] = $idUsuario;
            $this->insert($datos);
        }
    }
}
