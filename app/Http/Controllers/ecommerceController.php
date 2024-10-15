<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Lote_producto;
use App\Models\Producto;
use App\Models\Subcategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ecommerceController extends Controller
{
    public function productosDestacados(){
        $productosMasVendidos = Producto::withCount(['detalleVentas as total_vendido' => function ($query) {
            $query->select(DB::raw('SUM(cantidad)'));
        }])->where('stock', '>', 0)->orderByDesc('total_vendido')->take(10)->get();

        if ($productosMasVendidos->count() < 4) {
            $productosAjuste = Producto::where('stock', '>', 0)->limit(4)->get();

            $productosMasVendidos = $productosMasVendidos->merge($productosAjuste);
        }

        return $productosMasVendidos;
    }

    public function productosRecientes(){
        $productosRecientes = Producto::where('estado', 1)
        ->where('stock', '>', 0)
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

        return $productosRecientes;
    }

    public function categoriasEcomemrce(){
        return Categoria::where('estado', 1)->get();
    }

    public function subCategoriasEcommerce(){
        return Subcategoria::where('estado', 1)->get();
    }

    public function productosEcommerce(Request $request){
        $query = Lote_producto::with(['productos', 'productos.subcategoria', 'productos.subcategoria.categoria'])
        ->where('estado', 1);

        // Filtro por categoría
        if ($request->filled('categoria')) {
            $query->whereHas('productos.subcategoria.categoria', function ($query) use ($request) {
                $query->where('id', $request->categoria);
            });
        }

        // Filtro por subcategoría
        if ($request->filled('subcategoria')) {
            $query->whereHas('productos.subcategoria', function ($subquery) use ($request) {
                $subquery->where('id', $request->subcategoria);
            });
        }
        // Paginación
        $productos = $query->paginate(6);
        return response()->json($productos);
    }
}
