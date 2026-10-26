<?php

namespace App\Livewire;

use App\Livewire\Form\SaldoVacacionForm;
use App\Models\Empleado;
use App\Models\Gestion;
use App\Models\SaldoVacacion;
use Livewire\Component;
use Livewire\WithPagination;

class LivewireSaldosVacaciones extends Component
{
    use WithPagination;

    public SaldoVacacionForm $form;

    public $gestionFiltro = '';
    public string $search = '';
    public $modalVisible = false;
    public bool $buscado = false;

    public ?int $viewingId = null;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->form->resetForm();
        $this->gestionFiltro = Gestion::where('anio', now()->year)->value('id') ?? '';
    }

    public function updatingGestionFiltro()
    {
        $this->resetPage();
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
        $this->reset('search', 'gestionFiltro');
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

    public function editarDesdeDetalle($id)
    {
        $this->viewingId = null;
        $this->openModal($id);
    }

    public function render()
    {
        $saldos = null;

        if ($this->buscado) {
            $query = SaldoVacacion::query()->with(['empleado.persona', 'gestion']);

            if (!empty($this->gestionFiltro)) {
                $query->where('id_gestion', $this->gestionFiltro);
            }

            if (!empty($this->search)) {
                $query->whereHas('empleado', function ($q) {
                    $q->where('codigo_empleado', 'like', "%{$this->search}%")
                        ->orWhereHas('persona', function ($p) {
                            $p->where('primer_nombre', 'like', "%{$this->search}%")
                                ->orWhere('primer_apellido', 'like', "%{$this->search}%");
                        });
                });
            }

            $saldos = $query->orderByDesc('id')->paginate(10);
        }

        return view('livewire.saldos-vacaciones.index', [
            'saldos' => $saldos,
            'saldoDetalle' => $this->viewingId
                ? SaldoVacacion::with(['empleado.persona', 'gestion'])->find($this->viewingId)
                : null,
            'empleados' => Empleado::with('persona')->where('estado', true)->get(),
            'gestiones' => Gestion::orderByDesc('anio')->get(),
        ]);
    }

    public function openModal($id = null)
    {
        if ($id) {
            $this->form->fillForm(SaldoVacacion::findOrFail($id));
        } else {
            $this->form->resetForm();
            if ($this->gestionFiltro) {
                $this->form->id_gestion = $this->gestionFiltro;
            }
        }

        $this->modalVisible = true;
    }

    public function save()
    {
        $this->validate($this->form->rules(), $this->form->messages());

        try {
            $diasDisponibles = round($this->form->dias_generados - $this->form->dias_tomados, 2);
            $data = array_merge($this->form->toArray(), [
                'dias_disponibles' => $diasDisponibles,
                'fecha_ultimo_calculo' => now()->format('Y-m-d'),
            ]);

            if ($this->form->id) {
                SaldoVacacion::findOrFail($this->form->id)->update($data);
                $message = 'Saldo de vacaciones actualizado correctamente.';
            } else {
                SaldoVacacion::create($data);
                $message = 'Saldo de vacaciones registrado correctamente.';
            }

            $this->dispatch('toast', type: 'success', message: $message);
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo guardar el saldo de vacaciones. Inténtalo nuevamente.');
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
