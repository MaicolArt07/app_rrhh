<?php

namespace App\Livewire\Form;

use Livewire\Form;

class AsistenciaForm extends Form
{
    public $id = null;
    public $id_empleado = '';
    public $fecha = '';
    public $hora_entrada = '';
    public $hora_salida = '';
    public $id_estado_asistencia = '';
    public $observaciones = '';
    public $estado = 1;

    public function rules()
    {
        return [
            'form.id_empleado' => 'required|exists:empleados,id',
            'form.fecha' => 'required|date',
            'form.hora_entrada' => 'nullable',
            'form.hora_salida' => 'nullable|after:form.hora_entrada',
            'form.id_estado_asistencia' => 'required|exists:tipos,id',
            'form.observaciones' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'form.id_empleado.required' => 'Debe seleccionar un empleado.',
            'form.fecha.required' => 'La fecha es obligatoria.',
            'form.hora_salida.after' => 'La hora de salida debe ser posterior a la hora de entrada.',
            'form.id_estado_asistencia.required' => 'Debe seleccionar un estado.',
        ];
    }

    public function resetForm()
    {
        $this->id = null;
        $this->id_empleado = '';
        $this->fecha = now()->format('Y-m-d');
        $this->hora_entrada = '';
        $this->hora_salida = '';
        $this->id_estado_asistencia = '';
        $this->observaciones = '';
        $this->estado = 1;
    }

    public function fillForm($asistencia)
    {
        $this->id = $asistencia->id;
        $this->id_empleado = $asistencia->id_empleado;
        $this->fecha = $asistencia->fecha?->format('Y-m-d');
        $this->hora_entrada = $asistencia->hora_entrada?->format('H:i');
        $this->hora_salida = $asistencia->hora_salida?->format('H:i');
        $this->id_estado_asistencia = $asistencia->id_estado_asistencia;
        $this->observaciones = $asistencia->observaciones;
        $this->estado = $asistencia->estado;
    }
}
