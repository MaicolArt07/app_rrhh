<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnticipoPrestamo extends Model
{
    protected $table = 'anticipos_prestamos';

    protected $fillable = [
        'id_empleado',
        'id_tipo_anticipo',
        'monto_total',
        'numero_cuotas',
        'monto_cuota',
        'saldo_pendiente',
        'fecha_otorgado',
        'estado_anticipo',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'monto_total' => 'decimal:2',
        'monto_cuota' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'fecha_otorgado' => 'date',
        'estado' => 'boolean',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }

    public function tipoAnticipo()
    {
        return $this->belongsTo(Tipo::class, 'id_tipo_anticipo');
    }

    public function pagos()
    {
        return $this->hasMany(AnticipoPago::class, 'id_anticipo');
    }
}
