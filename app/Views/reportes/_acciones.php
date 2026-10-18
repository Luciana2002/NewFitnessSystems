<?php
/**
 * Barra de acciones para los reportes: exportar a Excel (CSV) e imprimir/PDF.
 *
 * @var string $exportUrl  URL de exportación (vacío = no mostrar)
 */
$exportUrl = $exportUrl ?? '';
?>
<div class="reporte-acciones no-print">
    <?php if ($exportUrl !== ''): ?>
        <a href="<?= $exportUrl ?>" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel"></i> Exportar Excel
        </a>
    <?php endif; ?>

    <button type="button" class="btn btn-outline-light btn-sm" onclick="window.print()">
        <i class="bi bi-printer"></i> Imprimir / PDF
    </button>
</div>
