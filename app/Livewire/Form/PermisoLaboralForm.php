<?php

namespace App\Livewire\Form;

use Livewire\Form;

class PermisoLaboralForm extends Form
{
    public $id = null;
    public $id_empleado = '';
    public $id_tipo_permiso = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $goce_sueldo = 1;
    public $estado = 1;

    public function rules()
    {
        return [
            'form.id_empleado' => 'required|exists:empleados,id',
            'form.id_tipo_permiso' => 'required|exists:tipos,id',
            'form.fecha_inicio' => 'required|date',
            'form.fecha_fin' => 'required|date|after_or_equal:form.fecha_inicio',
        ];
    }

    public function messages()
    {
        return [
            'form.id_empleado.required' => 'Debe seleccionar un empleado.',
            'form.id_tipo_permiso.required' => 'Debe seleccionar un tipo de permiso.',
            'form.fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'form.fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'form.fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
        ];
    }

    public function resetForm()
    {
        $this->id = null;
        $this->id_empleado = '';
        $this->id_tipo_permiso = '';
        $this->fecha_inicio = '';
        $this->fecha_fin = '';
        $this->goce_sueldo = 1;
        $this->estado = 1;
    }

    public function fillForm($permiso)
    {
        $this->id = $permiso->id;
        $this->id_empleado = $permiso->id_empleado;
        $this->id_tipo_permiso = $permiso->id_tipo_permiso;
        $this->fecha_inicio = $permiso->fecha_inicio?->format('Y-m-d');
        $this->fecha_fin = $permiso->fecha_fin?->format('Y-m-d');
        $this->goce_sueldo = $permiso->goce_sueldo;
        $this->estado = $permiso->estado;
    }
}
