<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles'; // agregamos esta linea para especificar la tabla ya que laravel por defecto busca la tabla en plural
    
    protected $fillable = [
        'nombre_rol'
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }
}
