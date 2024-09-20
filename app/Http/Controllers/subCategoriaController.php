<?php

namespace App\Http\Controllers;

use App\Models\Subcategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class subCategoriaController extends Controller
{
    public function index(){
        return Subcategoria::where('estado', 1)->get();
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:255',
            'categoria' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->isNotEmpty()) {
                return response()->json(['errors' => $errors], 422);
            }
        } else {
            try{
                $subcategoria = new Subcategoria();
                $subcategoria->nombre = $request->nombre;
                $subcategoria->descripcion = $request->descripcion;
                $subcategoria->categorias_id = $request->categoria;
                $subcategoria->save();
                return response()->json(['message' => 'Categoría creada con éxito', 'data' => $subcategoria], 201);
            }catch (\Exception $e){
                return response()->json(['error' => 'Error al guardar la categoría', 'details' => $e->getMessage()], 500);
            }
        }
    }

    public function update(Request $request, Subcategoria $subcategoria){
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:255',
            'categoria' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->isNotEmpty()) {
                return response()->json(['errors' => $errors], 422);
            }
        } else {
            try{
                $subcategoria->nombre = $request->nombre;
                $subcategoria->descripcion = $request->descripcion;
                $subcategoria->categorias_id = $request->categoria;
                $subcategoria->save();
                return response()->json(['message' => 'Categoría actualizada con éxito'], 201);
            }catch (\Exception $e){
                return response()->json(['error' => 'Error al guardar la categoría', 'details' => $e->getMessage()], 500);
            }
        }
    }

    public function destroy(Subcategoria $subcategoria){
        $subcategoria->estado = 0;
        $subcategoria->updated_at = now();
        $subcategoria->save();
        return response()->json(['message' => 'Categoría eliminada con éxito'], 201);
    }
}
