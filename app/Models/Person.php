<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'person';
    public $timestamps = false;
    protected $fillable = [
        'id_country',
        'name',
        'lastname',
        'ci',
        'phone',
        'address',
        'state'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'id_country');
    }
}
