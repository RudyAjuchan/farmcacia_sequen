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
        $hoy = now()->toDateString();
        $productosMasVendidos = Producto::with(['loteProductos','loteProductos.productos', 'loteProductos.producto_promocion.promocion'=> function ($query) use ($hoy) {
            // Carga las promociones pero solo si el estado es 1
            $query
            ->where('fecha_inicio', '<=', $hoy)
                ->where('fecha_fin', '>=', $hoy);
        }])->withCount(['detalleVentas as total_vendido' => function ($query) {
            $query->select(DB::raw('SUM(cantidad)'));
        }])->where('stock', '>', 0)->orderByDesc('total_vendido')->take(10)->get();

        foreach($productosMasVendidos as &$PMV){
            $datosFiltrados = $PMV->lote_productos[0]->producto_promocion->filter(function ($p) {
                return $p->estado !== 0; // Usar ->estado ya que es un objeto
            })->values();
            unset($PMV->lote_productos[0]->producto_promocion);
            $PMV->lote_productos[0]->producto_promocion = $datosFiltrados;
        }

        if ($productosMasVendidos->count() < 4) {
            $productosAjuste = Producto::with(['loteProductos','loteProductos.productos', 'loteProductos.producto_promocion.promocion'=> function ($query) use ($hoy) {
                // Carga las promociones pero solo si el estado es 1
                $query
                ->where('fecha_inicio', '<=', $hoy)
                    ->where('fecha_fin', '>=', $hoy);
            }])->where('stock', '>', 0)->limit(4)->get();

            foreach($productosAjuste as &$PA){
                $datosFiltrados2 = $PA->producto_promocion->filter(function ($p) {
                    return $p->estado !== 0; // Usar ->estado ya que es un objeto
                })->values();
                unset($PA->producto_promocion);
                $PA->producto_promocion = $datosFiltrados2;
            }


            $productosMasVendidos = $productosMasVendidos->merge($productosAjuste);
        }

        return $productosMasVendidos;
    }

    public function productosRecientes(){
        $productosRecientes = Producto::with(['loteProductos'])->where('estado', 1)
        ->where('stock', '>', 0)
        ->orderBy('created_at', 'desc')
        ->take(6)
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

    public function productoEcommerce(Request $request)
    {
        $hoy = now()->toDateString(); // Obtener la fecha actual en formato 'YYYY-MM-DD'

        $producto =  Lote_producto::with(['productos', 'producto_promocion.promocion' => function ($query) use ($hoy) {
            // Carga las promociones pero solo si el estado es 1
            $query
            ->where('fecha_inicio', '<=', $hoy)
                ->where('fecha_fin', '>=', $hoy);
        }])
        ->where('id', $request->id)
        ->first();
        
        $datosFiltrados = $producto->producto_promocion->filter(function ($p) {
            return $p->estado !== 0; // Usar ->estado ya que es un objeto
        })->values();
        unset($producto->producto_promocion);
        $producto->producto_promocion = $datosFiltrados;
        return $producto;
    }





    public function productosSimilaresEcommerce(Request $request){
        // Obtener la subcategoría del producto en el lote
        $loteProducto = Lote_producto::with(['productos', 'productos.subcategoria'])->where('id', $request->id)->first();

        if (!$loteProducto || !$loteProducto->productos || !$loteProducto->productos->subcategoria) {
            return response()->json(['error' => 'Producto o subcategoría no encontrados'], 404);
        }

        $subcategoriaId = $loteProducto->productos->subcategoria->id;

        // Buscar productos similares en la misma subcategoría
        $query = Producto::with(['loteProductos', 'subcategoria'])
            ->where('estado', 1)
            ->where('stock', '>', 0)
            ->whereHas('subcategoria', function ($q) use ($subcategoriaId) {
                $q->where('id', $subcategoriaId);
            })
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return $query;
    }
}
