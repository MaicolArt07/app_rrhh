<?php

namespace App\Livewire;

use App\Livewire\Form\PermisoLaboralForm;
use App\Models\Empleado;
use App\Models\PermisoLaboral;
use App\Models\Tipo;
use App\Services\NotificacionService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class LivewirePermisosLaborales extends Component
{
    use WithPagination;
    use WithFileUploads;

    public PermisoLaboralForm $form;

    public $estadoFiltro = 'pendiente';
    public string $search = '';
    public $modalVisible = false;
    public $documentoSoporte;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->form->resetForm();
    }

    public function updatingEstadoFiltro()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = PermisoLaboral::query()->with(['empleado.persona', 'tipoPermiso', 'aprobadoPor']);

        if (!empty($this->estadoFiltro)) {
            $query->where('estado_solicitud', $this->estadoFiltro);
        }

        if (!empty($this->search)) {
            $query->whereHas('empleado', function ($q) {
                $q->where('codigo_empleado', 'like', "%{$this->search}%")
                    ->orWhereHas('persona', function ($p) {
                        $p->where('nombres', 'like', "%{$this->search}%")
                            ->orWhere('apellidos', 'like', "%{$this->search}%");
                    });
            });
        }

        return view('livewire.permisos-laborales.index', [
            'permisos' => $query->orderByDesc('id')->paginate(10),
            'empleados' => Empleado::with('persona')->where('estado', true)->get(),
            'tiposPermiso' => Tipo::categoria('tipo_permiso')->activos()->orderBy('orden')->get(),
        ]);
    }

    public function openModal($id = null)
    {
        $this->documentoSoporte = null;

        if ($id) {
            $this->form->fillForm(PermisoLaboral::findOrFail($id));
        } else {
            $this->form->resetForm();
        }

        $this->modalVisible = true;
    }

    public function save()
    {
        $this->validate($this->form->rules(), $this->form->messages());
        $this->validate(['documentoSoporte' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'], [], ['documentoSoporte' => 'documento de soporte']);

        try {
            $inicio = Carbon::parse($this->form->fecha_inicio);
            $fin = Carbon::parse($this->form->fecha_fin);
            $dias = $inicio->diffInDays($fin) + 1;

            $data = array_merge($this->form->toArray(), ['dias_solicitados' => $dias]);

            if ($this->form->id) {
                $permiso = PermisoLaboral::findOrFail($this->form->id);
                $permiso->update($data);
                $message = 'Permiso actualizado correctamente.';
            } else {
                $data['estado_solicitud'] = 'pendiente';
                $permiso = PermisoLaboral::create($data);
                $message = 'Permiso solicitado correctamente.';

                $empleadoNombre = $permiso->empleado->persona?->nombreCompleto() ?? $permiso->empleado->codigo_empleado;
                app(NotificacionService::class)->notificarUsuariosConPermiso(
                    'Aprobar Permisos Laborales',
                    'permiso_solicitado',
                    'Nuevo permiso solicitado',
                    "{$empleadoNombre} solicitó un permiso del {$permiso->fecha_inicio->format('d/m/Y')} al {$permiso->fecha_fin->format('d/m/Y')}.",
                    $permiso
                );
            }

            if ($this->documentoSoporte) {
                $permiso->addMedia($this->documentoSoporte->getRealPath())
                    ->usingFileName($this->documentoSoporte->getClientOriginalName())
                    ->toMediaCollection('documento_soporte');
            }

            $this->dispatch('toast', type: 'success', message: $message);
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo guardar el permiso. Inténtalo nuevamente.');
        }
    }

    public function closeModal()
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->form->resetForm();
        $this->documentoSoporte = null;
        $this->modalVisible = false;
    }

    public function aprobar($id)
    {
        try {
            $permiso = PermisoLaboral::findOrFail($id);
            $permiso->estado_solicitud = 'aprobado';
            $permiso->id_aprobado_por = auth()->id();
            $permiso->save();

            $usuarioEmpleado = $permiso->empleado->persona?->user;
            if ($usuarioEmpleado) {
                app(NotificacionService::class)->crear(
                    $usuarioEmpleado,
                    'permiso_aprobado',
                    'Permiso aprobado',
                    "Tu permiso del {$permiso->fecha_inicio->format('d/m/Y')} al {$permiso->fecha_fin->format('d/m/Y')} fue aprobado.",
                    $permiso
                );
            }

            $this->dispatch('toast', type: 'success', message: 'Permiso aprobado correctamente.');
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo aprobar el permiso.');
        }
    }

    public function rechazar($id)
    {
        try {
            $permiso = PermisoLaboral::findOrFail($id);
            $permiso->estado_solicitud = 'rechazado';
            $permiso->id_aprobado_por = auth()->id();
            $permiso->save();

            $this->dispatch('toast', type: 'warning', message: 'Permiso rechazado.');
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo rechazar el permiso.');
        }
    }

    public function delete($id)
    {
        try {
            $permiso = PermisoLaboral::findOrFail($id);
            $permiso->estado = !$permiso->estado;
            $permiso->save();

            $this->dispatch('toast', type: $permiso->estado ? 'success' : 'warning', message: 'Estado del permiso actualizado.');
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo actualizar el estado del permiso.');
        }
    }
}
