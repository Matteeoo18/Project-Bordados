<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\DropzoneController;
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

    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('usuarios.index');
        Route::post('/fillterUsers', [UserController::class, 'fillUsers'])->name('usuarios.fill');
        Route::get('/fillUsersBystatus/{status}', [UserController::class, 'fillByStatus'])->name('usuarios.fillByStatus');
        //RUTA PARA DESACTIVAR/ACTIVAR USUARIOS
        Route::put('/updateStatus/{id}', [UserController::class, 'changeStatusUser'])->name('usuarios.updateStatus');
    });
    // aqui van la rutas de los controladores
    Route::get('/dashboard', [CatalogoController::class, 'index'])->name('dashboard');
    // creamos la ruta para crear un producto
    Route::get('/catalogo/create', [CatalogoController::class, 'create'])->name('catalogo.create');
    // aqui se recibi el id a eliminar
    Route::post('/catalogo/store', [CatalogoController::class, 'store'])->name('catalogo.store');
    Route::delete('/catalogo/{id}', [CatalogoController::class, 'destroy'])->name('catalogo.destroy');
    Route::post('/catalogo/{id}', [CatalogoController::class, 'updatearchive'])->name('catalogo.update');
    Route::get('/edit/{id}', [CatalogoController::class, 'edit'])->name('catalogo.edit');
    //Ruta para filtrar los archivos de imagen y video.
    Route::get("/fillFiles/{type}", [CatalogoController::class, 'fillFiles'])->name('catalogo.fillFiles');
    //Ruta para enviar al front la firma del cloudniary 
<<<<<<< HEAD
    Route::get('/cloudinary-signature',[CatalogoController::class,'signature'])->name('catalogo.signature');
    //Ruta de ensayo para el envio de la imagen al back
    Route::put("/catalogo/update", [CatalogoController::class, 'ensayo'])->name('catalogo.ensayo');
=======
    Route::get('/cloudinary-signature', [CatalogoController::class, 'signature'])->name('catalogo.signature');
    // Esta ruta es para el Dropzone
    Route::post('/dropzone', DropzoneController::class)->name('upload.archivo');
>>>>>>> Aricapa
});
