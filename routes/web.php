<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\proveedoresController;
use App\Http\Controllers\subCategoriaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return redirect('/');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /* RUTAS PARA CATEGORIAS */
    Route::apiResource('categorias', CategoriasController::class);
    /* RUTAS PARA SUB CATEGORIAS */
    Route::apiResource('subcategorias', subCategoriaController::class);
    /* RUTAS PARA PROVEEDORES */
    Route::apiResource('proveedores', proveedoresController::class);
});

require __DIR__.'/auth.php';
