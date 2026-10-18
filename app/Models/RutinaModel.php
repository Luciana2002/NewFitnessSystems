<?php

namespace App\Models;

use CodeIgniter\Model;

class RutinaModel extends Model
{
    protected $table = 'Rutina';
    protected $primaryKey = 'id_rutina';

    protected $allowedFields = [
        'id_profesor',
        'id_cliente',
        'para_todos',
        'nombre',
        'descripcion',
        'fecha',
        'baja'
    ];

    private function subconsultaDias(): string
    {
        return "(SELECT COUNT(*) FROM RutinaDia d WHERE d.id_rutina = Rutina.id_rutina) AS cantidad_dias";
    }

    public function getRutinasCliente($idCliente)
    {
        return $this->select('
                Rutina.id_rutina,
                Rutina.nombre,
                Rutina.descripcion,
                Rutina.fecha,
                Rutina.para_todos,
                Persona.nombre AS profesor_nombre,
                Persona.apellido AS profesor_apellido
            ')
            ->join('Persona', 'Persona.id_persona = Rutina.id_profesor', 'left')
            ->groupStart()
                ->where('Rutina.id_cliente', $idCliente)
                ->orWhere('Rutina.para_todos', 1)
            ->groupEnd()
            ->where('Rutina.baja', 'N')
            ->orderBy('Rutina.fecha', 'DESC')
            ->findAll();
    }

    /**
     * Rutinas que le corresponden a un cliente en un día concreto (1=Lunes..7=Domingo).
     */
    public function getRutinasClienteDia($idCliente, $dia)
    {
        return $this->select('
                Rutina.id_rutina,
                Rutina.nombre,
                Rutina.descripcion,
                Rutina.fecha,
                Rutina.para_todos,
                Persona.nombre AS profesor_nombre,
                Persona.apellido AS profesor_apellido,
                (SELECT TOP 1 d.dia_semana FROM RutinaDia d WHERE d.id_rutina = Rutina.id_rutina AND d.dia_semana = ' . (int) $dia . ') AS dia
            ')
            ->join('Persona', 'Persona.id_persona = Rutina.id_profesor', 'left')
            ->join('RutinaDia', 'RutinaDia.id_rutina = Rutina.id_rutina', 'inner')
            ->groupStart()
                ->where('Rutina.id_cliente', $idCliente)
                ->orWhere('Rutina.para_todos', 1)
            ->groupEnd()
            ->where('Rutina.baja', 'N')
            ->where('RutinaDia.dia_semana', (int) $dia)
            ->orderBy('Rutina.fecha', 'DESC')
            ->findAll();
    }

    public function getRutinasProfesor($idProfesor)
    {
        return $this->select('
                Rutina.id_rutina,
                Rutina.nombre,
                Rutina.descripcion,
                Rutina.fecha,
                Rutina.para_todos,
                Persona.nombre AS cliente_nombre,
                Persona.apellido AS cliente_apellido,
                Persona.dni AS cliente_dni,
                (SELECT COUNT(*) FROM RutinaEjercicio e WHERE e.id_rutina = Rutina.id_rutina) AS cantidad_ejercicios,
                ' . $this->subconsultaDias() . '
            ')
            ->join('Persona', 'Persona.id_persona = Rutina.id_cliente', 'left')
            ->where('Rutina.id_profesor', $idProfesor)
            ->where('Rutina.baja', 'N')
            ->orderBy('Rutina.fecha', 'DESC')
            ->findAll();
    }

    public function getRutina($idRutina)
    {
        return $this->select('
                Rutina.id_rutina,
                Rutina.id_profesor,
                Rutina.id_cliente,
                Rutina.para_todos,
                Rutina.nombre,
                Rutina.descripcion,
                Rutina.fecha,
                Persona.nombre AS cliente_nombre,
                Persona.apellido AS cliente_apellido
            ')
            ->join('Persona', 'Persona.id_persona = Rutina.id_cliente', 'left')
            ->where('Rutina.id_rutina', $idRutina)
            ->first();
    }

    public function getEjercicios($idRutina)
    {
        return $this->db->table('RutinaEjercicio')
            ->where('id_rutina', $idRutina)
            ->orderBy('orden', 'ASC')
            ->orderBy('id_ejercicio', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Días (1..7) asignados a una rutina, ordenados.
     */
    public function getDias($idRutina): array
    {
        $rows = $this->db->table('RutinaDia')
            ->select('dia_semana')
            ->where('id_rutina', $idRutina)
            ->orderBy('dia_semana', 'ASC')
            ->get()
            ->getResultArray();

        return array_map('intval', array_column($rows, 'dia_semana'));
    }
}
