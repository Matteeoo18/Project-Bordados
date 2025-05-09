<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    //
    protected $fillable = [
        'nombre_rol'
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }
}
