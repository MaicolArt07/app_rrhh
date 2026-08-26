<?php

namespace App\Livewire\Form;

use Illuminate\Validation\Rule;
use Livewire\Form;

class PersonaForm extends Form
{
    public $id = null;
    public $primer_nombre = '';
    public $segundo_nombre = '';
    public $primer_apellido = '';
    public $segundo_apellido = '';
    public $id_tipo_documento = '';
    public $numero_documento = '';
    public $nit = '';
    public $fecha_nacimiento = '';
    public $genero = '';
    public $direccion = '';
    public $telefono = '';
    public $email = '';
    public $estado = 1;

    public function rules()
    {
        return [
            'form.primer_nombre' => 'required|string|max:100',
            'form.segundo_nombre' => 'nullable|string|max:100',
            'form.primer_apellido' => 'required|string|max:100',
            'form.segundo_apellido' => 'nullable|string|max:100',
            'form.id_tipo_documento' => 'required|exists:tipos,id',
            'form.numero_documento' => [
                'required',
                'string',
                'max:50',
                Rule::unique('personas', 'numero_documento')
                    ->where(fn ($query) => $query->where('id_tipo_documento', $this->id_tipo_documento))
                    ->ignore($this->id),
            ],
            'form.nit' => 'nullable|string|max:20',
            'form.fecha_nacimiento' => 'nullable|date|before:today',
            'form.genero' => 'required|in:masculino,femenino,otro',
            'form.direccion' => 'nullable|string|max:500',
            'form.telefono' => 'nullable|string|max:20',
            'form.email' => 'nullable|email|max:150',
        ];
    }

    public function messages()
    {
        return [
            'form.primer_nombre.required' => 'El primer nombre es obligatorio.',
            'form.primer_apellido.required' => 'El primer apellido es obligatorio.',
            'form.id_tipo_documento.required' => 'Debe seleccionar un tipo de documento.',
            'form.id_tipo_documento.exists' => 'El tipo de documento seleccionado no es válido.',
            'form.numero_documento.required' => 'El número de documento es obligatorio.',
            'form.numero_documento.unique' => 'Ya existe una persona con ese número de documento para el tipo seleccionado.',
            'form.fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'form.genero.required' => 'Debe seleccionar un género.',
            'form.email.email' => 'El correo electrónico no es válido.',
        ];
    }

    public function resetForm()
    {
        $this->id = null;
        $this->primer_nombre = '';
        $this->segundo_nombre = '';
        $this->primer_apellido = '';
        $this->segundo_apellido = '';
        $this->id_tipo_documento = '';
        $this->numero_documento = '';
        $this->nit = '';
        $this->fecha_nacimiento = '';
        $this->genero = '';
        $this->direccion = '';
        $this->telefono = '';
        $this->email = '';
        $this->estado = 1;
    }

    public function fillForm($persona)
    {
        $this->id = $persona->id;
        $this->primer_nombre = $persona->primer_nombre;
        $this->segundo_nombre = $persona->segundo_nombre;
        $this->primer_apellido = $persona->primer_apellido;
        $this->segundo_apellido = $persona->segundo_apellido;
        $this->id_tipo_documento = $persona->id_tipo_documento;
        $this->numero_documento = $persona->numero_documento;
        $this->nit = $persona->nit;
        $this->fecha_nacimiento = $persona->fecha_nacimiento?->format('Y-m-d');
        $this->genero = $persona->genero;
        $this->direccion = $persona->direccion;
        $this->telefono = $persona->telefono;
        $this->email = $persona->email;
        $this->estado = $persona->estado;
    }
}
