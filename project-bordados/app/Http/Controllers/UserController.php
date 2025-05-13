<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;


class UserController extends Controller
{
    /**
     * Lleva los datos de los usuarios a la vista Pages\Usuarios\index.vue  
     * 
     * @return \Inertia\Response
     */
    public function index()
    {
        $usuarios = User::with('roles')->select('id', 'name', 'email','id_rol','is_active', 'created_at')->orderBy("created_at", "desc")->paginate(20);
        // dd($usuarios->items());
        // die;
        return Inertia::render('Usuarios/index', compact('usuarios'));
    }

    public function fillUsers(Request $request)
    {
        $dataFill = $request->input("dataFill");

        $user = User::with('roles')->select('id', 'name', 'email','id_rol','is_active', 'created_at')->where('email', 'like', "%$dataFill%")->orWhere('name', 'like', "%$dataFill%")->orderBy("created_at", "desc")->paginate(10);

        return response()->json(['user'=>$user]);
    }


}
