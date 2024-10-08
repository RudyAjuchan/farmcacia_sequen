<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\clientesController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\proveedoresController;
use App\Http\Controllers\subCategoriaController;
use App\Http\Controllers\ventasController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\RoleRedirect;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return redirect('/');
});

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
    //RUTAS PARA SUBIR IMÀGENES
    Route::post('/upload', [FileUploadController::class, 'store']);
    Route::post('/uploadDelete', [FileUploadController::class, 'eliminarImagen']);
});

require __DIR__.'/auth.php';
