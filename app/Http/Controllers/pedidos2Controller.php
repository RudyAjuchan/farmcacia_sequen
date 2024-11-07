<?php

namespace App\Http\Controllers;

use App\Models\Detalle_pedido;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

        /* PROCEDEMOS A ARMAR LA TABLA A ENVIAR EN EL CORREO */
        $tabla = "
            <table border='1' style='border-collapse: collapse;'>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Descuento</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
        ";

        foreach($request->productos as $CAR){
            $tabla.=    "<tr>
                            <td>{$CAR['nombre']}</td>
                            <td>{$CAR['cantidad']}</td>
                            <td>{$CAR['precio']}</td>
                            <td>{$CAR['descuento']}</td>
                            <td>{$CAR['subtotal']}</td>
                        </tr>";
        }

        $tabla.=    "<tr>
                        <td colspan='4'>Total</td>
                        <td><span style='color: red; font-weight: bold;'>Q. {$request->total}</span></td>
                    </tr>
                    </tbody>
                    </table>";

        /* PROCEDEMOS A ENVIAR CORREO AL ADMINISTRADOR Y AL CLIENTE */
        Mail::send([], [], function ($message) use ($cliente) {
            $message->to('farmaciasequen@gmail.com')
            ->subject('Nuevo Pedido')
            ->html("
                    <p>El usuario {$cliente->name} ha hecho un pedido, por favor dirigete al sistema para gestionar el pedido.</p>
                ");
        });
        //Ahora enviamos el correo al cliente con el listado de los productos
        Mail::send([], [], function ($message) use ($tabla, $cliente) {
            $message->to($cliente->email)
                ->subject('Pedido en espera')
                ->html("
                    <p>Estimado(a) {$cliente->name} su pedido fue solicitado exitosamente, le damos el resumen de su pedido:</p>
                    {$tabla}
                    <p>Le pedimos su paciencia para que su pedido sea aprobado.</p>
                ");
        });

        return response()->json(['message' => 'Venta hecha con éxito'], 201);
    }
}
