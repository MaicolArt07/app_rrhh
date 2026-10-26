<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use App\Traits\PermissionsFormTrait;

class LivewirePermissions extends Component
{
    use WithPagination, PermissionsFormTrait;

    public $modalVisible = false;
    public $search = '';
    public bool $buscado = false;
    public ?int $viewingId = null;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->resetForm();
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
        $permissions = null;

        if ($this->buscado) {
            $query = Permission::query();

            if (!empty($this->search)) {
                $query->where('name', 'like', '%' . $this->search . '%');
            }

            $permissions = $query->orderBy('name')->paginate(5);
        }

        return view('livewire.permissions.index', [
            'permissions' => $permissions,
            'permissionDetalle' => $this->viewingId
                ? Permission::with('roles')->find($this->viewingId)
                : null,
        ]);
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->resetForm();

        if ($id) {
            $permission = Permission::findOrFail($id);
            $this->permissionId = $permission->id;
            $this->name = $permission->name;
            $this->updateMode = true;
        } else {
            $this->updateMode = false;
        }

        $this->modalVisible = true;
    }

    public function save()
    {
        $this->validate($this->rules(), $this->messages());

        if ($this->updateMode) {
            $permission = Permission::findOrFail($this->permissionId);
            $permission->update(['name' => $this->name]);
            session()->flash('message', 'Permiso actualizado correctamente.');
        } else {
            Permission::create(['name' => $this->name]);
            session()->flash('message', 'Permiso creado correctamente.');
        }

        $this->modalVisible = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        Permission::findOrFail($id)->delete();
        session()->flash('message', 'Permiso eliminado correctamente.');
    }
}
