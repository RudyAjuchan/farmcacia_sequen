<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use Illuminate\Http\Request;

class clientesController extends Controller
{
    public function index(){
        return User::where('estado', 1)->where('role', 'cliente')->get();
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->isNotEmpty()) {
                return response()->json(['errors' => $errors], 422);
            }
        } else {
            $user = new User();
            $user->name = $request->nombre;
            $user->email = $request->email;
            $user->direccion = $request->direccion;
            $user->nit = $request->nit;
            $user->telefono = $request->telefono;
            $user->save();
            return response()->json(['message' => 'Cliente creado con éxito', 'data' => $user], 201);
        }
    }
}
