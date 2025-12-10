<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormularioController;
use Illuminate\Support\Facades\Route;

// ===============================
// REDIRECCIÓN A DASHBOARD
// ===============================
Route::get('/', function () {
    return redirect()->route('dashboard');
});


// ===============================
// DASHBOARD
// ===============================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');


// ===============================
// FORMULARIOS (CRUD + extras)
// ===============================
Route::middleware('auth')->group(function () {

    // LISTA DE FORMULARIOS
    Route::get('/formularios', [FormularioController::class, 'index'])
        ->name('formularios.index');

    
    // CREAR FORMULARIO
    Route::get('/formularios/crear', [FormularioController::class, 'crear'])
        ->name('formularios.crear');

    // GUARDAR FORMULARIO
    Route::post('/formularios', [FormularioController::class, 'guardar'])
        ->name('formularios.guardar');

    // Editar formulario (constructor)
    Route::get('/formularios/{id}/editar', [FormularioController::class, 'editar'])
        ->name('formularios.editar');

        // ACTUALIZAR FORMULARIO
Route::put('/formularios/{id}', [FormularioController::class, 'actualizar'])
    ->name('formularios.actualizar');



    
    // Guardar estructura completa (secciones/preguntas/opciones)
    Route::post('/formularios/{id}/estructura', [FormularioController::class, 'guardarEstructura'])
        ->name('formularios.guardarEstructura');

        // Secciones y preguntas AJAX
    Route::post('/formularios/{formulario}/secciones', [FormularioController::class, 'storeSeccion'])->name('formularios.secciones.store');
    Route::delete('/formularios/secciones/{seccion}', [FormularioController::class, 'destroySeccion'])->name('formularios.secciones.destroy');
    
    // Preguntas
    Route::post('/secciones/{seccion}/preguntas', [FormularioController::class, 'storePregunta'])->name('formularios.preguntas.store');
    Route::put('/preguntas/{pregunta}', [FormularioController::class, 'updatePregunta'])->name('formularios.preguntas.update');
    Route::delete('/preguntas/{pregunta}', [FormularioController::class, 'destroyPregunta'])->name('formularios.preguntas.destroy');

    // Opciones (crear/borrar rápidas)
    Route::post('/preguntas/{pregunta}/opciones', [FormularioController::class, 'storeOpcion'])->name('formularios.opciones.store');
    Route::delete('/opciones/{opcion}', [FormularioController::class, 'destroyOpcion'])->name('formularios.opciones.destroy');




});


// ===============================
// PERFIL DE USUARIO
// ===============================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});


// ===============================
// AUTENTICACIÓN (Breeze / Jetstream)
// ===============================
require __DIR__.'/auth.php';
