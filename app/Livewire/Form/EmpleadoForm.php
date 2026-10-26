<?php

namespace App\Livewire\Form;

use Illuminate\Validation\Rule;
use Livewire\Form;

class EmpleadoForm extends Form
{
    public $id = null;
    public $id_persona = '';
    public $id_empresa = '';
    public $codigo_empleado = '';
    public $codigo_biometrico = '';
    public $email_corporativo = '';
    public $id_cargo = '';
    public $id_area = '';
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
            'form.id_empresa' => 'required|exists:empresas,id',
            'form.codigo_empleado' => [
                'required', 'string', 'max:20',
                Rule::unique('empleados', 'codigo_empleado')->ignore($this->id),
            ],
            'form.codigo_biometrico' => [
                'nullable', 'string', 'max:20',
                Rule::unique('empleados', 'codigo_biometrico')->ignore($this->id),
            ],
            'form.email_corporativo' => 'nullable|email|max:150',
            'form.id_cargo' => 'required|exists:cargos,id',
            'form.id_area' => 'required|exists:areas,id',
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
            'form.id_empresa.required' => 'Debe seleccionar la empresa.',
            'form.codigo_empleado.required' => 'El código de empleado es obligatorio.',
            'form.codigo_empleado.unique' => 'Ese código de empleado ya existe.',
            'form.codigo_biometrico.unique' => 'Ese código biométrico ya está asignado a otro empleado.',
            'form.id_cargo.required' => 'Debe seleccionar un cargo.',
            'form.id_area.required' => 'Debe seleccionar un área.',
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
        $this->id_empresa = '';
        $this->codigo_empleado = '';
        $this->codigo_biometrico = '';
        $this->email_corporativo = '';
        $this->id_cargo = '';
        $this->id_area = '';
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
        $this->id_empresa = $empleado->id_empresa;
        $this->codigo_empleado = $empleado->codigo_empleado;
        $this->codigo_biometrico = $empleado->codigo_biometrico;
        $this->email_corporativo = $empleado->email_corporativo;
        $this->id_cargo = $empleado->id_cargo;
        $this->id_area = $empleado->id_area;
        $this->fecha_ingreso = $empleado->fecha_ingreso?->format('Y-m-d');
        $this->fecha_baja = $empleado->fecha_baja?->format('Y-m-d');
        $this->sueldo_actual = $empleado->sueldo_actual;
        $this->id_estado_empleado = $empleado->id_estado_empleado;
        $this->numero_cuenta_bancaria = $empleado->numero_cuenta_bancaria;
        $this->banco = $empleado->banco;
        $this->estado = $empleado->estado;
    }
}
