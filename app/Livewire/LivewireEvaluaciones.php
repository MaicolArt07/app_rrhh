<?php

namespace App\Livewire;

use App\Livewire\Form\EvaluacionDesempenoForm;
use App\Models\Empleado;
use App\Models\EvaluacionDesempeno;
use App\Models\Gestion;
use App\Models\Tipo;
use Livewire\Component;
use Livewire\WithPagination;

class LivewireEvaluaciones extends Component
{
    use WithPagination;

    public EvaluacionDesempenoForm $form;

    public $modalVisible = false;
    public $criterios = [];
    public string $search = '';
    public bool $buscado = false;
    public ?int $viewingId = null;

    protected $paginationTheme = 'tailwind';

    protected $criteriosPorDefecto = ['Puntualidad', 'Calidad de trabajo', 'Trabajo en equipo', 'Iniciativa', 'Cumplimiento de metas'];

    public function mount()
    {
        $this->form->resetForm();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function buscar()
    {
        $this->resetPage();
        $this->buscado = true;
    }

    public function limpiarFiltros()
    {
        $this->reset('search');
        $this->buscado = false;
    }

    public function verDetalle($id)
    {
        $this->viewingId = $id;
    }

    public function cerrarDetalle()
    {
        $this->viewingId = null;
    }

    public function render()
    {
        $evaluaciones = null;

        if ($this->buscado) {
            $query = EvaluacionDesempeno::with(['empleado.persona', 'evaluador', 'gestion', 'resultadoEvaluacion']);

            if (!empty($this->search)) {
                $query->whereHas('empleado', function ($q) {
                    $q->where('codigo_empleado', 'like', "%{$this->search}%")
                        ->orWhereHas('persona', function ($p) {
                            $p->where('primer_nombre', 'like', "%{$this->search}%")
                                ->orWhere('primer_apellido', 'like', "%{$this->search}%");
                        });
                });
            }

            $evaluaciones = $query->orderByDesc('id')->paginate(10);
        }

        return view('livewire.evaluaciones.index', [
            'evaluaciones' => $evaluaciones,
            'evaluacionDetalle' => $this->viewingId
                ? EvaluacionDesempeno::with(['empleado.persona', 'evaluador', 'gestion', 'resultadoEvaluacion', 'detalles'])->find($this->viewingId)
                : null,
            'empleados' => Empleado::with('persona')->where('estado', true)->get(),
            'gestiones' => Gestion::orderByDesc('anio')->get(),
        ]);
    }

    public function openModal()
    {
        $this->form->resetForm();
        $this->criterios = collect($this->criteriosPorDefecto)
            ->map(fn ($c) => ['criterio' => $c, 'puntuacion' => ''])
            ->toArray();
        $this->modalVisible = true;
    }

    public function agregarCriterio()
    {
        $this->criterios[] = ['criterio' => '', 'puntuacion' => ''];
    }

    public function quitarCriterio($index)
    {
        unset($this->criterios[$index]);
        $this->criterios = array_values($this->criterios);
    }

    public function calcularPuntuacionTotal(): float
    {
        $validas = collect($this->criterios)->pluck('puntuacion')->filter(fn ($p) => is_numeric($p));

        return $validas->isEmpty() ? 0 : round($validas->avg(), 2);
    }

    public function save()
    {
        $this->validate($this->form->rules(), $this->form->messages());
        $this->validate([
            'criterios.*.criterio' => 'required|string|max:150',
            'criterios.*.puntuacion' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $puntuacionTotal = $this->calcularPuntuacionTotal();
            $resultado = match (true) {
                $puntuacionTotal >= 90 => 'excelente',
                $puntuacionTotal >= 75 => 'bueno',
                $puntuacionTotal >= 60 => 'regular',
                default => 'deficiente',
            };
            $tipoResultado = Tipo::categoria('resultado_evaluacion')->where('codigo', $resultado)->firstOrFail();

            $evaluacion = EvaluacionDesempeno::create(array_merge($this->form->toArray(), [
                'id_evaluador' => auth()->id(),
                'puntuacion_total' => $puntuacionTotal,
                'id_resultado_evaluacion' => $tipoResultado->id,
            ]));

            foreach ($this->criterios as $criterio) {
                $evaluacion->detalles()->create($criterio);
            }

            $this->dispatch('toast', type: 'success', message: 'Evaluación registrada correctamente.');
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo registrar la evaluación. Inténtalo nuevamente.');
        }
    }

    public function closeModal()
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->form->resetForm();
        $this->modalVisible = false;
    }
}
