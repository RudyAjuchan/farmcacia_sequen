<?php

namespace App\Http\Controllers;

use App\Models\Detalle_pedido;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Http\Request;

class PedidosController extends Controller
{
    public function index(){
        return Pedido::with(['cliente', 'detallePedido', 'detallePedido.producto', 'detallePedido.loteProducto'])->where('estado', 1)->get();
    }

    
}
