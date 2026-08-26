<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluacionDetalle extends Model
{
    protected $table = 'evaluacion_detalles';

    protected $fillable = [
        'id_evaluacion',
        'criterio',
        'puntuacion',
        'estado',
    ];

    protected $casts = [
        'puntuacion' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function evaluacion()
    {
        return $this->belongsTo(EvaluacionDesempeno::class, 'id_evaluacion');
    }
}
