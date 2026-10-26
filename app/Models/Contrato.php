<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Contrato extends Model implements HasMedia
{
    use InteractsWithMedia;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    /** Máximo de contratos por prestación de servicios que permite el Ministerio de Trabajo. */
    public const MAX_PRESTACION = 2;

    /** Días de anticipación con que se alerta un vencimiento. */
    public const DIAS_ALERTA = 30;

    protected $table = 'contratos';

    protected $fillable = [
        'id_empleado',
        'id_empresa',
        'id_tipo_contrato',
        'numero_contrato',
        'cargo',
        'monto',
        'dependencia',
        'funciones',
        'herederos',
        'id_contrato_anterior',
        'fecha_inicio',
        'fecha_fin',
        'notificado_vencimiento',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'monto' => 'decimal:2',
        'notificado_vencimiento' => 'boolean',
        'estado' => 'boolean',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }

    public function tipoContrato()
    {
        return $this->belongsTo(Tipo::class, 'id_tipo_contrato');
    }

    /** Empresa con la que se firmó el contrato. */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function contratoAnterior()
    {
        return $this->belongsTo(Contrato::class, 'id_contrato_anterior');
    }

    public function contratoSiguiente()
    {
        return $this->hasOne(Contrato::class, 'id_contrato_anterior');
    }

    public function esPrestacion(): bool
    {
        return $this->tipoContrato?->codigo === 'prestacion';
    }

    public function esIndefinido(): bool
    {
        return $this->tipoContrato?->codigo === 'indefinido';
    }

    /** Es el último contrato por prestación que se le puede dar al empleado. */
    public function esUltimaPrestacion(): bool
    {
        return $this->esPrestacion() && $this->numero_contrato >= self::MAX_PRESTACION;
    }

    public function diasParaVencer(): ?int
    {
        return $this->fecha_fin ? (int) now()->startOfDay()->diffInDays($this->fecha_fin, false) : null;
    }

    public function porVencer(): bool
    {
        $dias = $this->diasParaVencer();

        return $this->estado && $dias !== null && $dias >= 0 && $dias <= self::DIAS_ALERTA;
    }

    public function vencido(): bool
    {
        $dias = $this->diasParaVencer();

        return $this->estado && $dias !== null && $dias < 0;
    }

    public function situacion(): string
    {
        if ($this->estado) {
            return 'Vigente';
        }

        return $this->contratoSiguiente ? 'Finalizado' : 'Inactivo';
    }

    public function etiquetaNumero(): string
    {
        return "{$this->numero_contrato}.º";
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('documento')->singleFile();
    }
}
