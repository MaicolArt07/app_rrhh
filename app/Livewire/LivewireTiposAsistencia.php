<?php

namespace App\Livewire;

use App\Models\TipoAsistencia;
use Livewire\Component;

/**
 * Catálogo de tipos de asistencia: se puede cambiar el nombre y el color.
 * Los códigos base no se crean ni se borran, porque las reglas dependen de ellos.
 */
class LivewireTiposAsistencia extends Component
{
    public $modalVisible = false;
    public $tipoId = null;
    public $codigo = '';
    public $nombre = '';
    public $color_hex = '#FFFFFF';
    public $orden = 0;
    public $estado = true;

    public function render()
    {
        return view('livewire.asistencia-biometrico.tipos', [
            'tipos' => TipoAsistencia::withCount('asistencias')->orderBy('orden')->get(),
        ]);
    }

    public function openModal($id)
    {
        $tipo = TipoAsistencia::findOrFail($id);
        $this->resetValidation();
        $this->tipoId = $tipo->id;
        $this->fill($tipo->only(['codigo', 'nombre', 'color_hex', 'orden', 'estado']));
        $this->modalVisible = true;
    }

    public function save()
    {
        abort_unless(auth()->user()->can('Editar Matriz Asistencia'), 403);

        $this->color_hex = strtoupper($this->color_hex);
        $this->validate([
            'nombre' => 'required|string|max:80',
            'color_hex' => ['required', 'regex:/^#[0-9A-F]{6}$/'],
            'orden' => 'required|integer|min:0|max:999',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'color_hex.regex' => 'El color debe tener el formato #RRGGBB.',
        ]);

        $tipo = TipoAsistencia::findOrFail($this->tipoId);
        $tipo->update([
            'nombre' => $this->nombre,
            'color_hex' => $this->color_hex,
            'orden' => $this->orden,
            // Solo los tipos que se eligen a mano se pueden desactivar del selector.
            'estado' => $tipo->seleccionable_manual ? (bool) $this->estado : true,
        ]);

        $this->modalVisible = false;
        $this->dispatch('toast', type: 'success', message: 'Tipo de asistencia actualizado.');
    }

    public function closeModal()
    {
        $this->modalVisible = false;
    }
}
