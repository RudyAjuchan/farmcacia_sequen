<?php

namespace App\Http\Controllers;

use App\Models\Lote_producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class loteProductoController extends Controller
{
    public function index(){
        return Lote_producto::select(DB::raw("lote_productos.id, CONCAT(productos.nombre, ' (Q. ', lote_productos.precio, ')', ' - ' , lote_productos.cantidad_restante, ' restantes') as nombre, lote_productos.precio, lote_productos.cantidad_restante, productos.nombre as nombreAux, productos.id as idProducto"))
        ->join('productos', 'productos.id', 'lote_productos.productos_id')
        ->where('lote_productos.estado', 1)->get();
    }

    public function loteProductosPromociones(){
        return Lote_producto::select(DB::raw("lote_productos.id, CONCAT(productos.nombre, ' (Q. ', lote_productos.precio, ')', ' - ' , lote_productos.cantidad_restante, ' restantes', ' Fecha vencimiento: ', lote_productos.fecha_vencimiento) as nombre, lote_productos.precio, lote_productos.cantidad_restante, productos.nombre as nombreAux, productos.id as idProducto"))
        ->join('productos', 'productos.id', 'lote_productos.productos_id')
        ->where('lote_productos.estado', 1)->get();
    }
}
