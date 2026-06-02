<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Contestar_FormularioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormularioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstructuraFormularioController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\Usuarios;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// ===============================
// REDIRECCIÓN A DASHBOARD
// ===============================
Route::get('/', function () {
    return redirect()->route('dashboard');

});


// ======================================================
// 🔹 Autenticación con Google
// ======================================================
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
Route::get('/formularios/gracias', [Contestar_FormularioController::class, 'gracias'])->name('gracias');


// ======================================================
// 🔹 Verificación de correo
// ======================================================

// Vista donde se avisa que debe verificar
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Link del correo
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

// Reenviar correo de verificación
Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// ===============================
// DASHBOARD
// ===============================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');
Route::get('/formulario_anonimo/{formulario}', [Contestar_FormularioController::class, 'mostrar']) ->name('mostrar_anonimos');

// ===============================
// ENLACES PÚBLICOS DE FORMULARIOS
// ===============================
/*
// Enlace único para acceder a un formulario por token
Route::get('/f/{token}', [FormularioController::class, 'acceder'])
    ->name('formularios.acceder');

// Vista para responder un formulario específico
Route::get('/formularios/{id}/responder', [FormularioController::class, 'responder'])
    ->name('formularios.responder');


    */
// ===============================
// ENLACES PÚBLICOS DE FORMULARIOS
// ===============================

// Acceso por token (decide si login normal o login anónimo)
Route::get('/f/{token}', [FormularioController::class, 'acceder'])
    ->name('formularios.acceder');

// Vista para responder un formulario específico (mostrar preguntas)
Route::get('/formularios/{id}/responder', [FormularioController::class, 'responder'])
    ->name('formularios.responder');

// Guardar respuestas (autenticados o anónimos)
Route::post('/formularios/{formulario}/responder', [Contestar_FormularioController::class, 'responder'])
    ->name('formularios.responder.guardar');

// Vista anónima directa
Route::get('/formulario_anonimo/{formulario}', [Contestar_FormularioController::class, 'mostrar'])
    ->name('mostrar_anonimos');





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


    
    Route::post('/formularios/{id}/modo', [FormularioController::class, 'actualizarModo']);

   
    //EVALUACIONES:


    Route::get('/formularios/{id}/evaluaciones', 
        [Contestar_FormularioController::class, 'evaluaciones']
    )->name('formularios.evaluaciones');


    // ===============================================
    // VER DETALLE DE EVALUACIÓN
    // ===============================================
    Route::get(
        '/respuestas/{id}/evaluar',
        [App\Http\Controllers\Contestar_FormularioController::class, 'evaluarRespuesta']
    )->name('respuestas.evaluar');

   // ===============================================
    // GUARDAR EVALUACIÓN MANUAL (texto corto/parrafo)
    // ===============================================
    Route::post('/respuestas/{id}/evaluar',
        [Contestar_FormularioController::class, 'guardarEvaluacionManual']
    )->name('respuestas.evaluar.guardar');


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

    Route::get('/formularios/{formulario}', [Contestar_FormularioController::class, 'mostrar']) ->name('mostrar');
    /* Route::post('/formularios/{formulario}/responder', [Contestar_FormularioController::class, 'responder']);*/

    
    Route::patch('/usuarios/{user}/toggle', [Usuarios::class, 'toggleActivo'])->name('usuarios.toggle');
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

