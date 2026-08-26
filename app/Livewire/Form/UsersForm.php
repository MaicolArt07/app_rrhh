<?php

namespace App\Livewire\Form;

use Livewire\Form;

class UsersForm extends Form
{
    public $user_id = null;
    public $name = '';
    public $email = '';
    public $selectedRoles = [];
    public $password = '';
    public $password_confirmation = '';
    public $updateMode = false;


    public function resetForm()
    {
        $this->user_id = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->selectedRoles = [];
        $this->updateMode = false;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'selectedRoles' => 'required|array|min:1',
        ];

        if (!$this->updateMode) {
            $rules['password'] = 'required|min:6|confirmed';
        } elseif ($this->password || $this->password_confirmation) {
            $rules['password'] = 'nullable|min:6|confirmed';
        }

        return $rules;
    }

    public function messages()
    {
        return [
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
    }
}
