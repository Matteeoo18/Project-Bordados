<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Catalogo;
use Inertia\Inertia;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;



class CatalogoController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'productos' => Catalogo::paginate(10) // Esto es un objeto paginador
        ]);
    }

    /**
     * Summary of create
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Catalogos/CreateProduct');
    }
    /**
     * Función para almacenar las imagenes y productos.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:50',
            'descripcion' => 'required|string',
            'ruta' => 'required|string',
            'nombre' => 'required|string',
            'mime' => 'required|string',
        ]);

        // Validamos si el archivo existe físicamente
        $path = storage_path("app/" . $request->ruta . '/' . $request->nombre);
        if (!file_exists($path)) {
                return response()->json(['error' => 'Archivo no encontrado en el servidor'], 404);
        }

        $obj = Cloudinary::upload($path, ['folder' => 'archivos']);
        $public_id = $obj->getPublicId();
        $url = $obj->getSecurePath();

        // Opcional: guardar en DB si lo necesitas
        // Producto::create([...]);

        
        try {
            Catalogo::create([
                'titulo_post' => $request->input("titulo"),
                'enlace_post' => $url,
                'descripcion_post' => $request->input("descripcion"),
                'public_id' => $public_id,
                'tag_post' => $obj->getOriginalFileName(),
                'type_post' => $obj->getFileType(),
                'id_usuario' => auth()->id(),
            ]);
            // Queda pendiente eliminar el archivo del local.
            return redirect()->route("dashboard");
        } catch (\Exception $e) {
                return redirect()->back()->with('danger', '¡Ups! oucrrio un problema al momento de almacenar el bordado '.$e);
        }

        // dd([
        //     'public_id' => $obj->getPublicId(),
        //     'url' => $obj->getPath(),
        //     'filetype' => $obj->getFileType(),
        //     'originalFileName' => $obj->getOriginalFileName(),
        //     // 'raw_response' => $obj, // si quieres ver TODO lo que responde Cloudinary
        // ]);
    }

    public function updatearchive(Request $request, $id)
    {
        dd(
            $request->all(),
            $id
        );
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


    public function fillFiles($type)
    {
        $query = Catalogo::where("type_post", $type)->paginate(20);

        if ($query->isEmpty()) {
            return response()->json([
                'productosFill' => $query,
                'message' => 'No se han encontrado resultados referentes al tipo de archivo (' . $type . ') '
            ]);
        }

        return response()->json([
            'productosFill' => $query,
            'message' => 'Se han filtrado los archivos (' . $type . ') correctamente'
        ]);
    }
}
