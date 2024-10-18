<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Lote_producto;
use App\Models\Producto;
use App\Models\producto_promociones;
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

    public function productosEcommerce(Request $request) {
        $query = Lote_producto::with(['productos', 'productos.subcategoria', 'productos.subcategoria.categoria'])
        ->where('estado', 1)
        ->whereIn('id', function ($subquery) {
            $subquery->selectRaw('MIN(id)')
            ->from('lote_productos')
            ->where('cantidad_restante', '>', 0)
                ->where('estado', 1)
                ->groupBy('productos_id');
        });
    
        // Filtro por búsqueda de nombre de producto
        if ($request->filled('buscar')) {
            $query->whereHas('productos', function ($query) use ($request) {
                $query->where('nombre', 'like', '%' . $request->buscar . '%');
            });
        }
    
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
    

    public function productosPromocion(Request $request){
        $query = producto_promociones::with(['loteProductos', 'promocion', 'loteProductos.productos'])
        ->where('estado', 1)
        ->whereHas('promocion', function ($query) {
            $hoy = now()->toDateString(); // Obtener la fecha actual en formato 'YYYY-MM-DD'
            $query->where('fecha_inicio', '<=', $hoy)
            ->where('fecha_fin', '>=', $hoy);
        });


        // Filtro por categoría
        if ($request->filled('categoria')) {
            $query->whereHas('loteProductos.productos.subcategoria.categoria', function ($query) use ($request) {
                $query->where('id', $request->categoria);
            });
        }
    
        // Filtro por subcategoría
        if ($request->filled('subcategoria')) {
            $query->whereHas('loteProductos.productos.subcategoria', function ($subquery) use ($request) {
                $subquery->where('id', $request->subcategoria);
            });
        }

        $precios = [
            0 => 200,
            1 => 400,
            2 => 600,
            3 => 800,
            4 => 1000,
        ];

        //Filtro por precio
        if ($request->filled('precio') && $request->precio!=5){
            $query->whereHas('loteProductos', function ($query) use ($request, $precios) {
                $query->where('precio', '<=', $precios[$request->precio]);
            });
        }

        $productos = $query->paginate(6);

        return response()->json($productos);
    }
}
