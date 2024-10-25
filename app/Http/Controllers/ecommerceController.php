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
        //buscamos los productos más vendidos
        $detalle_venta = DB::table('detalle_ventas')->select(DB::raw('SUM(cantidad) as cantidad_vendida'), 'productos_id')->groupBy('productos_id')->orderByDesc('cantidad_vendida')->take(10)->get()->toArray();

        //obtenemos un array
        $idsProductos = array_column($detalle_venta, 'productos_id');

        //validamos si hay por lo menos 10 productos más vendidos, sino entonces rellenamos de productos normales
        if(sizeof($idsProductos)<10){
            $cantidad = 10 - sizeof($idsProductos);
            $productosAjuste = DB::table('productos')->whereNotIn('id',$idsProductos)->where('estado', 1)->take($cantidad)->get()->toArray();
            $idsProductosAjuste = array_column($productosAjuste, 'id');
            $idsProductos = array_merge($idsProductos, $idsProductosAjuste);
        }

        $productos = DB::table('productos')->whereIn('id',$idsProductos)->where('estado', 1)->take(10)->get();

        foreach($productos as &$PROD){
            $lote = DB::table('lote_productos')->where('estado', 1)->where('cantidad_restante', '>', 0)->where('productos_id', $PROD->id)->first();
            $PROD->lote_productos = $lote;

            $producto_promocion = DB::table('producto_promociones')->where('lote_productos_id', $lote->id)->where('estado', 1)->get();

            $hoy = now()->toDateString();
            foreach($producto_promocion as &$PP){
                $promocion = DB::table('promociones')
                ->where('fecha_inicio', '<=', $hoy)
                ->where('fecha_fin', '>=', $hoy)
                ->where('estado', 1)
                ->where('id', $PP->promociones_id)->first();
                $PP->promocion = $promocion;
            }

            $PROD->promociones = $producto_promocion;
        }

        return $productos;
    }

    public function productosRecientes(){
        $productosRecientes = Producto::where('estado', 1)
        ->where('stock', '>', 0)
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();

        foreach($productosRecientes as &$PROD){
            $lote = DB::table('lote_productos')->where('estado', 1)->where('cantidad_restante', '>', 0)->where('productos_id', $PROD->id)->first();
            $PROD->lote_productos = $lote;

            $producto_promocion = DB::table('producto_promociones')->where('lote_productos_id', $lote->id)->where('estado', 1)->get();

            $hoy = now()->toDateString();
            foreach($producto_promocion as &$PP){
                $promocion = DB::table('promociones')
                ->where('fecha_inicio', '<=', $hoy)
                ->where('fecha_fin', '>=', $hoy)
                ->where('estado', 1)
                ->where('id', $PP->promociones_id)->first();
                $PP->promocion = $promocion;
            }

            $PROD->promociones = $producto_promocion;
        }

        return $productosRecientes;
    }

    public function categoriasEcomemrce(){
        return Categoria::where('estado', 1)->get();
    }

    public function subCategoriasEcommerce(){
        return Subcategoria::where('estado', 1)->get();
    }

    public function productosEcommerce(Request $request) {
        $hoy = now()->toDateString(); // Obtener la fecha actual en formato 'YYYY-MM-DD'

        $query = Lote_producto::with(['productos', 'productos.subcategoria', 'productos.subcategoria.categoria', 'producto_promocion' => function ($query) {
            $query->where('estado', 1);
        }, 'producto_promocion.promocion' => function ($query) use ($hoy) {
            $query
            ->where('fecha_inicio','<=',$hoy)->where('fecha_fin', '>=', $hoy);
        }])
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
