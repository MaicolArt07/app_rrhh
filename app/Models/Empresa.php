<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Empresa extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    protected $table = 'empresas';

    protected $fillable = [
        'codigo',
        'nombre',
        'razon_social',
        'nit',
        'representante_legal',
        'direccion',
        'telefono',
        'email',
        'color_hex',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'id_empresa');
    }

    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'id_empresa');
    }

    /** Busca la empresa por el nombre que usa el Excel de RR.HH. ("Deltaexpress", "Delta Cargo "…). */
    public static function porNombreFlexible(string $nombre): ?self
    {
        $clave = preg_replace('/[^a-z]/', '', strtolower($nombre));

        return static::all()->first(fn (Empresa $e) => preg_replace('/[^a-z]/', '', strtolower($e->nombre)) === $clave);
    }
}
