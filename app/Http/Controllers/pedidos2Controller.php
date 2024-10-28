<?php

namespace App\Http\Controllers;

use App\Models\Detalle_pedido;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Http\Request;

class pedidos2Controller extends Controller
{
    public function store(Request $request){
        $pedido = new Pedido();
            $pedido->total = $request->total;
            $pedido->clientes_id = $request->usuario["id"];
            $pedido->total = $request->total;
            $pedido->save();

            //Guardamos el detalle del pedido
            foreach($request->productos as $CAR){
                $detalle_pedido = new Detalle_pedido();
                $detalle_pedido->cantidad = $CAR['cantidad'];
                $detalle_pedido->precio_unitario = $CAR['precio'];
                $detalle_pedido->subtotal = $CAR['subtotal'];
                $detalle_pedido->pedidos_id = $pedido->id;
                $detalle_pedido->productos_id = $CAR['id'];
                $detalle_pedido->lote_productos_id = $CAR['lote_productos_id'];
                $detalle_pedido->save();
            }

            //ACTUALIZAR DATOS DEL CLIENTE
            $cliente = User::find($request->usuario["id"]);
            $cliente->direccion = $request->usuario["direccion"];
            $cliente->nit = $request->usuario["nit"];
            $cliente->telefono = $request->usuario["telefono"];
            $cliente->save();
            return response()->json(['message' => 'Venta hecha con éxito'], 201);
    }
}
