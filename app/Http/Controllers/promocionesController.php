<?php

namespace App\Http\Controllers;

use App\Models\producto_promociones;
use App\Models\Promociones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class promocionesController extends Controller
{
    public function index(){
        return producto_promociones::with(['loteProductos', 'promocion', 'loteProductos.productos'])->where('estado', 1)->get();
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'descuento' => 'required|numeric',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'lote' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->isNotEmpty()) {
                return response()->json(['errors' => $errors], 422);
            }
        } else {
            try{
                $promocion = new Promociones();
                $promocion->descripcion = $request->descripcion;
                $promocion->descuento = $request->descuento;
                $promocion->fecha_inicio = $request->fecha_inicio;
                $promocion->fecha_fin = $request->fecha_fin;
                $promocion->save();
    
                //GUARDAMOS EN DETALLE PROMOCIÓN
                $promocion_producto = new producto_promociones();
                $promocion_producto->lote_productos_id = $request->lote;
                $promocion_producto->promociones_id = $promocion->id;
                $promocion_producto->save();
                return response()->json(['message' => 'Promoción creada con éxito'], 201);
            }catch(\Exception $e){
                return response()->json(['error' => 'Error al guardar la promoción', 'details' => $e->getMessage()], 500);
            }
        }
    }

    public function update(Request $request, producto_promociones $promocion_producto){
        return $promocion_producto;
    }
}
