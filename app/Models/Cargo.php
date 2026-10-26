<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Cargo extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    protected $table = 'cargos';

    protected $fillable = [
        'id_area',
        'nombre',
        'sueldo_base',
        'descripcion_funciones',
        'estado',
    ];

    protected $casts = [
        'sueldo_base' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area');
    }

    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'id_cargo');
    }
}
