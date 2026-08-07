<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
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

    /**
     * Devuelve todos los clientes (rol 3) con sus sistemas (inscripciones),
     * la suscripción de cada uno, su estado y el último pago.
     *
     * @return array
     */
    public function getClientesConInfo()
    {
        return $this->getPersonasConInfo(3);
    }

    /**
     * Devuelve todos los profesores (rol 2) con su información.
     *
     * @return array
     */
    public function getProfesoresConInfo()
    {
        return $this->getPersonasConInfo(2);
    }

    /**
     * Devuelve las personas de un rol con sus sistemas (inscripciones),
     * la suscripción de cada uno, su estado y el último pago.
     *
     * @param int $idRol
     * @return array
     */
    private function getPersonasConInfo($idRol)
    {
        $db = \Config\Database::connect();

        $sql = "
            SELECT
                p.id_persona,
                p.nombre,
                p.apellido,
                p.email,
                p.telefono,
                p.dni,
                p.baja,
                r.descripcion AS rol,
                u.id_usuario,
                u.nombre_usuario,
                i.id_inscripcion,
                i.fecha_inscripcion,
                s.id_sistema,
                s.nombre_sistema,
                su.id_suscripcion,
                su.fecha_inicio,
                su.fecha_vencimiento,
                es.descripcion AS estado_suscripcion,
                (
                    SELECT TOP 1 me.precio
                    FROM Mensualidad me
                    WHERE me.id_sistema = s.id_sistema
                    ORDER BY me.fecha_vigencia DESC
                ) AS precio_mensualidad,
                (
                    SELECT MAX(pg.fecha_pago)
                    FROM Pago pg
                    INNER JOIN Suscripcion su2 ON su2.id_suscripcion = pg.id_suscripcion
                    INNER JOIN Inscripcion i2 ON i2.id_inscripcion = su2.id_inscripcion
                    WHERE i2.id_persona = p.id_persona
                ) AS ultimo_pago
            FROM Persona p
            LEFT JOIN Rol r ON r.id_rol = p.id_rol
            LEFT JOIN Usuario u ON u.id_persona = p.id_persona
            LEFT JOIN Inscripcion i ON i.id_persona = p.id_persona
            LEFT JOIN Sistema s ON s.id_sistema = i.id_sistema
            LEFT JOIN Suscripcion su ON su.id_inscripcion = i.id_inscripcion
            LEFT JOIN Estado_suscripcion es ON es.id_estado = su.id_estado
            WHERE p.id_rol = ?
            ORDER BY p.apellido, p.nombre, i.id_inscripcion, su.id_suscripcion DESC
        ";

        $rows = $db->query($sql, [$idRol])->getResultArray();

        $clientes = [];
        $inscripcionesVistas = [];

        foreach ($rows as $row) {
            $idPersona = (int) $row['id_persona'];

            if (!isset($clientes[$idPersona])) {
                $clientes[$idPersona] = [
                    'id_persona'     => $row['id_persona'],
                    'nombre'         => $row['nombre'],
                    'apellido'       => $row['apellido'],
                    'email'          => $row['email'],
                    'telefono'       => $row['telefono'],
                    'dni'            => $row['dni'],
                    'baja'           => $row['baja'],
                    'rol'            => $row['rol'],
                    'id_usuario'     => $row['id_usuario'],
                    'nombre_usuario' => $row['nombre_usuario'],
                    'ultimo_pago'    => $row['ultimo_pago'],
                    'sistemas'       => []
                ];
            }

            if ($row['id_inscripcion'] === null) {
                continue;
            }

            $idInscripcion = (int) $row['id_inscripcion'];

            if (in_array($idInscripcion, $inscripcionesVistas[$idPersona] ?? [], true)) {
                continue;
            }

            $inscripcionesVistas[$idPersona][] = $idInscripcion;

            $clientes[$idPersona]['sistemas'][] = [
                'id_inscripcion'      => $row['id_inscripcion'],
                'fecha_inscripcion'   => $row['fecha_inscripcion'],
                'id_sistema'          => $row['id_sistema'],
                'nombre_sistema'      => $row['nombre_sistema'],
                'id_suscripcion'      => $row['id_suscripcion'],
                'fecha_inicio'        => $row['fecha_inicio'],
                'fecha_vencimiento'   => $row['fecha_vencimiento'],
                'estado_suscripcion'  => $row['estado_suscripcion'],
                'precio_mensualidad'  => $row['precio_mensualidad']
            ];
        }

        return array_values($clientes);
    }

    /**
     * Devuelve la información completa de un cliente (datos personales + usuario).
     *
     * @param int $idPersona
     * @return array|null
     */
    public function getClienteDetalle($idPersona)
    {
        return $this->select('
                Persona.id_persona,
                Persona.nombre,
                Persona.apellido,
                Persona.email,
                Persona.telefono,
                Persona.dni,
                Persona.baja,
                Persona.id_rol,
                Rol.descripcion AS rol,
                Usuario.id_usuario,
                Usuario.nombre_usuario
            ')
            ->join('Rol', 'Rol.id_rol = Persona.id_rol', 'left')
            ->join('Usuario', 'Usuario.id_persona = Persona.id_persona', 'left')
            ->where('Persona.id_persona', $idPersona)
            ->first();
    }

    /**
     * Devuelve los sistemas (inscripciones) y suscripciones de un cliente.
     *
     * @param int $idPersona
     * @return array
     */
    public function getSuscripcionesCliente($idPersona)
    {
        $db = \Config\Database::connect();

        $sql = "
            SELECT
                i.id_inscripcion,
                i.fecha_inscripcion,
                s.id_sistema,
                s.nombre_sistema,
                su.id_suscripcion,
                su.fecha_inicio,
                su.fecha_vencimiento,
                es.descripcion AS estado_suscripcion,
                (
                    SELECT TOP 1 me.precio
                    FROM Mensualidad me
                    WHERE me.id_sistema = s.id_sistema
                    ORDER BY me.fecha_vigencia DESC
                ) AS precio_mensualidad
            FROM Inscripcion i
            LEFT JOIN Sistema s ON s.id_sistema = i.id_sistema
            LEFT JOIN Suscripcion su ON su.id_inscripcion = i.id_inscripcion
            LEFT JOIN Estado_suscripcion es ON es.id_estado = su.id_estado
            WHERE i.id_persona = ?
            ORDER BY i.id_inscripcion, su.id_suscripcion DESC
        ";

        $result = $db->query($sql, [$idPersona]);

        return $result->getResultArray();
    }
}
