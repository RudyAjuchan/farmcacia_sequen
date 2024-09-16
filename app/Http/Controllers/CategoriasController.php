<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriasController extends Controller
{
    public function index()
    {
        return Categoria::where('estado', 1)->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->isNotEmpty()) {
                return response()->json(['errors' => $errors], 422);
            }
        } else {
            try {
                $categoria = new Categoria();
                $categoria->nombre = $request->nombre;
                $categoria->descripcion = $request->descripcion;
                $categoria->save();
                return response()->json(['message' => 'Categoría creada con éxito'], 201);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al guardar la categoría', 'details' => $e->getMessage()], 500);
            }
        }
    }

    public function show(Categoria $categoria){
        return $categoria;
    }

    public function update(Request $request, Categoria $categoria){
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->isNotEmpty()) {
                return response()->json(['errors' => $errors], 422);
            }
        } else {
            try {
                $categoria->nombre = $request->nombre;
                $categoria->descripcion = $request->descripcion;
                $categoria->save();
                return response()->json(['message' => 'Categoría actualizada con éxito'], 201);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al guardar la categoría', 'details' => $e->getMessage()], 500);
            }
        }
    }

    public function destroy(Categoria $categoria){
        $categoria->estado = 0;
        $categoria->updated_at = now();
        $categoria->save();
        return response()->json(['message' => 'Categoría eliminada con éxito'], 201);
    }
}
