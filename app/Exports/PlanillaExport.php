<?php

namespace App\Exports;

use App\Models\Planilla;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PlanillaExport implements FromCollection, WithHeadings
{
    public function __construct(protected int $idPlanilla)
    {
    }

    public function collection()
    {
        $planilla = Planilla::with('detalles.empleado.persona', 'detalles.cargo')->findOrFail($this->idPlanilla);

        return $planilla->detalles->map(fn ($d) => [
            'codigo_empleado' => $d->empleado->codigo_empleado,
            'nombre' => $d->empleado->persona?->nombreCompleto(),
            'cargo' => $d->cargo?->nombre,
            'sueldo_base' => $d->sueldo_base,
            'dias_trabajados' => $d->dias_trabajados,
            'horas_extra' => $d->horas_extra,
            'total_ingresos' => $d->total_ingresos,
            'total_deducciones' => $d->total_deducciones,
            'sueldo_liquido' => $d->sueldo_liquido,
            'estado_pago' => $d->estado_pago,
        ]);
    }

    public function headings(): array
    {
        return ['Código', 'Nombre', 'Cargo', 'Sueldo Base', 'Días Trabajados', 'Horas Extra', 'Total Ingresos', 'Total Deducciones', 'Sueldo Líquido', 'Estado de Pago'];
    }
}
