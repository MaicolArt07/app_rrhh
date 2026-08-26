<?php

namespace App\Livewire\Form;

use Illuminate\Validation\Rule;
use Livewire\Form;

class GestionForm extends Form
{
    public $id = null;
    public $anio = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $estado = 1;

    public function rules()
    {
        return [
            'form.anio' => [
                'required',
                'integer',
                'digits:4',
                Rule::unique('gestiones', 'anio')->ignore($this->id),
            ],
            'form.fecha_inicio' => 'required|date',
            'form.fecha_fin' => 'required|date|after_or_equal:form.fecha_inicio',
        ];
    }

    public function messages()
    {
        return [
            'form.anio.required' => 'El año es obligatorio.',
            'form.anio.unique' => 'Ya existe una gestión para ese año.',
            'form.fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'form.fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'form.fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
        ];
    }

    public function resetForm()
    {
        $this->id = null;
        $this->anio = '';
        $this->fecha_inicio = '';
        $this->fecha_fin = '';
        $this->estado = 1;
    }

    public function fillForm($gestion)
    {
        $this->id = $gestion->id;
        $this->anio = $gestion->anio;
        $this->fecha_inicio = $gestion->fecha_inicio->format('Y-m-d');
        $this->fecha_fin = $gestion->fecha_fin->format('Y-m-d');
        $this->estado = $gestion->estado;
    }

    public function actualizarFechasPorAnio()
    {
        if ($this->anio && strlen((string) $this->anio) === 4) {
            $this->fecha_inicio = "{$this->anio}-01-01";
            $this->fecha_fin = "{$this->anio}-12-31";
        }
    }
}
