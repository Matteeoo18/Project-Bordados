<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalogo extends Model
{
    protected $table = 'catalogos';
    protected $fillable = [
        'titulo_post',
        'enlace_post',
        'descripcion_post',
        'public_id',
        'tag_post',
        'id_usuario',
        'is_active_post'    
    ];

    public function user()
    {
        return $this->belongsTo(Catalogo::class, "id_usuario");
    }
}
