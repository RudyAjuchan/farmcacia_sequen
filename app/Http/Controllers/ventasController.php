<?php

namespace App\Http\Controllers;

use App\Models\Detalle_venta;
use App\Models\Ventas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ventasController extends Controller
{
    public function index(){
        return Ventas::with(['user', 'cliente', 'detalleVentas', 'detalleVentas.producto', 'detalleVentas.loteProducto'])->where('estado', 1)->get();
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'cliente' => 'required',
            'carrito' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->isNotEmpty()) {
                return response()->json(['errors' => $errors], 422);
            }
        } else {
            //Guardamos primero la venta
            
            $venta = new Ventas();
            $venta->total = $request->total;
            $venta->users_id = auth()->user()->id;
            $venta->clientes_id = $request->cliente;
            $venta->save();

            //Guardamos el detalle de venta
            foreach($request->carrito as $CAR){
                $detalle_venta = new Detalle_venta();
                $detalle_venta->cantidad = $CAR['cantidad'];
                $detalle_venta->precio_unitario = $CAR['precio'];
                $detalle_venta->subtotal = $CAR['subtotal'];
                $detalle_venta->ventas_id = $venta->id;
                $detalle_venta->productos_id = $CAR['producto']['idProducto'];
                $detalle_venta->lote_productos_id = $CAR['producto']['id'];
                $detalle_venta->save();
            }
            return response()->json(['message' => 'Venta hecha con éxito'], 201);
            
        }
    }

    public function destroy(Ventas $venta){
        //Primero cambiamos el estado a la venta
        $venta->estado = 0;
        $venta->updated_at = now();
        $venta->save();

        //Ahora el detalle de la venta
        $detalle_venta = Detalle_venta::where('ventas_id', $venta->id)->get();
        foreach($detalle_venta as $DV){
            $detalle = Detalle_venta::where('id', $DV->id)->first();
            $detalle->estado = 0;
            $detalle->updated_at = now();
            $detalle->save();
        }

        return response()->json(['message' => 'Venta cancelada con éxito'], 201);
    }
}
