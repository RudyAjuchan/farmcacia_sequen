<?php

namespace App\Http\Controllers;

use App\Models\Detalle_pedido;
use App\Models\Detalle_venta;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Ventas;
use Illuminate\Http\Request;

class PedidosController extends Controller
{
    public function index(){
        return Pedido::with(['cliente', 'detallePedido', 'detallePedido.producto', 'detallePedido.loteProducto'])->where('estado', '>=', 1)->get();
    }

    public function store(Request $request){
        //buscar el pedido
        $pedido = Pedido::where('id', $request->id)->first();
        //guardamos la venta según el pedido
        $venta = new Ventas();
        $venta->total = $pedido->total;
        $venta->clientes_id = $pedido->clientes_id;
        $venta->save();

        //buscamos el detalle del pedido
        $detalle_pedido = Detalle_pedido::where('pedidos_id', $pedido->id)->get();
        foreach ($detalle_pedido as $DP) {
            $detalle_venta = new Detalle_venta();
            $detalle_venta->cantidad = $DP->cantidad;
            $detalle_venta->precio_unitario = $DP->precio_unitario;
            $detalle_venta->subtotal = $DP->subtotal;
            $detalle_venta->ventas_id = $venta->id;
            $detalle_venta->productos_id = $DP->productos_id;
            $detalle_venta->lote_productos_id = $DP->lote_productos_id;
            $detalle_venta->save();
        }

        //una vez pasado los datos del pedido a la venta cambiamos los estados del pedido y detalle
        $pedido->estado = 2;
        $pedido->updated_at = now();
        $pedido->save();

        foreach($detalle_pedido as $DP){
            $detalle = Detalle_pedido::where('id', $DP->id)->first();
            $detalle->estado = 2;
            $detalle->updated_at = now();
            $detalle->save();
        }

        return response()->json(['message' => 'Pedido confirmado con éxito'], 201);
    }

    public function destroy(Pedido $pedidos2){
        try{
            //Primero cambiamos el estado al pedido
            $pedidos2->estado = 0;
            $pedidos2->updated_at = now();
            $pedidos2->save();
    
            //Ahora el detalle del pedido
            $detalle_pedido = Detalle_pedido::where('pedidos_id', $pedidos2->id)->get();
            foreach($detalle_pedido as $DP){
                $detalle = Detalle_pedido::where('id', $DP->id)->first();
                $detalle->estado = 0;
                $detalle->updated_at = now();
                $detalle->save();
            }
    
            return response()->json(['message' => 'Pedido cancelada con éxito'], 201);
        }catch(\Exception $e){
            return response()->json(['error' => 'Error al eliminar el pedido', 'details' => $e->getMessage()], 500);
        }
    }
}
