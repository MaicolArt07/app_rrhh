<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mes extends Model
{
    protected $table = 'meses';

    protected $fillable = [
        'numero',
        'nombre',
        'abreviatura',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];
}
