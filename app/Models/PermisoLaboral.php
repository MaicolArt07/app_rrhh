<?php

namespace App\Models;

use App\Services\Asistencia\AsistenciaService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PermisoLaboral extends Model implements HasMedia
{
    use InteractsWithMedia;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    protected $table = 'permisos_laborales';

    protected static function booted(): void
    {
        // Aprobar, rechazar o cambiar un permiso reclasifica la asistencia de esos días.
        $recalcular = function (PermisoLaboral $permiso) {
            $inicio = collect([$permiso->fecha_inicio, $permiso->getOriginal('fecha_inicio')])->filter()->map(fn ($f) => Carbon::parse($f))->min();
            $fin = collect([$permiso->fecha_fin, $permiso->getOriginal('fecha_fin')])->filter()->map(fn ($f) => Carbon::parse($f))->max();

            if ($inicio && $fin) {
                app(AsistenciaService::class)->recalcular($inicio, $fin, [$permiso->id_empleado]);
            }
        };

        static::saved($recalcular);
        static::deleted($recalcular);
    }

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
