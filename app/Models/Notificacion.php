<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'id_usuario_destino',
        'id_tipo_notificacion',
        'titulo',
        'mensaje',
        'leido',
        'fecha_leido',
        'referencia_tipo',
        'referencia_id',
        'estado',
    ];

    protected $casts = [
        'leido' => 'boolean',
        'fecha_leido' => 'datetime',
        'estado' => 'boolean',
    ];

    public function usuarioDestino()
    {
        return $this->belongsTo(User::class, 'id_usuario_destino');
    }

    public function tipoNotificacion()
    {
        return $this->belongsTo(Tipo::class, 'id_tipo_notificacion');
    }
}
