<?php

if (!function_exists('nfs_csv')) {
    /**
     * Genera una respuesta de descarga en formato CSV (compatible con Excel).
     * Usa separador ';' y BOM UTF-8 para que Excel respete los acentos.
     *
     * @param string $nombreArchivo Nombre del archivo (ej: cuotas.csv)
     * @param array  $encabezados   Lista de títulos de columna
     * @param array  $filas         Lista de filas (cada fila es un array de valores)
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    function nfs_csv(string $nombreArchivo, array $encabezados, array $filas)
    {
        $escapar = static function ($valor): string {
            $valor = (string) $valor;
            $valor = str_replace('"', '""', $valor);

            if (preg_match('/[";\r\n]/', $valor)) {
                $valor = '"' . $valor . '"';
            }

            return $valor;
        };

        $csv = "\xEF\xBB\xBF";
        $csv .= implode(';', array_map($escapar, $encabezados)) . "\r\n";

        foreach ($filas as $fila) {
            $csv .= implode(';', array_map($escapar, array_values((array) $fila))) . "\r\n";
        }

        return service('response')
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $nombreArchivo . '"')
            ->setBody($csv);
    }
}

if (!function_exists('nfs_dinero')) {
    /**
     * Formatea un número como importe con separador de miles (sin símbolo).
     */
    function nfs_dinero($valor): string
    {
        return number_format((float) $valor, 0, ',', '.');
    }
}
