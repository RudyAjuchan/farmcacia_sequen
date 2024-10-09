<?php

namespace App\Http\Controllers;

use App\Models\Detalle_venta;
use App\Models\Ventas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ventasController extends Controller
{
    public function index(){
        return Ventas::with(['user', 'detalleVentas', 'detalleVentas.producto', 'detalleVentas.loteProducto'])->where('estado', 1)->get();
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
            $venta->users_id = session('id');
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
}
