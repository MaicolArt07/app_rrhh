<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class LivewireUsers extends Component
{
    use WithPagination;

    // Propiedades del formulario directamente en el componente
    public $userId;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $selectedRoles = [];
    public $updateMode = false;

    public $roles = [];
    public $modalVisible = false;

    public function mount()
    {
        $this->resetForm();
        $this->roles = Role::where('guard_name', 'web')
                ->orderBy('name')
                ->get();
    }

    public function render()
    {
        return view('livewire.users.index', [
            'users' => User::with('roles')->paginate(10),
        ]);
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->resetForm();

        if ($id) {
            $user = User::with('roles')->findOrFail($id);
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->selectedRoles = $user->roles->pluck('name')->toArray();
            $this->updateMode = true;
        } else {
            $this->updateMode = false;
        }

        $this->modalVisible = true;
    }

    public function resetForm()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->selectedRoles = [];
        $this->updateMode = false;
    }

    protected function rules()
    {
        $uniqueEmailRule = $this->updateMode
            ? 'required|email|max:255|unique:users,email,' . $this->userId
            : 'required|email|max:255|unique:users,email';

        $rules = [
            'name' => 'required|string|max:255',
            'email' => $uniqueEmailRule,
            'selectedRoles' => 'required|array|min:1',
        ];

        if (!$this->updateMode) {
            $rules['password'] = 'required|min:6|confirmed';
        } elseif ($this->password || $this->password_confirmation) {
            $rules['password'] = 'nullable|min:6|confirmed';
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'El correo debe ser válido.',
        'email.unique' => 'Este correo ya está registrado.',
        'selectedRoles.required' => 'Debe seleccionar al menos un rol.',
        'selectedRoles.min' => 'Seleccione al menos un rol.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'Mínimo 6 caracteres para la contraseña.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
    ];

    public function save()
    {
        $this->validate($this->rules(), $this->messages);

        if ($this->updateMode) {
            $user = User::findOrFail($this->userId);
            $user->name = $this->name;
            $user->email = $this->email;
            if ($this->password) {
                $user->password = Hash::make($this->password);
            }
            $user->save();
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);
        }

        $user->syncRoles($this->selectedRoles);

        session()->flash('message', $this->updateMode
            ? 'Usuario actualizado correctamente.'
            : 'Usuario creado correctamente.');

        $this->modalVisible = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();
        session()->flash('message', 'Usuario eliminado correctamente.');
    }
}
