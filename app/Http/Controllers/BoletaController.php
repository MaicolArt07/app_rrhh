<?php

namespace App\Http\Controllers;

use App\Models\PlanillaDetalle;
use Barryvdh\DomPDF\Facade\Pdf;

class BoletaController extends Controller
{
    public function download(PlanillaDetalle $detalle)
    {
        $detalle->load(['empleado.persona', 'cargo', 'planilla.gestion', 'planilla.mes', 'conceptos.conceptoPago']);

        $pdf = Pdf::loadView('pdf.boleta', ['detalle' => $detalle]);

        return $pdf->download("boleta-{$detalle->empleado->codigo_empleado}-{$detalle->id_planilla}.pdf");
    }
}
