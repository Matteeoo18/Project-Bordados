<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// importamos inertia para usarlo en el controlador
use App\Models\Catalogo;
use Inertia\Inertia;
use Cloudinary\Api\ApiUtils;
use Illuminate\Support\Facades\Date;

class CatalogoController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'productos' => Catalogo::paginate(10) // Esto es un objeto paginador
        ]);
    }


    public function create()
    {
        return Inertia::render('Catalogos/CreateProduct');
    }
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string',
            'descripcion' => 'required|string',
            'archivo' => 'required|file|mimes:jpg,jpeg,png,mp4,mov,avi',
        ]);
        dd([
            'campos' => $request->all(),
            'archivo' => $request->file('archivo')
        ]);
    }

    // aqui recibimos el id  a eliminar de la table
    public function destroy($id)
    {
        // aqui se recibi el id a eliminar
        $producto = Catalogo::findOrFail($id);
        $producto->delete();
        // redireccionamos a la vista de index
        return response()->json([
            'message' => 'Producto eliminado correctamente'
        ]);
    }
    public function edit($id)
    {
        // aqui se recibi el id a editar
        $producto = Catalogo::findOrFail($id);
        //    dd($producto);
        // redireccionamos a la vista de index
        return Inertia::render('Catalogos/EditProduct', [
            'producto' => $producto
        ]);
    }

    public function signature()
    {
        // Por si acaso, para la firma se necesita generar el timestamp y enviarlo al front, esto por seguridad de que no vayan a usar una firma antigua
        $timestamp = time();
        $params_to_sign = [
            'timestamp' => $timestamp,
        ];

        $signature = ApiUtils::signParameters($params_to_sign,env('CLOUDINARY_NOTIFICATION_URL'));

        return response()->json([
            'signature' => $signature,
            'timestamp' => $timestamp,
            'api_key' => env('CLOUDINARY_UPLOAD_PRESET'),
            'cloud_name' => env('CLOUDINDARY_NAME'),
        ]);

    }
}
