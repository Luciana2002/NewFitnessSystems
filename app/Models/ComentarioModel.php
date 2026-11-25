<?php

namespace App\Models;

use CodeIgniter\Model;

class ComentarioModel extends Model
{
    public const TIPOS = ['Sugerencia', 'Comentario', 'Reclamo'];

    protected $table         = 'Comentario';
    protected $primaryKey    = 'id_comentario';
    protected $allowedFields = [
        'id_persona',
        'tipo',
        'mensaje',
        'leido',
    ];

    /**
     * Normaliza el tipo a los valores permitidos por el buzón.
     */
    private function normalizarTipo(string $tipo, ?string $porDefecto = null): ?string
    {
        foreach (self::TIPOS as $valido) {
            if (strcasecmp(trim($tipo), $valido) === 0) {
                return $valido;
            }
        }

        return $porDefecto;
    }

    /**
     * Alta de un mensaje del buzón. Devuelve el id generado.
     *
     * @throws \InvalidArgumentException
     */
    public function crearComentario(int $idPersona, string $tipo, string $mensaje): int
    {
        if ($idPersona <= 0) {
            throw new \InvalidArgumentException('La persona es obligatoria.');
        }

        $tipoNormalizado = $this->normalizarTipo($tipo, 'Comentario');
        $mensaje         = trim($mensaje);

        if ($mensaje === '') {
            throw new \InvalidArgumentException('El mensaje es obligatorio.');
        }

        if (function_exists('mb_substr')) {
            $mensaje = mb_substr($mensaje, 0, 1000);
        } else {
            $mensaje = substr($mensaje, 0, 1000);
        }

        $this->insert([
            'id_persona' => $idPersona,
            'tipo'       => $tipoNormalizado,
            'mensaje'    => $mensaje,
        ]);

        return (int) $this->getInsertID();
    }

    /**
     * Mensajes enviados por una persona, más recientes primero.
     */
    public function getComentariosPorPersona(int $idPersona): array
    {
        return $this->where('id_persona', $idPersona)
                    ->orderBy('fecha_registro', 'DESC')
                    ->findAll();
    }

    /**
     * Todos los mensajes con datos del socio.
     *
     * Filtros admitidos: 'todos', 'no_leidos' o alguno de self::TIPOS.
     */
    public function getTodosConCliente(?string $filtro = null): array
    {
        $builder = $this->db->table('Comentario c')
            ->select('c.id_comentario, c.id_persona, c.tipo, c.mensaje, c.fecha_registro, c.leido,
                      p.nombre, p.apellido, p.dni')
            ->join('Persona p', 'p.id_persona = c.id_persona', 'inner');

        $filtro = strtolower(trim((string) $filtro));

        if ($filtro === 'no_leidos') {
            $builder->where('c.leido', 0);
        } elseif ($filtro !== '' && $filtro !== 'todos') {
            $tipo = $this->normalizarTipo($filtro);

            if ($tipo !== null) {
                $builder->where('c.tipo', $tipo);
            }
        }

        return $builder->orderBy('c.leido', 'ASC')
                       ->orderBy('c.fecha_registro', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Marca un mensaje como leído por la administración.
     */
    public function marcarComoLeido(int $idComentario): bool
    {
        if ($idComentario <= 0) {
            return false;
        }

        return $this->update($idComentario, ['leido' => 1]);
    }

    /**
     * Total de mensajes pendientes de lectura.
     */
    public function contarNoLeidos(): int
    {
        return $this->where('leido', 0)->countAllResults();
    }
}
