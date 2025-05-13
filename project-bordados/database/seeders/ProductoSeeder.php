<?php

namespace Database\Seeders;

use App\Models\Catalogo;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('catalogos')->insert([
            [
                'titulo_post' => 'Camiseta Bordada',
                'enlace_post' => 'https://tucatalogo.com/camiseta-bordada',
                'descripcion_post' => 'Hermosa camiseta con bordado personalizado.',
                'id_usuario' => 1,
                'is_active_post' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo_post' => 'Gorra Bordada',
                'enlace_post' => 'https://tucatalogo.com/gorra-bordada',
                'descripcion_post' => 'Gorra con bordado personalizado.',
                'id_usuario' => 1,
                'is_active_post' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo_post' => 'Sudadera Bordada',
                'enlace_post' => 'https://tucatalogo.com/sudadera-bordada',
                'descripcion_post' => 'Sudadera con bordado personalizado.',
                'id_usuario' => 1,
                'is_active_post' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo_post' => 'Taza Bordada',
                'enlace_post' => 'https://tucatalogo.com/taza-bordada',
                'descripcion_post' => 'Taza con bordado personalizado.',
                'id_usuario' => 1,
                'is_active_post' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo_post' => 'Mochila Bordada',
                'enlace_post' => 'https://tucatalogo.com/mochila-bordada',
                'descripcion_post' => 'Mochila con bordado personalizado.',
                'id_usuario' => 1,
                'is_active_post' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
