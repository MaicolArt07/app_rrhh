<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluacionDesempeno extends Model
{
    protected $table = 'evaluaciones_desempeno';

    protected $fillable = [
        'id_empleado',
        'id_evaluador',
        'id_gestion',
        'semestre',
        'fecha_evaluacion',
        'puntuacion_total',
        'id_resultado_evaluacion',
        'comentarios',
        'plan_mejora',
        'estado',
    ];

    protected $casts = [
        'fecha_evaluacion' => 'date',
        'puntuacion_total' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }

    public function evaluador()
    {
        return $this->belongsTo(User::class, 'id_evaluador');
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion');
    }

    public function resultadoEvaluacion()
    {
        return $this->belongsTo(Tipo::class, 'id_resultado_evaluacion');
    }

    public function detalles()
    {
        return $this->hasMany(EvaluacionDetalle::class, 'id_evaluacion');
    }
}
