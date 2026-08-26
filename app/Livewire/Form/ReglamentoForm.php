<?php

namespace App\Livewire\Form;

use Livewire\Form;

class ReglamentoForm extends Form
{
    public $id = null;
    public $titulo = '';
    public $contenido = '';
    public $version = '';
    public $vigente_desde = '';
    public $estado = 1;

    public function rules()
    {
        return [
            'form.titulo' => 'required|string|max:200',
            'form.contenido' => 'required|string',
            'form.version' => 'required|string|max:20',
            'form.vigente_desde' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'form.titulo.required' => 'El título es obligatorio.',
            'form.contenido.required' => 'El contenido es obligatorio.',
            'form.version.required' => 'La versión es obligatoria.',
            'form.vigente_desde.required' => 'La fecha de vigencia es obligatoria.',
        ];
    }

    public function resetForm()
    {
        $this->id = null;
        $this->titulo = '';
        $this->contenido = '';
        $this->version = '1.0';
        $this->vigente_desde = now()->format('Y-m-d');
        $this->estado = 1;
    }

    public function fillForm($reglamento)
    {
        $this->id = $reglamento->id;
        $this->titulo = $reglamento->titulo;
        $this->contenido = $reglamento->contenido;
        $this->version = $reglamento->version;
        $this->vigente_desde = $reglamento->vigente_desde?->format('Y-m-d');
        $this->estado = $reglamento->estado;
    }
}
