<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialCargo extends Model
{
    protected $table = 'historial_cargos';

    protected $fillable = [
        'id_empleado',
        'id_cargo_anterior',
        'id_cargo_nuevo',
        'sueldo_anterior',
        'sueldo_nuevo',
        'fecha_cambio',
        'motivo',
        'estado',
    ];

    protected $casts = [
        'sueldo_anterior' => 'decimal:2',
        'sueldo_nuevo' => 'decimal:2',
        'fecha_cambio' => 'date',
        'estado' => 'boolean',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }

    public function cargoAnterior()
    {
        return $this->belongsTo(Cargo::class, 'id_cargo_anterior');
    }

    public function cargoNuevo()
    {
        return $this->belongsTo(Cargo::class, 'id_cargo_nuevo');
    }
}
