<?php

namespace App\Livewire\Form;

use Illuminate\Validation\Rule;
use Livewire\Form;

class EmpleadoForm extends Form
{
    public $id = null;
    public $id_persona = '';
    public $codigo_empleado = '';
    public $id_cargo = '';
    public $id_departamento = '';
    public $fecha_ingreso = '';
    public $fecha_baja = '';
    public $sueldo_actual = '';
    public $id_estado_empleado = '';
    public $numero_cuenta_bancaria = '';
    public $banco = '';
    public $estado = 1;

    public function rules()
    {
        return [
            'form.id_persona' => [
                'required',
                'exists:personas,id',
                Rule::unique('empleados', 'id_persona')->ignore($this->id),
            ],
            'form.codigo_empleado' => [
                'required', 'string', 'max:20',
                Rule::unique('empleados', 'codigo_empleado')->ignore($this->id),
            ],
            'form.id_cargo' => 'required|exists:cargos,id',
            'form.id_departamento' => 'required|exists:departamentos,id',
            'form.fecha_ingreso' => 'required|date',
            'form.fecha_baja' => 'nullable|date|after_or_equal:form.fecha_ingreso',
            'form.sueldo_actual' => 'required|numeric|min:0',
            'form.id_estado_empleado' => 'required|exists:tipos,id',
            'form.numero_cuenta_bancaria' => 'nullable|string|max:50',
            'form.banco' => 'nullable|string|max:100',
        ];
    }

    public function messages()
    {
        return [
            'form.id_persona.required' => 'Debe seleccionar una persona.',
            'form.id_persona.unique' => 'Esta persona ya está registrada como empleado.',
            'form.codigo_empleado.required' => 'El código de empleado es obligatorio.',
            'form.codigo_empleado.unique' => 'Ese código de empleado ya existe.',
            'form.id_cargo.required' => 'Debe seleccionar un cargo.',
            'form.id_departamento.required' => 'Debe seleccionar un departamento.',
            'form.fecha_ingreso.required' => 'La fecha de ingreso es obligatoria.',
            'form.fecha_baja.after_or_equal' => 'La fecha de baja debe ser posterior o igual a la fecha de ingreso.',
            'form.sueldo_actual.required' => 'El sueldo actual es obligatorio.',
            'form.id_estado_empleado.required' => 'Debe seleccionar un estado.',
        ];
    }

    public function resetForm()
    {
        $this->id = null;
        $this->id_persona = '';
        $this->codigo_empleado = '';
        $this->id_cargo = '';
        $this->id_departamento = '';
        $this->fecha_ingreso = '';
        $this->fecha_baja = '';
        $this->sueldo_actual = '';
        $this->id_estado_empleado = '';
        $this->numero_cuenta_bancaria = '';
        $this->banco = '';
        $this->estado = 1;
    }

    public function fillForm($empleado)
    {
        $this->id = $empleado->id;
        $this->id_persona = $empleado->id_persona;
        $this->codigo_empleado = $empleado->codigo_empleado;
        $this->id_cargo = $empleado->id_cargo;
        $this->id_departamento = $empleado->id_departamento;
        $this->fecha_ingreso = $empleado->fecha_ingreso?->format('Y-m-d');
        $this->fecha_baja = $empleado->fecha_baja?->format('Y-m-d');
        $this->sueldo_actual = $empleado->sueldo_actual;
        $this->id_estado_empleado = $empleado->id_estado_empleado;
        $this->numero_cuenta_bancaria = $empleado->numero_cuenta_bancaria;
        $this->banco = $empleado->banco;
        $this->estado = $empleado->estado;
    }
}
