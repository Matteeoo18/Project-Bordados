<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// importamos inertia para usarlo en el controlador
use App\Models\Catalogo;
use Inertia\Inertia;

class CatalogoController extends Controller
{
    public function index()
    {
        $productos = Catalogo::paginate(10); // 10 productos por página
        return Inertia::render('Dashboard', [
            'productos' => $productos
        ]);
    }

    public function create()
    {
        return view('catalogo.create');
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
        return Inertia::render('Catalogos/EditProduct',[
            'producto' => $producto
        ]);
    }
}
