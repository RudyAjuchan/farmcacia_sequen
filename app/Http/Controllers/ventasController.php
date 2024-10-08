<?php

namespace App\Http\Controllers;

use App\Models\Ventas;
use Illuminate\Http\Request;

class ventasController extends Controller
{
    public function index(){
        return Ventas::with(['user', 'detalle_venta', 'detalle_venta.producto', 'detalle_venta.loteProducto'])->where('estado', 1)->get();
    }
}
