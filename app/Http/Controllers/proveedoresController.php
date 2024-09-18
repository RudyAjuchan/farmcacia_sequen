<?php

namespace App\Http\Controllers;

use App\Models\Proveedores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class proveedoresController extends Controller
{
    public function index(){
        return Proveedores::where('estado', 1)->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:255',
            'telefono' => 'required',
            'email' => 'email',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->isNotEmpty()) {
                return response()->json(['errors' => $errors], 422);
            }
        } else {
            try{
                $proveedor = new Proveedores();
                $proveedor->nombre = $request->nombre;
                $proveedor->contacto = $request->contacto;
                $proveedor->direccion = $request->direccion;
                $proveedor->telefono = $request->telefono;
                $proveedor->email = $request->email;
                $proveedor->save();
                return response()->json(['message' => 'Proveedor creada con éxito', 'data' => $proveedor], 201);
            }catch(\Exception $e){
                return response()->json(['error' => 'Error al guardar el proveedor', 'details' => $e->getMessage()], 500);
            }
        }
    }

    public function update(Request $request, Proveedores $proveedore){
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:255',
            'telefono' => 'required',
            'email' => 'email',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->isNotEmpty()) {
                return response()->json(['errors' => $errors], 422);
            }
        } else {
            try{
                $proveedore->nombre = $request->nombre;
                $proveedore->contacto = $request->contacto;
                $proveedore->direccion = $request->direccion;
                $proveedore->telefono = $request->telefono;
                $proveedore->email = $request->email;
                $proveedore->save();
                return response()->json(['message' => 'Proveedor actualizada con éxito'], 201);
            }catch(\Exception $e){
                return response()->json(['error' => 'Error al actualizar el proveedor', 'details' => $e->getMessage()], 500);
            }
        }
    }

    public function destroy(Proveedores $proveedore){
        $proveedore->estado=0;
        $proveedore->updated_at=now();
        $proveedore->save();
        return response()->json(['message' => 'Proveedor eliminado con éxito'], 201);
    }
}
