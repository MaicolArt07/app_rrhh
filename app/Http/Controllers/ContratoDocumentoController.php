<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Services\ContratoDocumentoService;

class ContratoDocumentoController extends Controller
{
    /** Descarga el contrato en PDF con el formato de su empresa y tipo de contrato. */
    public function descargar(Contrato $contrato, ContratoDocumentoService $documentos)
    {
        if (! $documentos->formato($contrato)) {
            abort(404, 'No hay un formato de contrato para esta empresa y tipo de contrato.');
        }

        if ($faltan = $documentos->faltantes($contrato)) {
            abort(422, 'Faltan datos para el contrato: '.implode(', ', $faltan).'. Complételos desde el módulo de contratos.');
        }

        [$nombre, $pdf] = $documentos->generar($contrato);

        return response()->streamDownload(fn () => print($pdf), $nombre, ['Content-Type' => 'application/pdf']);
    }
}
