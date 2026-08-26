<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PermisoLaboral extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'permisos_laborales';

    protected $fillable = [
        'id_empleado',
        'id_tipo_permiso',
        'fecha_inicio',
        'fecha_fin',
        'dias_solicitados',
        'goce_sueldo',
        'estado_solicitud',
        'id_aprobado_por',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'dias_solicitados' => 'decimal:2',
        'goce_sueldo' => 'boolean',
        'estado' => 'boolean',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }

    public function tipoPermiso()
    {
        return $this->belongsTo(Tipo::class, 'id_tipo_permiso');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'id_aprobado_por');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('documento_soporte')->singleFile();
    }
}
