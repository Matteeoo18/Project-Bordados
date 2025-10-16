<?php

namespace App\Http\Controllers;


use File;
use Illuminate\Http\Request;
use App\Models\Catalogo;
use Inertia\Inertia;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Storage;



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
        // dd($request->all());
        $request->validate([
            'titulo' => 'required|string|max:50',
            'descripcion' => 'required|string',
            'ruta' => 'required|string',
            'nombre' => 'required|string',
            'mime' => 'required|string',
        ]);

        // Validamos si el archivo existe físicamente
        $path = storage_path("app/" . $request->ruta . $request->nombre);
        // dd($path);

        if (!file_exists($path)) {
            return back()->withErrors(['archivo' => 'Archivo no encontrado en el servidor']);
        }
        // dd($path);
        // se usa cuando esta en local
        // $obj = Cloudinary::uploadFile($path, ['folder' => 'archivos']);
        // se usa cuando esta subido en un servidor 
        $obj = Cloudinary::upload($path, ['folder' => 'archivos']);
        $public_id = $obj->getPublicId();
        $url = $obj->getSecurePath();


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
            File::delete($path);
            $directory = dirname($path); // Obtiene la carpeta del archivo
            if (File::isDirectory($directory) && count(File::files($directory)) === 0) {
                File::deleteDirectory($directory); // Borra la carpeta si ya está vacía
            }
            return redirect()->route("dashboard");
        } catch (\Exception $e) {
            return redirect()->back()->with('danger', '¡Ups! oucrrio un problema al momento de almacenar el bordado ' . $e);
        }
    }

    public function updatearchive(Request $request, $id)
    {

        $request->validate([
            'titulo' => 'required|string|max:50',
            'descripcion' => 'required|string',
        ]);
        if ($request->cambio_archivo == '1') {
            // recibimos el id y la informacion a actualizar
            $request->validate([
                'ruta' => 'required|string',
                'nombre' => 'required|string',
                'mime' => 'required|string',
            ]);
            // Validamos si el archivo existe físicamente
            $path = storage_path("app/" . $request->ruta . $request->nombre);


            if (!file_exists($path)) {
                // aqui es cuando la ruta no existe
            }
            // seguimos con la logica para guardar el nuevo archivo y eliminarlo en el cloudinary
            // Buscar el registro
            $catalogo = Catalogo::findOrFail($id);
            if ($catalogo->public_id) {
                Cloudinary::destroy($catalogo->public_id);
            }

            // 4️ Subir la nueva imagen

            $obj = Cloudinary::upload($path, ['folder' => 'archivos']);
            $public_id = $obj->getPublicId();
            $url = $obj->getSecurePath();
            // actualizamos el registro
            try {
                $catalogo->update([
                    'titulo_post' => $request->input("titulo"),
                    'enlace_post' => $url,
                    'descripcion_post' => $request->input("descripcion"),
                    'public_id' => $public_id,
                    'tag_post' => $obj->getOriginalFileName(),
                    'type_post' => $obj->getFileType(),
                    'id_usuario' => auth()->id(),
                ]);
                // Queda pendiente eliminar el archivo del local.
                File::delete($path); // Funciona pero no nos esta elimnando la carpeta como tal solo el archivo. Probar con el disk (modificando el coso en el filesystem).
                $directory = dirname($path); // Obtiene la carpeta del archivo
                if (File::isDirectory($directory) && count(File::files($directory)) === 0) {
                    File::deleteDirectory($directory); // Borra la carpeta si ya está vacía
                }
                return redirect()->route("dashboard");
            } catch (\Exception $e) {
                return redirect()->back()->with('danger', '¡Ups! oucrrio un problema al momento de almacenar el bordado ' . $e);
            }
        } else {

            // se valida que no se envie ningun archivo y solo se actualiza el titulo y la descripcion
            try {
                $bordado = Catalogo::findOrFail($id);
                $bordado->titulo_post = $request->input("titulo");
                $bordado->descripcion_post = $request->input("descripcion");
                $bordado->save();
                return redirect()->route("dashboard");
            } catch (\Exception $e) {
                return redirect()->back()->with('danger', '¡Ups! oucrrio un problema al momento de actualizar el bordado ' . $e);
            }
        }
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
