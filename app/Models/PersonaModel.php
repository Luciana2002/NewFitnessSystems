<?php

namespace App\Models;

use CodeIgniter\Model;

class PersonaModel extends Model
{
    protected $table = 'Persona';
    protected $primaryKey = 'id_persona';

    protected $allowedFields = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'dni',
        'id_rol',
        'baja'
    ];

    public function getPersonasAll()
    {
        return $this->select('
                Persona.id_persona,
                Persona.nombre,
                Persona.apellido,
                Persona.email,
                Persona.telefono,
                Persona.dni,
                Persona.id_rol,
                Persona.baja,
                Rol.descripcion AS rol
            ')
            ->join('Rol', 'Rol.id_rol = Persona.id_rol')
            ->findAll();
    }

    public function getClientes()
    {
        return $this->select('
                Persona.id_persona,
                Persona.nombre,
                Persona.apellido,
                Persona.email,
                Persona.telefono,
                Persona.dni,
                Persona.id_rol,
                Persona.baja,
                Rol.descripcion AS rol
            ')
            ->join('Rol', 'Rol.id_rol = Persona.id_rol')
            ->where('Persona.id_rol', 3)
            ->findAll();
    }

    public function getProfesores()
    {
        return $this->select('
                Persona.id_persona,
                Persona.nombre,
                Persona.apellido,
                Persona.email,
                Persona.telefono,
                Persona.dni,
                Persona.id_rol,
                Persona.baja,
                Rol.descripcion AS rol
            ')
            ->join('Rol', 'Rol.id_rol = Persona.id_rol')
            ->where('Persona.id_rol', 2)
            ->findAll();
    }

    public function getAdministradores()
    {
        return $this->select('
                Persona.id_persona,
                Persona.nombre,
                Persona.apellido,
                Persona.email,
                Persona.telefono,
                Persona.dni,
                Persona.id_rol,
                Persona.baja,
                Rol.descripcion AS rol
            ')
            ->join('Rol', 'Rol.id_rol = Persona.id_rol')
            ->where('Persona.id_rol', 1)
            ->findAll();
    }
}