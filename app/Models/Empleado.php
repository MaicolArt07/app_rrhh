<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Empleado extends Model
{
    use SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    protected $table = 'empleados';

    protected $fillable = [
        'id_persona',
        'id_empresa',
        'codigo_empleado',
        'codigo_biometrico',
        'email_corporativo',
        'id_cargo',
        'id_area',
        'fecha_ingreso',
        'fecha_baja',
        'sueldo_actual',
        'id_estado_empleado',
        'numero_cuenta_bancaria',
        'banco',
        'estado',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'fecha_baja' => 'date',
        'sueldo_actual' => 'decimal:2',
        'estado' => 'boolean',
    ];

    /** Un código biométrico vacío se guarda como NULL (la columna es única). */
    public function setCodigoBiometricoAttribute($valor): void
    {
        $valor = trim((string) $valor);
        $this->attributes['codigo_biometrico'] = $valor === '' ? null : $valor;
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    /** Contrato vigente más reciente. */
    public function contratoVigente()
    {
        return $this->hasOne(Contrato::class, 'id_empleado')->where('estado', true)->latestOfMany('fecha_inicio');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'id_cargo');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area');
    }

    public function estadoEmpleado()
    {
        return $this->belongsTo(Tipo::class, 'id_estado_empleado');
    }

    public function historialCargos()
    {
        return $this->hasMany(HistorialCargo::class, 'id_empleado');
    }

    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'id_empleado');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoEmpleado::class, 'id_empleado');
    }

    public function turnos()
    {
        return $this->belongsToMany(Turno::class, 'empleado_turno', 'id_empleado', 'id_turno')
            ->withPivot('estado')
            ->withTimestamps();
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_empleado');
    }

    public function diasRemotos()
    {
        return $this->hasMany(DiaRemoto::class, 'id_empleado');
    }

    public function permisosLaborales()
    {
        return $this->hasMany(PermisoLaboral::class, 'id_empleado');
    }

    public function saldosVacaciones()
    {
        return $this->hasMany(SaldoVacacion::class, 'id_empleado');
    }

    public function horasExtraDetalle()
    {
        return $this->hasMany(HoraExtraDetalle::class, 'id_empleado');
    }

    public function liquidaciones()
    {
        return $this->hasMany(Liquidacion::class, 'id_empleado');
    }

    public function evaluacionesDesempeno()
    {
        return $this->hasMany(EvaluacionDesempeno::class, 'id_empleado');
    }

    public function anticiposPrestamos()
    {
        return $this->hasMany(AnticipoPrestamo::class, 'id_empleado');
    }

    public function reglamentoLecturas()
    {
        return $this->hasMany(ReglamentoLectura::class, 'id_empleado');
    }
}
