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

        $sql = "\xEF\xBB\xBF" . $this->generarScript();

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
     * Genera el script .sql completo: estructura (base, tablas, PK/UQ, FKs)
     * y datos (DELETE + INSERTs).
     */
    private function generarScript(): string
    {
        $cfg     = config('Database');
        $dbName  = $cfg->default['database'] ?? 'NewFitnessSystems';
        $dbNameL = str_replace("'", "''", $dbName);

        $tablas = $this->listarTablas();
        $fks    = $this->listarFks();

        $out  = "-- Backup NewFitnessSystems (estructura + datos)\n";
        $out .= "-- Generado: " . date('Y-m-d H:i:s') . "\n";
        $out .= "-- Base: " . $dbName . "\n\n";

        $out .= "IF DB_ID(N'" . $dbNameL . "') IS NULL\n";
        $out .= "BEGIN\n    CREATE DATABASE [" . $dbName . "];\nEND\nGO\n\n";
        $out .= "USE [" . $dbName . "];\nGO\n\n";
        $out .= "SET NOCOUNT ON;\nSET DATEFORMAT ymd;\nGO\n\n";

        $out .= "-- ============ ESTRUCTURA ============\n\n";
        foreach ($tablas as $tabla) {
            $out .= $this->ddlTabla($tabla) . "\nGO\n\n";
        }

        if (!empty($fks)) {
            $out .= "-- ============ CLAVES FORANEAS ============\n\n";
            foreach ($fks as $fk) {
                $out .= "IF NOT EXISTS (SELECT 1 FROM sys.foreign_keys WHERE name = N'"
                      . str_replace("'", "''", $fk['nombre']) . "')\n";
                $out .= "ALTER TABLE [dbo].[" . $fk['parent'] . "] WITH CHECK ADD CONSTRAINT ["
                      . $fk['nombre'] . "] FOREIGN KEY ([" . implode('], [', $fk['pcols'])
                      . "]) REFERENCES [dbo].[" . $fk['ref'] . "] ([" . implode('], [', $fk['rcols']) . "]);\n";
                $out .= "GO\n\n";
            }
        }

        $out .= "-- ============ DATOS ============\n\n";
        $out .= "BEGIN TRANSACTION;\nBEGIN TRY\n\n";

        $out .= "    DECLARE @sql_desactivar NVARCHAR(MAX) = N'';\n";
        $out .= "    SELECT @sql_desactivar += N'ALTER TABLE ' + QUOTENAME(OBJECT_SCHEMA_NAME(parent_object_id)) + N'.' + QUOTENAME(OBJECT_NAME(parent_object_id)) + N' NOCHECK CONSTRAINT ' + QUOTENAME(name) + N';' + CHAR(13)\n";
        $out .= "    FROM sys.foreign_keys;\n";
        $out .= "    EXEC sp_executesql @sql_desactivar;\n\n";

        foreach ($tablas as $tabla) {
            $out .= $this->datosTabla($tabla);
        }

        $out .= "    DECLARE @sql_activar NVARCHAR(MAX) = N'';\n";
        $out .= "    SELECT @sql_activar += N'ALTER TABLE ' + QUOTENAME(OBJECT_SCHEMA_NAME(parent_object_id)) + N'.' + QUOTENAME(OBJECT_NAME(parent_object_id)) + N' WITH CHECK CHECK CONSTRAINT ' + QUOTENAME(name) + N';' + CHAR(13)\n";
        $out .= "    FROM sys.foreign_keys;\n";
        $out .= "    EXEC sp_executesql @sql_activar;\n\n";

        $out .= "    COMMIT TRANSACTION;\n";
        $out .= "END TRY\nBEGIN CATCH\n";
        $out .= "    IF @@TRANCOUNT > 0 ROLLBACK TRANSACTION;\n";
        $out .= "    THROW;\n";
        $out .= "END CATCH\nGO\n";

        return $out;
    }

    /**
     * Genera el CREATE TABLE de una tabla (columnas, identidad, defaults, PK y UQ).
     */
    private function ddlTabla(string $tabla): string
    {
        $db  = \Config\Database::connect();
        $oid = "OBJECT_ID(N'dbo." . str_replace("'", "''", $tabla) . "', N'U')";

        $cols = $db->query(
            "SELECT c.name AS col, ty.name AS tipo, c.max_length AS maxlen, c.precision AS prec,"
            . " c.scale AS scale, c.is_nullable AS nullable, c.is_identity AS ident"
            . ' FROM sys.columns c JOIN sys.types ty ON ty.user_type_id = c.user_type_id'
            . ' WHERE c.object_id = ' . $oid . ' ORDER BY c.column_id'
        )->getResultArray();

        if (empty($cols)) {
            return '';
        }

        $defs = [];
        foreach ($db->query(
            'SELECT c.name AS col, dc.name AS cname, dc.definition AS def'
            . ' FROM sys.default_constraints dc'
            . ' JOIN sys.columns c ON c.object_id = dc.parent_object_id AND c.column_id = dc.parent_column_id'
            . ' WHERE dc.parent_object_id = ' . $oid
        )->getResultArray() as $d) {
            $defs[$d['col']] = $d;
        }

        $ident = [];
        foreach ($db->query(
            'SELECT c.name AS col, ic.seed_value AS seed, ic.increment_value AS inc'
            . ' FROM sys.identity_columns ic'
            . ' JOIN sys.columns c ON c.object_id = ic.object_id AND c.column_id = ic.column_id'
            . ' WHERE ic.object_id = ' . $oid
        )->getResultArray() as $i) {
            $ident[$i['col']] = $i;
        }

        $lineas = [];
        foreach ($cols as $c) {
            $linea = '    [' . $c['col'] . '] ' . $this->tipoSql($c);

            if (isset($ident[$c['col']])) {
                $linea .= ' IDENTITY(' . (int) $ident[$c['col']]['seed'] . ',' . (int) $ident[$c['col']]['inc'] . ')';
            }

            $linea .= ((int) $c['nullable'] === 1) ? ' NULL' : ' NOT NULL';

            if (!isset($ident[$c['col']]) && isset($defs[$c['col']])) {
                $linea .= ' CONSTRAINT [' . $defs[$c['col']]['cname'] . '] DEFAULT ' . $defs[$c['col']]['def'];
            }

            $lineas[] = $linea;
        }

        $cons = [];
        foreach ($db->query(
            'SELECT kc.name AS cname, kc.type AS ctype, i.type_desc AS idxtype, c.name AS col, ic.is_descending_key AS dsc'
            . ' FROM sys.key_constraints kc'
            . ' JOIN sys.indexes i ON i.object_id = kc.parent_object_id AND i.index_id = kc.unique_index_id'
            . ' JOIN sys.index_columns ic ON ic.object_id = i.object_id AND ic.index_id = i.index_id AND ic.key_ordinal > 0'
            . ' JOIN sys.columns c ON c.object_id = ic.object_id AND c.column_id = ic.column_id'
            . ' WHERE kc.parent_object_id = ' . $oid . ' ORDER BY kc.name, ic.key_ordinal'
        )->getResultArray() as $k) {
            $cons[$k['cname']]['type']    = $k['ctype'];
            $cons[$k['cname']]['idxtype'] = $k['idxtype'];
            $cons[$k['cname']]['cols'][]  = '[' . $k['col'] . ']' . ((int) $k['dsc'] === 1 ? ' DESC' : ' ASC');
        }
        foreach ($cons as $cname => $c) {
            $tipo = ($c['type'] === 'PK') ? 'PRIMARY KEY' : 'UNIQUE';
            $lineas[] = '    CONSTRAINT [' . $cname . '] ' . $tipo . ' ' . $c['idxtype']
                      . ' (' . implode(', ', $c['cols']) . ')';
        }

        return "IF OBJECT_ID(N'dbo." . $tabla . "', N'U') IS NULL\n"
             . "BEGIN\n"
             . "CREATE TABLE [dbo].[" . $tabla . "] (\n"
             . implode(",\n", $lineas) . "\n);\n"
             . "END";
    }

    /**
     * Genera DELETE + INSERTs de los datos de una tabla.
     */
    private function datosTabla(string $tabla): string
    {
        $db  = \Config\Database::connect();
        $oid = "OBJECT_ID(N'dbo." . str_replace("'", "''", $tabla) . "', N'U')";

        $cols = $db->query(
            'SELECT c.name AS col, ty.name AS tipo, c.is_identity AS ident'
            . ' FROM sys.columns c JOIN sys.types ty ON ty.user_type_id = c.user_type_id'
            . ' WHERE c.object_id = ' . $oid . ' ORDER BY c.column_id'
        )->getResultArray();

        if (empty($cols)) {
            return '';
        }

        $filas = $db->query('SELECT * FROM [dbo].[' . $tabla . ']')->getResultArray();

        $tieneIdentidad = false;
        $tipos = [];
        foreach ($cols as $c) {
            $tipos[$c['col']] = strtolower($c['tipo']);
            if ((int) $c['ident'] === 1) {
                $tieneIdentidad = true;
            }
        }

        $nombres = array_map(fn($c) => '[' . $c['col'] . ']', $cols);

        $out = "    DELETE FROM [dbo].[" . $tabla . "];\n";
        if ($tieneIdentidad) {
            $out .= "    SET IDENTITY_INSERT [dbo].[" . $tabla . "] ON;\n";
        }
        foreach ($filas as $r) {
            $vals = [];
            foreach ($cols as $c) {
                $col = $c['col'];
                $v   = $r[$col] ?? null;
                $tipo = $tipos[$col];

                if ($v === null) {
                    $vals[] = 'NULL';
                } elseif ($tipo === 'bit') {
                    $vals[] = ((int) $v) ? '1' : '0';
                } elseif ($this->esNumerico($tipo)) {
                    $vals[] = (string) $v;
                } else {
                    $vals[] = "N'" . str_replace("'", "''", (string) $v) . "'";
                }
            }
            $out .= "    INSERT INTO [dbo].[" . $tabla . "] (" . implode(', ', $nombres)
                  . ") VALUES (" . implode(', ', $vals) . ");\n";
        }
        if ($tieneIdentidad) {
            $out .= "    SET IDENTITY_INSERT [dbo].[" . $tabla . "] OFF;\n";
        }

        return $out . "\n";
    }

    /**
     * Devuelve las claves foráneas agrupadas por nombre.
     */
    private function listarFks(): array
    {
        $filas = \Config\Database::connect()->query(
            'SELECT fk.name AS nombre, tp.name AS parent, tr.name AS ref,'
            . ' fkc.constraint_column_id AS ord, cp.name AS pcol, cr.name AS rcol'
            . ' FROM sys.foreign_keys fk'
            . ' JOIN sys.tables tp ON tp.object_id = fk.parent_object_id'
            . ' JOIN sys.tables tr ON tr.object_id = fk.referenced_object_id'
            . ' JOIN sys.foreign_key_columns fkc ON fkc.constraint_object_id = fk.object_id'
            . ' JOIN sys.columns cp ON cp.object_id = fkc.parent_object_id AND cp.column_id = fkc.parent_column_id'
            . ' JOIN sys.columns cr ON cr.object_id = fkc.referenced_object_id AND cr.column_id = fkc.referenced_column_id'
            . " WHERE tp.name <> 'sysdiagrams'"
            . ' ORDER BY fk.name, fkc.constraint_column_id'
        )->getResultArray();

        $fks = [];
        foreach ($filas as $f) {
            $n = $f['nombre'];
            if (!isset($fks[$n])) {
                $fks[$n] = ['nombre' => $n, 'parent' => $f['parent'], 'ref' => $f['ref'], 'pcols' => [], 'rcols' => []];
            }
            $fks[$n]['pcols'][] = $f['pcol'];
            $fks[$n]['rcols'][] = $f['rcol'];
        }

        return array_values($fks);
    }

    /**
     * Devuelve el tipo SQL con su longitud/precisión cuando corresponde.
     */
    private function tipoSql(array $c): string
    {
        $t  = strtolower($c['tipo']);
        $ml = (int) $c['maxlen'];

        switch ($t) {
            case 'varchar':
            case 'char':
            case 'varbinary':
            case 'binary':
                return $t . '(' . ($ml === -1 ? 'max' : $ml) . ')';
            case 'nvarchar':
            case 'nchar':
                return $t . '(' . ($ml === -1 ? 'max' : intdiv($ml, 2)) . ')';
            case 'decimal':
            case 'numeric':
                return $t . '(' . (int) $c['prec'] . ',' . (int) $c['scale'] . ')';
            case 'time':
            case 'datetime2':
            case 'datetimeoffset':
                return $t . '(' . (int) $c['scale'] . ')';
            default:
                return $t;
        }
    }

    private function esNumerico(string $tipo): bool
    {
        return in_array($tipo, [
            'int', 'bigint', 'smallint', 'tinyint', 'decimal', 'numeric',
            'float', 'real', 'money', 'smallmoney',
        ], true);
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
