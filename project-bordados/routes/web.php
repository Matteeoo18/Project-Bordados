<?php

use App\Http\Controllers\UserController;
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
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    
    Route::prefix('usuarios')->group(function (){
        Route::get('/', [UserController::class, 'index'])->name('usuarios.index');
        Route::post('/fillterUsers', [UserController::class, 'fillUsers'])->name('usuarios.fill');
    });
    // aqui van la rutas de los controladores
    Route::get('/dashboard', [CatalogoController::class, 'index'])->name('dashboard');
    // aqui se recibi el id a eliminar
    Route::delete('/catalogo/{id}', [CatalogoController::class, 'destroy'])->name('catalogo.destroy');
    Route::get('/edit/{id}', [CatalogoController::class, 'edit'])->name('catalogo.edit');
});
