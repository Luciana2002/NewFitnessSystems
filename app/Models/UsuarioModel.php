<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'Usuario';
    protected $primaryKey = 'id_usuario';

    protected $allowedFields = [
        'nombre_usuario',
        'contraseña',
        'id_persona'
    ];

    public function getUsuariosAll()
    {
        return $this->select('
                Usuario.id_usuario,
                Usuario.nombre_usuario,
                Usuario.contraseña,
                Usuario.id_persona,
                Persona.nombre,
                Persona.apellido,
                Persona.email,
                Persona.telefono,
                Persona.dni,
                Persona.id_rol,
                Persona.baja,
                Rol.descripcion AS rol
            ')
            ->join('Persona', 'Persona.id_persona = Usuario.id_persona')
            ->join('Rol', 'Rol.id_rol = Persona.id_rol')
            ->findAll();
    }

    public function getUsuarioCompleto($id)
    {
        return $this->select('
                Usuario.id_usuario,
                Usuario.nombre_usuario,
                Usuario.contraseña,
                Usuario.id_persona,
                Persona.nombre,
                Persona.apellido,
                Persona.email,
                Persona.telefono,
                Persona.dni,
                Persona.id_rol,
                Persona.baja,
                Rol.descripcion AS rol
            ')
            ->join('Persona', 'Persona.id_persona = Usuario.id_persona')
            ->join('Rol', 'Rol.id_rol = Persona.id_rol')
            ->where('Usuario.id_usuario', $id)
            ->first();
    }

    public function getUsuarioPorEmail($email)
    {
        return $this->select('
                Usuario.id_usuario,
                Usuario.nombre_usuario,
                Usuario.contraseña,
                Usuario.id_persona,
                Persona.nombre,
                Persona.apellido,
                Persona.email,
                Persona.telefono,
                Persona.dni,
                Persona.id_rol,
                Persona.baja,
                Rol.descripcion AS rol
            ')
            ->join('Persona', 'Persona.id_persona = Usuario.id_persona')
            ->join('Rol', 'Rol.id_rol = Persona.id_rol')
            ->where('Persona.email', $email)
            ->first();
    }

    public function getUsuarioPorNombreUsuario($nombre_usuario)
    {
        return $this->select('
                Usuario.id_usuario,
                Usuario.nombre_usuario,
                Usuario.contraseña,
                Usuario.id_persona,
                Persona.nombre,
                Persona.apellido,
                Persona.email,
                Persona.telefono,
                Persona.dni,
                Persona.id_rol,
                Persona.baja,
                Rol.descripcion AS rol
            ')
            ->join('Persona', 'Persona.id_persona = Usuario.id_persona')
            ->join('Rol', 'Rol.id_rol = Persona.id_rol')
            ->where('Usuario.nombre_usuario', $nombre_usuario)
            ->first();
    }

    public function getUsuarioPorPersona($idPersona)
    {
        return $this->select('
                Usuario.id_usuario,
                Usuario.nombre_usuario,
                Usuario.contraseña,
                Usuario.id_persona
            ')
            ->where('Usuario.id_persona', $idPersona)
            ->first();
    }

    public function getClientesAll()
    {
        return $this->select('
            Usuario.id_usuario,
            Usuario.nombre_usuario,
            Usuario.contraseña,
            Usuario.id_persona,
            Persona.nombre,
            Persona.apellido,
            Persona.email,
            Persona.telefono,
            Persona.dni,
            Persona.id_rol,
            Persona.baja,
            Rol.descripcion AS rol
        ')
        ->join('Persona', 'Persona.id_persona = Usuario.id_persona', 'right')
        ->join('Rol', 'Rol.id_rol = Persona.id_rol')
        ->where('Persona.id_rol', 3)
        ->findAll();
    }
}