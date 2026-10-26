<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DiaRemoto extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    protected $table = 'dias_remotos';

    protected $fillable = [
        'id_empleado',
        'fecha',
        'observacion',
        'id_asignado_por',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'estado' => 'boolean',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }

    public function asignadoPor()
    {
        return $this->belongsTo(User::class, 'id_asignado_por');
    }
}
