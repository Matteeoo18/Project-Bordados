<?php

use App\Http\Controllers\CatalogoController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // aqui van la rutas de los controladores
    Route::get('/dashboard', [CatalogoController::class, 'index'])->name('dashboard');
    // creamos la ruta para crear un producto
    Route::get('/catalogo/create', [CatalogoController::class, 'create'])->name('catalogo.create');
    // aqui se recibi el id a eliminar
    Route::post('/catalogo/store', [CatalogoController::class, 'store'])->name('catalogo.store');
    Route::delete('/catalogo/{id}', [CatalogoController::class, 'destroy'])->name('catalogo.destroy');
    Route::get('/edit/{id}', [CatalogoController::class, 'edit'])->name('catalogo.edit');
});
