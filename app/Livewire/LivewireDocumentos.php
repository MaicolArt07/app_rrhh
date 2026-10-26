<?php

namespace App\Livewire;

use App\Livewire\Form\DocumentoEmpleadoForm;
use App\Models\DocumentoEmpleado;
use App\Models\Empleado;
use App\Models\Tipo;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class LivewireDocumentos extends Component
{
    use WithPagination;
    use WithFileUploads;

    public DocumentoEmpleadoForm $form;

    public $search = '';
    public $modalVisible = false;
    public $archivo;
    public bool $buscado = false;
    public ?int $viewingId = null;

    protected $paginationTheme = 'tailwind';

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

    public function editarDesdeDetalle($id)
    {
        $this->viewingId = null;
        $this->openModal($id);
    }

    public function render()
    {
        $documentos = null;

        if ($this->buscado) {
            $query = DocumentoEmpleado::query()->with(['empleado.persona', 'tipoDocumentoEmpleado']);

            if (!empty($this->search)) {
                $query->whereHas('empleado', function ($q) {
                    $q->where('codigo_empleado', 'like', "%{$this->search}%")
                      ->orWhereHas('persona', function ($p) {
                          $p->where('primer_nombre', 'like', "%{$this->search}%")
                            ->orWhere('primer_apellido', 'like', "%{$this->search}%");
                      });
                });
            }

            $documentos = $query->orderByDesc('id')->paginate(10);
        }

        return view('livewire.documentos.index', [
            'documentos' => $documentos,
            'documentoDetalle' => $this->viewingId
                ? DocumentoEmpleado::with(['empleado.persona', 'tipoDocumentoEmpleado'])->find($this->viewingId)
                : null,
            'empleados' => Empleado::with('persona')->where('estado', true)->get(),
            'tiposDocumentoEmpleado' => Tipo::categoria('tipo_documento_empleado')->activos()->orderBy('orden')->get(),
        ]);
    }

    public function openModal($id = null)
    {
        $this->archivo = null;

        if ($id) {
            $this->form->fillForm(DocumentoEmpleado::findOrFail($id));
        } else {
            $this->form->resetForm();
        }

        $this->modalVisible = true;
    }

    public function save()
    {
        $this->validate($this->form->rules(), $this->form->messages());
        $this->validate(['archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'], [], ['archivo' => 'archivo']);

        try {
            if ($this->form->id) {
                $documento = DocumentoEmpleado::findOrFail($this->form->id);
                $documento->update($this->form->toArray());
                $message = 'Documento actualizado correctamente.';
            } else {
                $documento = DocumentoEmpleado::create($this->form->toArray());
                $message = 'Documento registrado correctamente.';
            }

            if ($this->archivo) {
                $documento->addMedia($this->archivo->getRealPath())
                    ->usingFileName($this->archivo->getClientOriginalName())
                    ->toMediaCollection('archivo');
            }

            $this->dispatch('toast', type: 'success', message: $message);
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo guardar el documento. Inténtalo nuevamente.');
        }
    }

    public function closeModal()
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->form->resetForm();
        $this->archivo = null;
        $this->modalVisible = false;
    }

    public function delete($id)
    {
        try {
            $documento = DocumentoEmpleado::findOrFail($id);
            $documento->estado = !$documento->estado;
            $documento->save();

            $message = $documento->estado
                ? 'El documento fue activado correctamente.'
                : 'El documento fue desactivado correctamente.';

            $this->dispatch('toast', type: $documento->estado ? 'success' : 'warning', message: $message);
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo actualizar el estado del documento.');
        }
    }
}
