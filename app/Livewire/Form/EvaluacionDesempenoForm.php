<?php

namespace App\Livewire\Form;

use Livewire\Form;

class EvaluacionDesempenoForm extends Form
{
    public $id_empleado = '';
    public $id_gestion = '';
    public $semestre = '';
    public $fecha_evaluacion = '';
    public $comentarios = '';
    public $plan_mejora = '';

    public function rules()
    {
        return [
            'form.id_empleado' => 'required|exists:empleados,id',
            'form.id_gestion' => 'required|exists:gestiones,id',
            'form.semestre' => 'required|in:primero,segundo',
            'form.fecha_evaluacion' => 'required|date',
            'form.comentarios' => 'nullable|string|max:1000',
            'form.plan_mejora' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'form.id_empleado.required' => 'Debe seleccionar un empleado.',
            'form.id_gestion.required' => 'Debe seleccionar una gestión.',
            'form.semestre.required' => 'Debe seleccionar el semestre.',
            'form.fecha_evaluacion.required' => 'La fecha de evaluación es obligatoria.',
        ];
    }

    public function resetForm()
    {
        $this->id_empleado = '';
        $this->id_gestion = '';
        $this->semestre = '';
        $this->fecha_evaluacion = now()->format('Y-m-d');
        $this->comentarios = '';
        $this->plan_mejora = '';
    }
}
