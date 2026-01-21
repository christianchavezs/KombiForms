<?php

use App\Http\Controllers\Contestar_FormularioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormularioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstructuraFormularioController;
use App\Http\Controllers\Usuarios;

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
// ENLACES PÚBLICOS DE FORMULARIOS
// ===============================

// Enlace único para acceder a un formulario por token
Route::get('/f/{token}', [FormularioController::class, 'acceder'])
    ->name('formularios.acceder');

// Vista para responder un formulario específico
Route::get('/formularios/{id}/responder', [FormularioController::class, 'responder'])
    ->name('formularios.responder');


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

   //Guardar Formulario Estructura Formulario Controler
    

    Route::post('/formularios/{formulario}/estructura', [EstructuraFormularioController::class, 'guardar'] )->name('formularios.estructura.guardar');


    Route::get('/usuarios',[Usuarios::class, 'index'])->name('Usuarios');
    Route::patch('/usuarios/{user}/toggle', [Usuarios::class, 'toggleActivo'])->name('usuarios.toggle');


    Route::get('/formularios/{id}/configuracion', [FormularioController::class, 'configuracion'])
    ->name('formularios.configuracion');

   

    Route::get('/loginAnonimo', function () {
        return view('formularios.loginAnonimo');
    })->name('loginAnonimo');



    // Mostrar la vista con el concentrado
    Route::get('/formularios/{id}/concentrado', [FormularioController::class, 'mostrarConcentrado'])
        ->name('formularios.concentrado');

    // Descargar el Excel desde la vista
    Route::get('/formularios/{id}/concentrado/export', [FormularioController::class, 'concentrarRespuestas'])
        ->name('formularios.concentrarRespuestas');

    Route::get('/formularios/{formulario}', [Contestar_FormularioController::class, 'mostrar']);
    Route::post('/formularios/{formulario}/responder', [Contestar_FormularioController::class, 'responder']);

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


