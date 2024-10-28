<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\clientesController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\ecommerceController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\loteProductoController;
use App\Http\Controllers\pedidos2Controller;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\promocionesController;
use App\Http\Controllers\proveedoresController;
use App\Http\Controllers\subCategoriaController;
use App\Http\Controllers\ventasController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\RoleRedirect;
use Illuminate\Support\Facades\Route;

Route::get('/admin', function () {
    return view('login');
});
Route::get('/signing_up', function () {
    return view('register');
});
Route::get('/log_in', function () {
    return view('login_cliente');
});

Route::get('/', function () {
    return view('ecommerce');
})->name('home');

Route::get('/login', function () {
    return redirect('/');
});

Route::get('/user', function () {
    return response()->json(auth()->user());
})->name('user');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', RoleRedirect::class])->name('dashboard');

Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /* RUTAS PARA CATEGORIAS */
    Route::apiResource('categorias', CategoriasController::class);
    /* RUTAS PARA SUB CATEGORIAS */
    Route::apiResource('subcategorias', subCategoriaController::class);
    /* RUTAS PARA PROVEEDORES */
    Route::apiResource('proveedores', proveedoresController::class);
    /* RUTAS PARA PRODUCTOS */
    Route::apiResource('productos', ProductosController::class);
    /* RUTAS PARA COMPRAS */
    Route::apiResource('compras', ComprasController::class);
    /* RUTAS PARA VENTAS */
    Route::apiResource('ventas', ventasController::class);
    /* RUTAS PARA CLIENTES */
    Route::apiResource('clientes', clientesController::class);
    /* RUTAS PARA LOTE PRODUCTO */
    Route::apiResource('loteProductos', loteProductoController::class);
    Route::get('/loteProductosPromociones', [loteProductoController::class, 'loteProductosPromociones'])->name('productos.destacados');
    /* RUTAS PARA LOTE PRODUCTO */
    Route::apiResource('promociones', promocionesController::class);
    /* RUTAS PARA LOTE Pedidos */
    Route::apiResource('pedidos2', PedidosController::class);
    //RUTAS PARA SUBIR IMÀGENES
    Route::post('/upload', [FileUploadController::class, 'store']);
    Route::post('/uploadDelete', [FileUploadController::class, 'eliminarImagen']);
});

/* RUTAS PÚBLICAS */
Route::get('/productosDestacadas', [ecommerceController::class, 'productosDestacados'])->name('productos.destacados');
Route::get('/productosRecientes', [ecommerceController::class, 'productosRecientes'])->name('productos.recientes');
Route::get('/categoriasEcomemrce', [ecommerceController::class, 'categoriasEcomemrce'])->name('categorias.ecommerce');
Route::get('/subCategoriasEcommerce', [ecommerceController::class, 'subCategoriasEcommerce'])->name('subcategorias.ecommerce');
Route::get('/productosEcommerce', [ecommerceController::class, 'productosEcommerce'])->name('productos.ecommerce');
Route::get('/productosPromocion', [ecommerceController::class, 'productosPromocion'])->name('productos.promocion');
Route::post('/productoEcommerce', [ecommerceController::class, 'productoEcommerce'])->name('producto.ecommerce');
Route::post('/productosSimilaresEcommerce', [ecommerceController::class, 'productosSimilaresEcommerce'])->name('productosSimilares.ecommerce');
Route::apiResource('pedidos', pedidos2Controller::class);

require __DIR__.'/auth.php';
