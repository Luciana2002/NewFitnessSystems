<?php

namespace App\Models;

use CodeIgniter\Model;

class HorarioModel extends Model
{
    protected $table = 'Horario';
    protected $primaryKey = 'id_horario';

    protected $allowedFields = [
        'hora_inicio',
        'hora_fin',
        'dia_semana',
        'id_sistema',
        'cupo_max',
        'baja'
    ];

    public function getHorariosAll()
    {
        $db = \Config\Database::connect();

        $sql = "
            SELECT
                Horario.id_horario,
                Horario.id_sistema,
                Horario.hora_inicio,
                Horario.hora_fin,
                Horario.dia_semana,
                Horario.cupo_max,
                Horario.baja,
                Sistema.nombre_sistema,
                Sistema.color,
                (
                    SELECT COUNT(DISTINCT i.id_persona)
                    FROM Inscripcion i
                    INNER JOIN Horario hi ON hi.id_horario = i.id_horario
                    WHERE hi.id_sistema = Horario.id_sistema
                      AND hi.hora_inicio = Horario.hora_inicio
                      AND hi.hora_fin = Horario.hora_fin
                      AND EXISTS (
                          SELECT 1 FROM Suscripcion su
                          WHERE su.id_inscripcion = i.id_inscripcion
                            AND su.id_estado <> 3
                      )
                ) AS inscriptos
            FROM Horario
            INNER JOIN Sistema ON Sistema.id_sistema = Horario.id_sistema
            WHERE Sistema.baja = 'N'
            ORDER BY Sistema.nombre_sistema, Horario.hora_inicio, Horario.dia_semana
        ";

        return $db->query($sql)->getResultArray();
    }

    public function getHorariosActivos()
    {
        return $this->select('
                Horario.id_horario,
                Horario.hora_inicio,
                Horario.hora_fin,
                Horario.dia_semana,
                Horario.id_sistema,
                Horario.cupo_max,
                Horario.baja,
                Sistema.nombre_sistema,
                Sistema.color
            ')
            ->join('Sistema', 'Sistema.id_sistema = Horario.id_sistema')
            ->where('Horario.baja', 'N')
            ->where('Sistema.baja', 'N')
            ->findAll();
    }

    /**
     * Turnos (hora inicio/fin unica) de un sistema, con cupo e inscriptos.
     * Un turno abarca todos los dias a esa misma hora.
     */
    public function getTurnosPorSistema(int $idSistema): array
    {
        $db = \Config\Database::connect();

        $sql = "
            SELECT
                MIN(t.id_horario) AS id_horario,
                t.hora_inicio,
                t.hora_fin,
                MAX(t.cupo_max) AS cupo_max,
                (
                    SELECT COUNT(DISTINCT i.id_persona)
                    FROM Inscripcion i
                    INNER JOIN Horario hi ON hi.id_horario = i.id_horario
                    WHERE hi.id_sistema = t.id_sistema
                      AND hi.hora_inicio = t.hora_inicio
                      AND hi.hora_fin = t.hora_fin
                      AND EXISTS (
                          SELECT 1 FROM Suscripcion su
                          WHERE su.id_inscripcion = i.id_inscripcion
                            AND su.id_estado <> 3
                      )
                ) AS inscriptos
            FROM Horario t
            WHERE t.id_sistema = ? AND t.baja = 'N'
            GROUP BY t.id_sistema, t.hora_inicio, t.hora_fin
            ORDER BY t.hora_inicio
        ";

        return $db->query($sql, [$idSistema])->getResultArray();
    }

    /**
     * Id de un horario representativo (primer dia) para un turno.
     */
    public function horarioRepresentante(int $idSistema, string $horaInicio, string $horaFin): ?int
    {
        $fila = $this->select('id_horario')
            ->where('id_sistema', $idSistema)
            ->where('hora_inicio', $horaInicio)
            ->where('hora_fin', $horaFin)
            ->where('baja', 'N')
            ->orderBy('id_horario', 'ASC')
            ->first();

        return $fila ? (int) $fila['id_horario'] : null;
    }

    /**
     * Cantidad de inscriptos activos de un turno (misma hora dentro del sistema).
     */
    public function contarInscriptosTurno(int $idSistema, string $horaInicio, string $horaFin): int
    {
        $db = \Config\Database::connect();

        $fila = $db->query(
            "SELECT COUNT(DISTINCT i.id_persona) AS total
             FROM Inscripcion i
             INNER JOIN Horario hi ON hi.id_horario = i.id_horario
             WHERE hi.id_sistema = ?
               AND hi.hora_inicio = ?
               AND hi.hora_fin = ?
               AND EXISTS (
                   SELECT 1 FROM Suscripcion su
                   WHERE su.id_inscripcion = i.id_inscripcion
                     AND su.id_estado <> 3
               )",
            [$idSistema, $horaInicio, $horaFin]
        )->getRowArray();

        return (int) ($fila['total'] ?? 0);
    }

    /**
     * Indica si un turno alcanzo su cupo maximo.
     */
    public function estaLlenoTurno(int $idSistema, string $horaInicio, string $horaFin): bool
    {
        $db = \Config\Database::connect();

        $cupo = $db->query(
            "SELECT MAX(cupo_max) AS cupo FROM Horario
             WHERE id_sistema = ? AND hora_inicio = ? AND hora_fin = ? AND baja = 'N'",
            [$idSistema, $horaInicio, $horaFin]
        )->getRowArray();

        $cupoMax = $cupo['cupo'] ?? null;

        if ($cupoMax === null || (int) $cupoMax <= 0) {
            return false;
        }

        return $this->contarInscriptosTurno($idSistema, $horaInicio, $horaFin) >= (int) $cupoMax;
    }

    /**
     * Datos del turno (sistema + hora) de un horario concreto.
     */
    public function turnoDelHorario(int $idHorario): ?array
    {
        $fila = $this->select('id_sistema, hora_inicio, hora_fin')
            ->where('id_horario', $idHorario)
            ->first();

        return $fila ?: null;
    }
}
