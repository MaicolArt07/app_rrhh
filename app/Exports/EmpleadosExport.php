<?php

namespace App\Exports;

use App\Models\Empleado;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmpleadosExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Empleado::with(['persona', 'empresa', 'cargo', 'area', 'estadoEmpleado', 'contratoVigente.tipoContrato'])
            ->orderBy('codigo_empleado')
            ->get()
            ->map(fn ($e) => [
                'codigo' => $e->codigo_empleado,
                'nombre' => $e->persona?->nombreCompleto(),
                'documento' => $e->persona?->numero_documento,
                'empresa' => $e->empresa?->nombre,
                'contrato' => $e->contratoVigente?->tipoContrato?->nombre,
                'cargo' => $e->cargo?->nombre,
                'area' => $e->area?->nombre,
                'fecha_ingreso' => $e->fecha_ingreso?->format('Y-m-d'),
                'sueldo_actual' => $e->sueldo_actual,
                'estado' => $e->estadoEmpleado?->nombre,
            ]);
    }

    public function headings(): array
    {
        return ['Código', 'Nombre', 'Documento', 'Empresa', 'Contrato', 'Cargo', 'Área', 'Fecha Ingreso', 'Sueldo Actual', 'Estado'];
    }
}
