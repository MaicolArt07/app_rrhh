<?php

namespace App\Livewire;

use App\Models\Empresa;
use Illuminate\Validation\Rule;
use Livewire\Component;

class LivewireEmpresas extends Component
{
    public $modalVisible = false;

    public $empresaId = null;
    public $codigo = '';
    public $nombre = '';
    public $razon_social = '';
    public $nit = '';
    public $representante_legal = '';
    public $direccion = '';
    public $telefono = '';
    public $email = '';
    public $color_hex = '#6B7280';

    private const CAMPOS = ['codigo', 'nombre', 'razon_social', 'nit', 'representante_legal', 'direccion', 'telefono', 'email', 'color_hex'];

    public function render()
    {
        return view('livewire.empresas.index', [
            'empresas' => Empresa::withCount([
                'empleados',
                'empleados as activos_count' => fn ($q) => $q->where('estado', true),
                'contratos as indefinidos_count' => fn ($q) => $q->where('estado', true)->whereHas('tipoContrato', fn ($t) => $t->where('codigo', 'indefinido')),
                'contratos as prestacion_count' => fn ($q) => $q->where('estado', true)->whereHas('tipoContrato', fn ($t) => $t->where('codigo', 'prestacion')),
            ])->orderBy('nombre')->get(),
        ]);
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset(array_merge(['empresaId'], self::CAMPOS));

        if ($id) {
            $empresa = Empresa::findOrFail($id);
            $this->empresaId = $empresa->id;
            $this->fill(collect($empresa->only(self::CAMPOS))->map(fn ($v) => $v ?? '')->all());
        }

        $this->modalVisible = true;
    }

    public function save()
    {
        $this->codigo = strtoupper(trim($this->codigo));
        $this->color_hex = strtoupper($this->color_hex);

        $datos = $this->validate([
            'codigo' => ['required', 'string', 'max:10', Rule::unique('empresas', 'codigo')->ignore($this->empresaId)],
            'nombre' => ['required', 'string', 'max:150', Rule::unique('empresas', 'nombre')->ignore($this->empresaId)],
            'razon_social' => 'nullable|string|max:200',
            'nit' => 'nullable|string|max:30',
            'representante_legal' => 'nullable|string|max:150',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'color_hex' => ['required', 'regex:/^#[0-9A-F]{6}$/'],
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'Ya existe una empresa con ese código.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe una empresa con ese nombre.',
        ]);

        $datos = array_map(fn ($v) => $v === '' ? null : $v, $datos);

        try {
            if ($this->empresaId) {
                Empresa::findOrFail($this->empresaId)->update($datos);
                $message = 'Empresa actualizada correctamente.';
            } else {
                Empresa::create($datos + ['estado' => true]);
                $message = 'Empresa registrada correctamente.';
            }

            $this->dispatch('toast', type: 'success', message: $message);
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo guardar la empresa. Inténtalo nuevamente.');
        }
    }

    public function closeModal()
    {
        $this->modalVisible = false;
    }

    public function delete($id)
    {
        $empresa = Empresa::withCount(['empleados' => fn ($q) => $q->where('estado', true)])->findOrFail($id);

        if ($empresa->estado && $empresa->empleados_count > 0) {
            $this->dispatch('toast', type: 'error', message: "No se puede desactivar: {$empresa->nombre} tiene {$empresa->empleados_count} empleados activos.");

            return;
        }

        $empresa->update(['estado' => ! $empresa->estado]);
        $this->dispatch('toast', type: $empresa->estado ? 'success' : 'warning', message: $empresa->estado ? 'Empresa activada.' : 'Empresa desactivada.');
    }
}
