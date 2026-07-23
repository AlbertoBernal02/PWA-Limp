<?php

namespace App\Support;

use Barryvdh\DomPDF\Facade\Pdf;

trait ExportaTabla
{
    /**
     * Descarga $filas (cada una ya en el mismo orden que $cabeceras) en CSV
     * (para Excel) o en PDF con una tabla simple.
     */
    protected function exportarTabla(string $formato, string $titulo, array $cabeceras, array $filas, string $nombreArchivo)
    {
        if ($formato === 'csv') {
            return response()->streamDownload(function () use ($cabeceras, $filas) {
                $salida = fopen('php://output', 'w');
                fputcsv($salida, $cabeceras, escape: '\\');

                foreach ($filas as $fila) {
                    fputcsv($salida, $fila, escape: '\\');
                }

                fclose($salida);
            }, "{$nombreArchivo}.csv", ['Content-Type' => 'text/csv']);
        }

        return Pdf::loadView('admin.exports.tabla', compact('titulo', 'cabeceras', 'filas'))
            ->download("{$nombreArchivo}.pdf");
    }
}
