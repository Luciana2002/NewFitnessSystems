<?php

namespace App\Controllers;

class BackupController extends \App\Controllers\BaseController
{
    /** Tamaño máximo del archivo subido (MB). */
    private const MAX_MB = 20;

    private ?array $cacheTablas = null;

    public function index()
    {
        if ($redir = $this->soloAdmin()) {
            return $redir;
        }

        $data = [
            'hoy'        => date('Y-m-d'),
            'tablas'     => $this->listarTablas(),
            'filas'      => $this->conteoFilas(),
            'success'    => session()->getFlashdata('success'),
            'error'      => session()->getFlashdata('error'),
        ];

        return view('front/header')
             . view('front/navbar')
             . view('backup/index', $data)
             . view('front/footer');
    }

    /**
     * Descarga un .sql con todos los datos de la base.
     */
    public function descargar()
    {
        if ($redir = $this->soloAdmin()) {
            return $redir;
        }

        $sql = $this->generarScript();

        $nombre = 'backup_newfitnesssystems_' . date('Y-m-d') . '.sql';
        $this->registrar('DESCARGA', 'Backup', null, 'Backup de la base descargado (' . $nombre . ')');

        header('Content-Type: application/sql; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $nombre . '"');
        header('Content-Length: ' . strlen($sql));
        echo $sql;
        exit;
    }

    /**
     * Importa un .sql previamente descargado (reemplaza los datos).
     */
    public function importar()
    {
        if ($redir = $this->soloAdmin()) {
            return $redir;
        }

        $archivo = $this->request->getFile('archivo');

        if (!$archivo || !$archivo->isValid() || $archivo->hasMoved()) {
            return redirect()->back()->with('error', 'No se recibió ningún archivo válido.');
        }

        $nombreOriginal = $archivo->getClientName();

        if (strtolower((string) $archivo->getClientExtension()) !== 'sql') {
            return redirect()->back()->with('error', 'El archivo debe tener extensión .sql.');
        }

        if ($archivo->getSize() > self::MAX_MB * 1024 * 1024) {
            return redirect()->back()->with('error', 'El archivo supera el máximo de ' . self::MAX_MB . ' MB.');
        }

        $dir     = rtrim(WRITEPATH, '\\/') . DIRECTORY_SEPARATOR . 'uploads';
        $nombre  = 'nfs_backup_' . uniqid() . '.sql';
        $destino = $dir . DIRECTORY_SEPARATOR . $nombre;
        $archivo->move($dir, $nombre, true);

        [$rc, $salida] = $this->ejecutarSqlcmd('-i "' . $destino . '" -b');
        @unlink($destino);

        if ($rc !== 0) {
            $detalle = trim($salida);
            if (strlen($detalle) > 400) {
                $detalle = substr($detalle, 0, 400) . '…';
            }
            $this->registrar('IMPORTAR', 'Backup', null, 'Falló la importación del backup: ' . str_replace(['[', ']', '"'], '', $detalle));
            return redirect()->back()->with('error', 'Error al importar el backup: ' . $detalle);
        }

        $this->registrar('IMPORTAR', 'Backup', null, 'Backup importado correctamente (' . $nombreOriginal . ')');
        return redirect()->back()->with('success', 'Backup importado correctamente.');
    }

    /* ------------------------------------------------------------------ */

    private function soloAdmin()
    {
        if ((int) session()->get('id_rol') !== 1) {
            return redirect()->to('/usuario_logueado');
        }
        return null;
    }

    private function listarTablas(): array
    {
        if ($this->cacheTablas !== null) {
            return $this->cacheTablas;
        }

        [$rc, $salida] = $this->ejecutarSqlcmd(
            '-Q "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = \'BASE TABLE\' AND TABLE_NAME <> \'sysdiagrams\' ORDER BY TABLE_NAME"'
        );

        $tablas = [];
        foreach (preg_split('/\r?\n/', $salida) as $linea) {
            $linea = trim($linea);
            if ($linea !== '' && !preg_match('/filas afectadas/i', $linea) && !preg_match('/^\s*Mensaje\s+\d+/', $linea)) {
                $tablas[] = $linea;
            }
        }

        return $this->cacheTablas = $tablas;
    }

    private function conteoFilas(): array
    {
        $tablas = $this->listarTablas();
        if (empty($tablas)) {
            return [];
        }

        $union = [];
        foreach ($tablas as $t) {
            $union[] = "SELECT '" . str_replace("'", "''", $t) . "' AS tabla, COUNT(*) AS filas FROM [" . $t . "]";
        }
        $q = implode(' UNION ALL ', $union);

        [$rc, $salida] = $this->ejecutarSqlcmd('-Q "' . str_replace('"', '\"', $q) . '"');

        $res = [];
        foreach (preg_split('/\r?\n/', $salida) as $linea) {
            if (preg_match('/^(\S.*?)\s+(\d+)\s*$/', $linea, $m)) {
                $res[trim($m[1])] = (int) $m[2];
            }
        }
        return $res;
    }

    /**
     * Genera el script .sql con esquema de datos (DELETE + INSERTs).
     */
    private function generarScript(): string
    {
        $db = config('Database')->default;

        $out  = "-- Backup NewFitnessSystems\n";
        $out .= "-- Generado: " . date('Y-m-d H:i:s') . "\n";
        $out .= "-- Base: " . ($db['database'] ?? '') . "\n\n";
        $out .= "SET NOCOUNT ON;\nSET DATEFORMAT ymd;\nGO\n\n";
        $out .= "BEGIN TRANSACTION;\nBEGIN TRY\n";

        $tablas = $this->listarTablas();

        // Orden alfabético al insertar; deshabilitamos FKs para no depender del orden.
        $out .= "    EXEC sp_MSforeachtable 'ALTER TABLE ? NOCHECK CONSTRAINT ALL';\n\n";

        foreach ($tablas as $tabla) {
            $out .= $this->scriptTabla($tabla);
        }

        $out .= "    EXEC sp_MSforeachtable 'ALTER TABLE ? WITH CHECK CHECK CONSTRAINT ALL';\n";
        $out .= "    COMMIT TRANSACTION;\n";
        $out .= "END TRY\nBEGIN CATCH\n";
        $out .= "    IF @@TRANCOUNT > 0 ROLLBACK TRANSACTION;\n";
        $out .= "    THROW;\n";
        $out .= "END CATCH\nGO\n";

        return $out;
    }

    private function scriptTabla(string $tabla): string
    {
        $db = \Config\Database::connect();

        $registros = $db->query(
            "SELECT COLUMN_NAME, DATA_TYPE, COLUMNPROPERTY(OBJECT_ID('" . str_replace("'", "''", $tabla) . "'), COLUMN_NAME, 'IsIdentity') AS es_ident"
            . ' FROM INFORMATION_SCHEMA.COLUMNS'
            . " WHERE TABLE_NAME = '" . str_replace("'", "''", $tabla) . "' ORDER BY ORDINAL_POSITION"
        )->getResultArray();

        $cols = [];
        $numericos = [];
        $tieneIdentidad = false;
        foreach ($registros as $r) {
            $cols[] = $r['COLUMN_NAME'];
            $numericos[] = in_array(strtolower($r['DATA_TYPE']), [
                'int', 'bigint', 'smallint', 'tinyint', 'decimal', 'numeric',
                'float', 'real', 'money', 'smallmoney', 'bit',
            ], true);
            if ((int) ($r['es_ident'] ?? 0) === 1) {
                $tieneIdentidad = true;
            }
        }

        if (empty($cols)) {
            return '';
        }

        $filas = $this->obtenerFilas($tabla, $cols);

        $out = "DELETE FROM [" . $tabla . "];\n";
        if ($tieneIdentidad) {
            $out .= "SET IDENTITY_INSERT [" . $tabla . "] ON;\n";
        }
        foreach ($filas as $r) {
            $vals = [];
            foreach ($cols as $i => $c) {
                $v = $r[$c] ?? null;
                if ($v === null) {
                    $vals[] = 'NULL';
                } elseif ($numericos[$i]) {
                    $vals[] = (string) $v;
                } else {
                    $vals[] = "'" . str_replace("'", "''", (string) $v) . "'";
                }
            }
            $out .= "INSERT INTO [" . $tabla . "] ("
                  . implode(', ', array_map(fn($c) => '[' . $c . ']', $cols))
                  . ") VALUES (" . implode(', ', $vals) . ");\n";
        }
        if ($tieneIdentidad) {
            $out .= "SET IDENTITY_INSERT [" . $tabla . "] OFF;\n";
        }
        return $out . "\n";
    }

    private function obtenerFilas(string $tabla, array $cols): array
    {
        return \Config\Database::connect()->query('SELECT * FROM [' . $tabla . ']')->getResultArray();
    }

    /**
     * Ejecuta sqlcmd y devuelve [codigo_salida, salida].
     * Captura la salida a un archivo Unicode (-u) para preservar acentos.
     */
    private function ejecutarSqlcmd(string $extra): array
    {
        $exe = 'C:\Program Files\Microsoft SQL Server\Client SDK\ODBC\170\Tools\Binn\SQLCMD.EXE';
        $out = rtrim(sys_get_temp_dir(), '\\/') . DIRECTORY_SEPARATOR . 'nfs_sqlcmd_' . uniqid() . '.txt';

        $cmd = '"' . $exe . '" -S localhost\SQLEXPRESS -E -d NewFitnessSystems -f 65001 -W -h -1 -u -o "' . $out . '" ' . $extra;

        $ignorar = [];
        $rc = 0;
        exec($cmd, $ignorar, $rc);

        $salida = '';
        if (is_file($out)) {
            $raw = (string) file_get_contents($out);
            $salida = (string) mb_convert_encoding($raw, 'UTF-8', 'UTF-16LE');
            $salida = ltrim($salida, "\u{FEFF}");
            @unlink($out);
        }

        return [$rc, $this->aUtf8($salida)];
    }

    /**
     * Fuerza UTF-8 válido (sqlcmd puede devolver errores en la codepage de consola).
     */
    private function aUtf8(string $texto): string
    {
        if (mb_check_encoding($texto, 'UTF-8')) {
            return $texto;
        }
        return (string) @iconv('UTF-8', 'UTF-8//IGNORE', $texto);
    }

    private function registrar(string $accion, string $entidad, $id, string $detalle): void
    {
        if (!function_exists('nfs_log')) {
            return;
        }
        nfs_log($accion, $entidad, $id, $detalle);
    }
}
