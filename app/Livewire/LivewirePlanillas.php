<?php

namespace App\Livewire;

use App\Models\Gestion;
use App\Models\Mes;
use App\Models\Planilla;
use App\Models\Tipo;
use App\Services\PlanillaService;
use Livewire\Component;
use Livewire\WithPagination;

class LivewirePlanillas extends Component
{
    use WithPagination;

    public $modalVisible = false;
    public $verDetalleId = null;

    public $id_gestion = '';
    public $id_mes = '';
    public $id_tipo_planilla = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';

    public string $search = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Planilla::with(['gestion', 'mes', 'tipoPlanilla', 'estadoPlanilla', 'generadoPor']);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->whereHas('gestion', fn ($g) => $g->where('anio', 'like', "%{$this->search}%"))
                    ->orWhereHas('mes', fn ($m) => $m->where('nombre', 'like', "%{$this->search}%"))
                    ->orWhereHas('tipoPlanilla', fn ($t) => $t->where('nombre', 'like', "%{$this->search}%"));
            });
        }

        return view('livewire.planillas.index', [
            'planillas' => $query->orderByDesc('id')->paginate(10),
            'gestiones' => Gestion::orderByDesc('anio')->get(),
            'meses' => Mes::orderBy('numero')->get(),
            'tiposPlanilla' => Tipo::categoria('tipo_planilla')->activos()->orderBy('orden')->get(),
            'detallePlanilla' => $this->verDetalleId
                ? Planilla::with(['detalles.empleado.persona', 'detalles.cargo', 'detalles.conceptos.conceptoPago', 'estadoPlanilla'])
                    ->find($this->verDetalleId)
                : null,
        ]);
    }

    public function openModal()
    {
        $this->reset(['id_gestion', 'id_mes', 'id_tipo_planilla', 'fecha_inicio', 'fecha_fin']);
        $this->modalVisible = true;
    }

    public function closeModal()
    {
        $this->resetValidation();
        $this->modalVisible = false;
    }

    public function generar()
    {
        $this->validate([
            'id_gestion' => 'required|exists:gestiones,id',
            'id_tipo_planilla' => 'required|exists:tipos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        try {
            $planilla = app(PlanillaService::class)->generar([
                'id_gestion' => $this->id_gestion,
                'id_mes' => $this->id_mes ?: null,
                'id_tipo_planilla' => $this->id_tipo_planilla,
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_fin' => $this->fecha_fin,
                'id_generado_por' => auth()->id(),
            ]);

            $this->dispatch('toast', type: 'success', message: 'Planilla generada con ' . $planilla->detalles->count() . ' empleados.');
            $this->modalVisible = false;
            $this->verDetalleId = $planilla->id;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo generar la planilla: ' . $e->getMessage());
        }
    }

    public function verDetalle($id)
    {
        $this->verDetalleId = $id;
    }

    public function cerrarDetalle()
    {
        $this->verDetalleId = null;
    }

    public function avanzarEstado($id, $codigoEstado)
    {
        try {
            app(PlanillaService::class)->avanzarEstado(Planilla::findOrFail($id), $codigoEstado);
            $this->dispatch('toast', type: 'success', message: 'Estado de la planilla actualizado.');
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo actualizar el estado de la planilla.');
        }
    }
}
