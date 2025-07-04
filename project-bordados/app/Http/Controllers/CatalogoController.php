<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
// importamos inertia para usarlo en el controlador
use App\Models\Catalogo;
use Inertia\Inertia;
use Cloudinary\Api\ApiUtils;
use Illuminate\Support\Facades\Date;
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
    public function store(Request $request) {
        $request->validate([
            'titulo' => 'required|string|max:50',
            'descripcion' => 'required|string',
        ]);
        // dd([
        //     'campos' => $request->all(),
        //     'archivo' => $request->file('archivo')
        // ]);
        try{
            $catalogo = Catalogo::create([
                "titulo_post" => $request->input("titulo"),
                "enlace_post" => $request->input("enlace_post"),
                "descripcion_post" => $request->input("descripcion"),
                "public_id" => $request->input("public_id"),
                "tag_post" => $request->input("tag_post"),
                "type_post" => $request->input("type_post"),
                "id_usuario" => Auth()->user()->id,
            ]);
    
            if($catalogo){
                return redirect("/dashboard");
            }else{
               throw ValidationException::withMessages(["general"=>"Se presentó un problema al momento de almacenar los datos"]);
            }
        }catch(QueryException $e){
            throw ValidationException::withMessages(["general"=>"Se presentó un problema al momento de almacenar los datos"]);
        }

    }
    public function updatearchive(Request $request,$id){
        dd(
            $request->all(),
            $id
        );
    }
    
    public function edit($id) {
        // aqui se recibi el id a editar
        $producto = Catalogo::findOrFail($id);
        //    dd($producto);
        // redireccionamos a la vista de index
        return Inertia::render('Catalogos/EditProduct', [
            'producto' => $producto
        ]);
    }

    // aqui recibimos el id  a eliminar de la table
    public function destroy($id){
        // aqui se recibi el id a eliminar
        $producto = Catalogo::findOrFail($id);
        $producto->delete();
        // redireccionamos a la vista de index
        return response()->json([
            'message' => 'Producto eliminado correctamente'
        ]);
    }

    public function signature(){
        // Por si acaso, para la firma se necesita generar el timestamp y enviarlo al front, esto por seguridad de que no vayan a usar una firma antigua
        $timestamp = time();
        $params_to_sign = [
            'timestamp' => $timestamp,
        ];

        $signature = ApiUtils::signParameters($params_to_sign, env('CLOUDINARY_NOTIFICATION_URL'));

        return response()->json([
            'signature' => $signature,
            'timestamp' => $timestamp,
            'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),
            'api_key' => env('CLOUDINARY_API_KEY'),
            'cloud_name' => env('CLOUDINDARY_NAME'),
        ]);
    }

    public function fillFiles($type){
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
