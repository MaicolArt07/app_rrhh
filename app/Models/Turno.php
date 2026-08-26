<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $table = 'turnos';

    protected $fillable = [
        'nombre',
        'hora_entrada',
        'hora_salida',
        'tolerancia_minutos',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function empleados()
    {
        return $this->belongsToMany(Empleado::class, 'empleado_turno', 'id_turno', 'id_empleado')
            ->withPivot('estado')
            ->withTimestamps();
    }
}
