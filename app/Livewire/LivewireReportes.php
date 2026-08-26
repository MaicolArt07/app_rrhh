<?php

namespace App\Livewire;

use App\Exports\EmpleadosExport;
use App\Exports\PlanillaExport;
use App\Models\Empleado;
use App\Models\Planilla;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class LivewireReportes extends Component
{
    public $tab = 'empleados';

    public function setTab($tab)
    {
        $this->tab = $tab;
    }

    public function render()
    {
        return view('livewire.reportes.index', [
            'empleados' => Empleado::with(['persona', 'cargo', 'departamento'])->where('estado', true)->orderBy('codigo_empleado')->get(),
            'planillas' => Planilla::with(['gestion', 'mes', 'tipoPlanilla', 'estadoPlanilla'])->orderByDesc('id')->limit(20)->get(),
        ]);
    }

    public function exportarEmpleados()
    {
        return Excel::download(new EmpleadosExport(), 'empleados-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportarPlanilla($id)
    {
        return Excel::download(new PlanillaExport($id), 'planilla-' . $id . '-' . now()->format('Y-m-d') . '.xlsx');
    }
}
