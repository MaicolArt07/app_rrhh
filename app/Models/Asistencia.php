<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $table = 'asistencias';

    protected $fillable = [
        'id_empleado',
        'id_gestion',
        'id_mes',
        'fecha',
        'hora_entrada',
        'hora_salida',
        'hora_entrada_marcada_manual',
        'latitud',
        'longitud',
        'id_estado_asistencia',
        'id_tipo_asistencia',
        'marcaciones',
        'marcaciones_crudas',
        'minutos_atraso',
        'es_sancionable',
        'numero_evento_mes',
        'multa_dia',
        'tipo_asignado_manual',
        'id_usuario_modifico',
        'id_importacion',
        'origen',
        'horas_trabajadas',
        'horas_extra',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_entrada' => 'datetime',
        'hora_salida' => 'datetime',
        'hora_entrada_marcada_manual' => 'boolean',
        'marcaciones' => 'array',
        'es_sancionable' => 'boolean',
        'multa_dia' => 'decimal:2',
        'tipo_asignado_manual' => 'boolean',
        'latitud' => 'decimal:7',
        'longitud' => 'decimal:7',
        'horas_trabajadas' => 'decimal:2',
        'horas_extra' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion');
    }

    public function mes()
    {
        return $this->belongsTo(Mes::class, 'id_mes');
    }

    public function estadoAsistencia()
    {
        return $this->belongsTo(Tipo::class, 'id_estado_asistencia');
    }

    public function tipoAsistencia()
    {
        return $this->belongsTo(TipoAsistencia::class, 'id_tipo_asistencia');
    }

    public function usuarioModifico()
    {
        return $this->belongsTo(User::class, 'id_usuario_modifico');
    }

    public function importacion()
    {
        return $this->belongsTo(ImportacionBiometrico::class, 'id_importacion');
    }

    protected static function booted(): void
    {
        // Los registros manuales (CRUD de marcajes) siguen eligiendo un estado del
        // catálogo genérico; se traduce al tipo de asistencia equivalente (§3).
        static::saving(function (Asistencia $asistencia) {
            if ($asistencia->origen !== 'biometrico' && $asistencia->isDirty('id_estado_asistencia') && $asistencia->id_estado_asistencia) {
                $codigo = Tipo::find($asistencia->id_estado_asistencia)?->codigo;
                $tipo = self::MAPEO_ESTADOS[$codigo] ?? null;
                $asistencia->id_tipo_asistencia = $tipo ? TipoAsistencia::idDe($tipo) : $asistencia->id_tipo_asistencia;
            }
        });
    }

    /** Códigos de tipos.estado_asistencia → tipos_asistencia. */
    public const MAPEO_ESTADOS = [
        'puntual' => TipoAsistencia::PUNTUAL,
        'tarde' => TipoAsistencia::ATRASO,
        'ausente' => TipoAsistencia::SIN_MARCAR,
        'permiso' => TipoAsistencia::BAJA_PERMISO,
        'vacaciones' => TipoAsistencia::VACACION,
    ];

    public function horasExtraDetalle()
    {
        return $this->hasMany(HoraExtraDetalle::class, 'id_asistencia');
    }
}
