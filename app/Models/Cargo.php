<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $table = 'cargos';

    protected $fillable = [
        'id_departamento',
        'nombre',
        'sueldo_base',
        'descripcion_funciones',
        'estado',
    ];

    protected $casts = [
        'sueldo_base' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'id_cargo');
    }
}
