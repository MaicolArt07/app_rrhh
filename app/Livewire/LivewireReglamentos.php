<?php

namespace App\Livewire;

use App\Livewire\Form\ReglamentoForm;
use App\Models\Empleado;
use App\Models\Reglamento;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class LivewireReglamentos extends Component
{
    use WithPagination;
    use WithFileUploads;

    public ReglamentoForm $form;

    public $modalVisible = false;
    public $lecturasVisible = false;
    public $reglamentoLecturasId = null;
    public $documento;
    public string $search = '';

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->form->resetForm();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Reglamento::withCount('lecturas');

        if (!empty($this->search)) {
            $query->where('titulo', 'like', "%{$this->search}%");
        }

        return view('livewire.reglamentos.index', [
            'reglamentos' => $query->orderByDesc('vigente_desde')->paginate(10),
            'reglamentoLecturas' => $this->reglamentoLecturasId
                ? Reglamento::with('lecturas.empleado.persona')->find($this->reglamentoLecturasId)
                : null,
            'empleadosSinLeer' => $this->reglamentoLecturasId
                ? Empleado::with('persona')->where('estado', true)
                    ->whereDoesntHave('reglamentoLecturas', fn ($q) => $q->where('id_reglamento', $this->reglamentoLecturasId))
                    ->get()
                : collect(),
        ]);
    }

    public function openModal($id = null)
    {
        $this->documento = null;

        if ($id) {
            $this->form->fillForm(Reglamento::findOrFail($id));
        } else {
            $this->form->resetForm();
        }

        $this->modalVisible = true;
    }

    public function save()
    {
        $this->validate($this->form->rules(), $this->form->messages());
        $this->validate(['documento' => 'nullable|file|mimes:pdf|max:10240'], [], ['documento' => 'documento']);

        try {
            if ($this->form->id) {
                $reglamento = Reglamento::findOrFail($this->form->id);
                $reglamento->update($this->form->toArray());
                $message = 'Reglamento actualizado correctamente.';
            } else {
                $reglamento = Reglamento::create($this->form->toArray());
                $message = 'Reglamento registrado correctamente.';
            }

            if ($this->documento) {
                $reglamento->addMedia($this->documento->getRealPath())
                    ->usingFileName($this->documento->getClientOriginalName())
                    ->toMediaCollection('documento');
            }

            $this->dispatch('toast', type: 'success', message: $message);
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo guardar el reglamento. Inténtalo nuevamente.');
        }
    }

    public function closeModal()
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->form->resetForm();
        $this->documento = null;
        $this->modalVisible = false;
    }

    public function verLecturas($id)
    {
        $this->reglamentoLecturasId = $id;
        $this->lecturasVisible = true;
    }

    public function cerrarLecturas()
    {
        $this->lecturasVisible = false;
        $this->reglamentoLecturasId = null;
    }

    public function registrarLectura($idEmpleado)
    {
        try {
            $reglamento = Reglamento::findOrFail($this->reglamentoLecturasId);
            $reglamento->lecturas()->firstOrCreate(
                ['id_empleado' => $idEmpleado],
                ['fecha_lectura' => now(), 'ip_confirmacion' => request()->ip()]
            );

            $this->dispatch('toast', type: 'success', message: 'Lectura registrada.');
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo registrar la lectura.');
        }
    }

    public function delete($id)
    {
        try {
            $reglamento = Reglamento::findOrFail($id);
            $reglamento->estado = !$reglamento->estado;
            $reglamento->save();

            $this->dispatch('toast', type: $reglamento->estado ? 'success' : 'warning', message: 'Estado del reglamento actualizado.');
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo actualizar el estado del reglamento.');
        }
    }
}
