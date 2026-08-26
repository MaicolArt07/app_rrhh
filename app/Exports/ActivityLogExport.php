<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Spatie\Activitylog\Models\Activity;

class ActivityLogExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Activity::with('causer')
            ->orderByDesc('created_at')
            ->limit(5000)
            ->get()
            ->map(fn ($a) => [
                'fecha' => $a->created_at->format('Y-m-d H:i:s'),
                'usuario' => $a->causer?->name ?? 'Sistema',
                'accion' => $a->description,
                'modelo' => class_basename($a->subject_type ?? ''),
                'modelo_id' => $a->subject_id,
                'cambios' => json_encode($a->properties, JSON_UNESCAPED_UNICODE),
            ]);
    }

    public function headings(): array
    {
        return ['Fecha', 'Usuario', 'Acción', 'Modelo', 'ID', 'Cambios'];
    }
}
