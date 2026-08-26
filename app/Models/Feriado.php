<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feriado extends Model
{
    protected $table = 'feriados';

    protected $fillable = [
        'id_gestion',
        'fecha',
        'descripcion',
        'aplica_todos',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'aplica_todos' => 'boolean',
        'estado' => 'boolean',
    ];

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion');
    }
}
