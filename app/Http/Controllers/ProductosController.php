<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductosController extends Controller
{
    public function index(){
        return Producto::with(['proveedor'])->where('estado', 1)->get();
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:255',
            'proveedor' => 'required',
            'descripcion' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->isNotEmpty()) {
                return response()->json(['errors' => $errors], 422);
            }
        } else {
            try{
                $producto = new Producto();
                $producto->nombre = $request->nombre;
                $producto->descripcion = $request->descripcion;
                $producto->proveedores_id = $request->proveedor;
                $producto->stock = 0;
                $producto->save();
                return response()->json(['message' => 'Producto creado con éxito'], 201);
            }catch(\Exception $e){
                return response()->json(['error' => 'Error al guardar el producto', 'details' => $e->getMessage()], 500);
            }

        }
    }

    public function update(Request $request, Producto $producto){
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:255',
            'proveedor' => 'required',
            'descripcion' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->isNotEmpty()) {
                return response()->json(['errors' => $errors], 422);
            }
        } else {
            try{
                $producto->nombre = $request->nombre;
                $producto->descripcion = $request->descripcion;
                $producto->proveedores_id = $request->proveedor;
                $producto->stock = 0;
                $producto->save();
                return response()->json(['message' => 'Producto actualizado con éxito'], 201);
            }catch(\Exception $e){
                return response()->json(['error' => 'Error al actualizar el producto', 'details' => $e->getMessage()], 500);
            }

        }
    }

    public function destroy(Producto $producto){
        $producto->estado = 0;
        $producto->updated_at = now();
        $producto->save();
        return response()->json(['message' => 'Producto eliminado con éxito'], 201);
    }
}
