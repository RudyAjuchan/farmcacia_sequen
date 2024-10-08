<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Detalle_compra;
use App\Models\Lote_producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComprasController extends Controller
{
    public function index(){
        return Compra::with(['proveedor','detalleCompras','detalleCompras.producto', 'detalleCompras.loteProducto'])->where('estado', 1)->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'proveedor' => 'required',
            'fecha_compra' => 'required',
            'total' => 'required',
            'carrito' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->isNotEmpty()) {
                return response()->json(['errors' => $errors], 422);
            }
        } else {
            //primero vamos a guardar los datos de la compra
            $compra = new Compra();
            $compra->fecha_compra = $request->fecha_compra;
            $compra->total = $request->total;
            $compra->proveedores_id = $request->proveedor;
            $compra->save();

            //Ahora vamos a guardar en detalle de la compra y lotes
            foreach($request->carrito as  $CAR){
                //antes del detalle de la compra se guarda en lotes productos
                $lote = new Lote_producto();
                $lote->cantidad = $CAR['cantidad'];
                $lote->fecha_compra = $request->fecha_compra;
                $lote->cantidad_restante = $CAR['cantidad'];
                $lote->precio = $CAR['venta'];
                $lote->fecha_vencimiento = $CAR['fecha_vencimiento'];
                $lote->productos_id = $CAR['producto']['id'];
                $lote->save();

                //ahora si el detalle de la compra
                $detalle_compra = new Detalle_compra();
                $detalle_compra->cantidad = $CAR['cantidad'];
                $detalle_compra->precio_unitario = $CAR['venta'];
                $detalle_compra->precio_compra = $CAR['compra'];
                $detalle_compra->compras_id = $compra->id;
                $detalle_compra->productos_id = $CAR['producto']['id'];
                $detalle_compra->lote_productos_id = $lote->id;
                $detalle_compra->save();
            }
            return response()->json(['message' => 'compra creada con éxito'], 201);

        }
    }

    public function update(Request $request, Compra $compra){
        $validator = Validator::make($request->all(), [
            'dataCompra' => 'required',
            'dataDetalle' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->isNotEmpty()) {
                return response()->json(['errors' => $errors], 422);
            }
        } else {
            $compra->fecha_compra = $request->dataCompra['fecha_compra'];
            $compra->total = $request->dataCompra['total'];
            $compra->proveedores_id = $request->dataCompra['proveedor'];
            $compra->save();

            //Ahora vamos a guardar en detalle de la compra y lotes
            foreach($request->dataDetalle as  $CAR){
                if($CAR["nuevo"]){
                    //se inserta
                }else{
                    //se actualiza
                    //PRIMERO LOS LOTES
                    $lote = Lote_producto::where('id', $CAR['lote_id'])->first();
                    $lote->cantidad = $CAR['cantidad'];
                    $lote->fecha_compra = $request->dataCompra['fecha_compra'];
                    $lote->precio = $CAR['venta'];
                    $lote->fecha_vencimiento = $CAR['fecha_vencimiento'];
                    $lote->productos_id = $CAR['producto']['id'];
                    $lote->save();

                    $detalle_compra = Detalle_compra::where('id', $CAR['id'])->first();
                    $detalle_compra->cantidad = $CAR['cantidad'];
                    $detalle_compra->precio_unitario = $CAR['venta'];
                    $detalle_compra->precio_compra = $CAR['compra'];
                    $detalle_compra->compras_id = $compra->id;
                    $detalle_compra->productos_id = $CAR['producto']['id'];
                    $detalle_compra->lote_productos_id = $lote->id;
                    $detalle_compra->save();
                }
            }
            return response()->json(['message' => 'compra actualizada con éxito'], 201);
        }
    }

    public function destroy(Compra $compra){
        //cambiamos estado a la compra
        $compra->estado = 0;
        $compra->updated_at = now();
        $compra->save();
        
        //cambiamos estado al detalle y al lote también
        $detalle_compra = Detalle_compra::where('compras_id', $compra->id)->get();
        foreach($detalle_compra as $DC){
            $detalle = Detalle_compra::where('id', $DC->id)->first();
            $detalle->estado = 0;
            $detalle->updated_at = now();
            $detalle->save();

            $lote = Lote_producto::where('id', $detalle->lote_productos_id)->first();
            $lote->estado = 0;
            $lote->updated_at = now();
            $lote->save();
        }
        return response()->json(['message' => 'compra eliminada con éxito'], 201);
    }
}
