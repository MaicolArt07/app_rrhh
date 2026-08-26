<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReglamentoLectura extends Model
{
    protected $table = 'reglamento_lecturas';

    protected $fillable = [
        'id_reglamento',
        'id_empleado',
        'fecha_lectura',
        'ip_confirmacion',
        'estado',
    ];

    protected $casts = [
        'fecha_lectura' => 'datetime',
        'estado' => 'boolean',
    ];

    public function reglamento()
    {
        return $this->belongsTo(Reglamento::class, 'id_reglamento');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }
}
