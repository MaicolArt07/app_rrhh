<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
    protected $table = 'tipos';

    protected $fillable = [
        'categoria',
        'codigo',
        'nombre',
        'valor_referencia',
        'orden',
        'estado',
    ];

    protected $casts = [
        'valor_referencia' => 'decimal:4',
        'estado' => 'boolean',
    ];

    public function scopeCategoria($query, string $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}
