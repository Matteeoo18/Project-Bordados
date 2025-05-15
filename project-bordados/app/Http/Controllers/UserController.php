<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;


class UserController extends Controller
{
    /**
     * Lleva los datos de los usuarios a la vista Pages\Usuarios\index.vue  
     * @return \Inertia\Response
     */
    public function index()
    {
        $usuarios = $this->getUserPagination(); 
        // dd($usuarios->items());
        // die;
        return Inertia::render('Usuarios/index', compact('usuarios'));
    }   

    /**
     * Funcion para activar/desactivar usuarios
     * @param mixed $id
     * @return void
     */
    public function changeStatusUser($id)
    {
        $findUser = User::find($id);
        if(!$findUser){
            return response()->json(["message"=> "El usuario consultado no se pudo encontrar", Response::HTTP_BAD_REQUEST]);
        }

        $findUser->is_active = ($findUser->is_active  == 1) ? 0 : 1;
        $findUser->save();        

        return response()->json(['usuarios'=>$this->getUserPagination(), 'message'=>'Se cambió el estado del usuario '.$findUser->name]);        
    }

    /**
     * Función para filtrar a los usuarios
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function fillUsers(Request $request)
    {
        $dataFill = $request->input("dataFill");

        $user = User::with('roles')->select('id', 'name', 'email','id_rol','is_active', 'created_at')->where('email', 'like', "%$dataFill%")->orWhere('name', 'like', "%$dataFill%")->orderBy("created_at", "desc")->paginate(10);

        if($user->isEmpty()){
            return response()->json(['message'=>'Usuario no encontrado, revise los datos', 'user'=>$user]);
        }

        return response()->json(['message'=>'Se encontró al usuario '.$dataFill,'user'=>$user]);
    }

    public function fillByStatus($status)
    {

        $status = filter_var($status, FILTER_VALIDATE_BOOLEAN); // Por si vuelve a pasar, esto parsea el valor que se envia desde el front a boolean 

        $fillUsers = User::with('roles')->select('id', 'name', 'email','id_rol','is_active', 'created_at')->where('is_active', $status)->orderBy("created_at", "desc")->paginate(20);

        if($fillUsers->isEmpty()){
            return response()->json(['message'=>'No hay usuarios con ese estado','user'=>$fillUsers]);
        }

        return response()->json(['message'=>'Se han filtrado los usuarios','user'=>$fillUsers]);
    }

    /**
     * Funcion que retorna la consulta a la bd usuairos con una paginacion de (20) usuarios 
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUserPagination()
    {
        $usuarios = User::with('roles')->select('id', 'name', 'email','id_rol','is_active', 'created_at')->orderBy("created_at", "desc")->paginate(20);

        return $usuarios;
    }
}
