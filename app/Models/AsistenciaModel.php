<?php

namespace App\Models;

use CodeIgniter\Model;

class AsistenciaModel extends Model
{
    protected $table = 'Asistencia';
    protected $primaryKey = 'id_asistencia';

    protected $allowedFields = [
        'id_persona',
        'fecha',
        'hora',
        'resultado',
        'id_usuario',
        'observacion'
    ];

    public function getAsistenciasCliente($idPersona)
    {
        return $this->select('
                Asistencia.id_asistencia,
                Asistencia.fecha,
                Asistencia.hora,
                Asistencia.resultado,
                Asistencia.observacion,
                Usuario.nombre_usuario AS registrado_por
            ')
            ->join('Usuario', 'Usuario.id_usuario = Asistencia.id_usuario', 'left')
            ->where('Asistencia.id_persona', $idPersona)
            ->where('Asistencia.resultado', 'OK')
            ->orderBy('Asistencia.fecha', 'DESC')
            ->orderBy('Asistencia.hora', 'DESC')
            ->findAll();
    }

    public function getAsistenciasDelDia($fecha)
    {
        return $this->select('
                Asistencia.id_asistencia,
                Asistencia.fecha,
                Asistencia.hora,
                Asistencia.resultado,
                Persona.nombre,
                Persona.apellido,
                Persona.dni
            ')
            ->join('Persona', 'Persona.id_persona = Asistencia.id_persona', 'left')
            ->where('Asistencia.fecha', $fecha)
            ->orderBy('Asistencia.hora', 'DESC')
            ->findAll();
    }

    public function yaRegistroHoy($idPersona, $fecha)
    {
        return $this->where('id_persona', $idPersona)
            ->where('fecha', $fecha)
            ->where('resultado', 'OK')
            ->first();
    }

    /**
     * Devuelve la última asistencia (resultado OK) de cada persona,
     * indexada por id_persona. El valor es la fecha en formato Y-m-d.
     *
     * @return array<int, string>
     */
    public function getUltimasAsistencias(): array
    {
        $rows = $this->db->table('Asistencia')
            ->select('id_persona, MAX(fecha) AS ultima')
            ->where('resultado', 'OK')
            ->groupBy('id_persona')
            ->get()
            ->getResultArray();

        $mapa = [];

        foreach ($rows as $row) {
            $fecha = $row['ultima'];

            if ($fecha instanceof \DateTimeInterface) {
                $fecha = $fecha->format('Y-m-d');
            }

            $mapa[(int) $row['id_persona']] = (string) $fecha;
        }

        return $mapa;
    }
}
