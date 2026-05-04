<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table = 'Datos_personales';
    protected $primaryKey = 'id_persona';

    protected $allowedFields = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'dni',
        'baja'
    ];

    public function getClientes()
    {
        return $this->select('
                Datos_personales.id_persona,
                Datos_personales.nombre,
                Datos_personales.apellido,
                Datos_personales.email,
                Datos_personales.telefono,
                Datos_personales.dni,
                Datos_personales.baja,
                Rol.descripcion AS rol
            ')
            ->join('Usuario', 'Usuario.id_persona = Datos_personales.id_persona', 'left')
            ->join('Rol', 'Rol.id_rol = Usuario.id_rol', 'left')
            ->where('Usuario.id_rol', 3) // cliente
            ->orWhere('Usuario.id_usuario IS NULL') // por si no tiene usuario
            ->findAll();
    }
}