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


// ======================================================
// 🔹 RUTAS PÚBLICAS
// ======================================================

// Redirección inicial: envía al usuario directamente al dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ------------------------------------------------------
// 🔹 Autenticación con Google
// ------------------------------------------------------
// Inicia el proceso de login con Google
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.login');
// Callback que recibe la respuesta de Google después del login
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
// Vista de agradecimiento (página segura para usuarios bloqueados)
Route::get('/formularios/gracias', [Contestar_FormularioController::class, 'gracias'])->name('gracias');

// ------------------------------------------------------
// 🔹 Verificación de correo electrónico
// ------------------------------------------------------
// Vista que avisa al usuario que debe verificar su correo
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Link de verificación enviado por correo
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

// Reenvío del correo de verificación
Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// ------------------------------------------------------
// 🔹 Formularios públicos / anónimos
// ------------------------------------------------------
// Acceso a un formulario mediante token (decide login normal o anónimo)
Route::get('/f/{token}', [FormularioController::class, 'acceder'])->name('formularios.acceder');

// Vista para responder un formulario específico (mostrar preguntas)
Route::get('/formularios/{id}/responder', [FormularioController::class, 'responder'])->name('formularios.responder');

// Guardar respuestas de un formulario (autenticados o anónimos)
Route::post('/formularios/{formulario}/responder', [Contestar_FormularioController::class, 'responder'])->name('formularios.responder.guardar');

// Vista anónima directa de un formulario
Route::get('/formulario_anonimo/{formulario}', [Contestar_FormularioController::class, 'mostrar'])->name('mostrar_anonimos');

// Iniciar un formulario en modo anónimo (guarda en sesión y redirige)
Route::get('/anonimo/iniciar/{formulario}', function ($formulario) {
    session(['acceso_anonimo_formulario' => $formulario]);
    return redirect()->route('mostrar_anonimos', $formulario);
})->name('anonimo.iniciar');

// ------------------------------------------------------
// 🔹 Autenticación (Breeze / Jetstream)
// ------------------------------------------------------
require __DIR__.'/auth.php';


// ======================================================
// 🔹 RUTAS PARA RESPONDER FORMULARIOS (usuarios autenticados)
// ======================================================
// Cualquier usuario autenticado (administrador, creador o usuario)
// puede acceder únicamente al formulario para responderlo.
Route::middleware('auth')->group(function () {

    Route::get(
        '/formularios/{formulario}/contestar',
        [Contestar_FormularioController::class, 'mostrar']
    )->name('formularios.contestar');

});





// ======================================================
// 🔒 RUTAS PRIVADAS: Dashboard, Formularios (CRUD + extras) y Usuarios
// ======================================================
// Estas rutas requieren autenticación y además pasan por el middleware
// `verifica.noUsuario` para bloquear a los perfiles con rol "usuario".
Route::middleware(['auth','verifica.noUsuario'])->group(function () {

    // ------------------------------------------------------
    // 🔹 Dashboard
    // ------------------------------------------------------
    // Pantalla inicial del sistema, accesible solo para roles válidos (ej. "creador")
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //Compartir formulario
    Route::get('/formularios/{id}/compartir', [FormularioController::class, 'obtenerDatosCompartir'])->name('formularios.obtenerCompartir');
    Route::post('/formularios/{id}/compartir', [FormularioController::class, 'compartir'])->name('formularios.compartir');


    // ------------------------------------------------------
    // 🔹 Perfil de usuario
    // ------------------------------------------------------
    // Editar perfil del usuario autenticado
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Actualizar perfil del usuario autenticado
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Eliminar cuenta del usuario autenticado
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ------------------------------------------------------
    // 🔹 Formularios CRUD
    // ------------------------------------------------------
    Route::get('/formularios', [FormularioController::class, 'index'])->name('formularios.index');
    Route::get('/formularios/crear', [FormularioController::class, 'crear'])->name('formularios.crear');
    Route::post('/formularios', [FormularioController::class, 'guardar'])->name('formularios.guardar');
    Route::get('/formularios/{id}/editar', [FormularioController::class, 'editar'])->name('formularios.editar');
    Route::put('/formularios/{id}', [FormularioController::class, 'actualizar'])->name('formularios.actualizar');
    Route::post('/formularios/{id}/modo', [FormularioController::class, 'actualizarModo']);
    // Eliminar formulario
    Route::delete('/formularios/{id}', [FormularioController::class, 'destroy'])->name('formularios.destroy');


    // ------------------------------------------------------
    // 🔹 Evaluaciones
    // ------------------------------------------------------
    Route::get('/formularios/{id}/evaluaciones', [Contestar_FormularioController::class, 'evaluaciones'])->name('formularios.evaluaciones');
    Route::get('/respuestas/{id}/evaluar', [Contestar_FormularioController::class, 'evaluarRespuesta'])->name('respuestas.evaluar');
    Route::post('/respuestas/{id}/evaluar', [Contestar_FormularioController::class, 'guardarEvaluacionManual'])->name('respuestas.evaluar.guardar');

    // ------------------------------------------------------
    // 🔹 Secciones y preguntas (AJAX)
    // ------------------------------------------------------
    Route::post('/formularios/{formulario}/secciones', [FormularioController::class, 'storeSeccion'])->name('formularios.secciones.store');
    Route::delete('/formularios/secciones/{seccion}', [FormularioController::class, 'destroySeccion'])->name('formularios.secciones.destroy');
    Route::post('/secciones/{seccion}/preguntas', [FormularioController::class, 'storePregunta'])->name('formularios.preguntas.store');
    Route::put('/preguntas/{pregunta}', [FormularioController::class, 'updatePregunta'])->name('formularios.preguntas.update');
    Route::delete('/preguntas/{pregunta}', [FormularioController::class, 'destroyPregunta'])->name('formularios.preguntas.destroy');
    Route::post('/preguntas/{pregunta}/opciones', [FormularioController::class, 'storeOpcion'])->name('formularios.opciones.store');
    Route::delete('/opciones/{opcion}', [FormularioController::class, 'destroyOpcion'])->name('formularios.opciones.destroy');

    // ------------------------------------------------------
    // 🔹 Estructura de formulario
    // ------------------------------------------------------
    Route::post('/formularios/{formulario}/estructura', [EstructuraFormularioController::class, 'guardar'])->name('formularios.estructura.guardar');

    // ------------------------------------------------------
    // 🔹 Usuarios
    // ------------------------------------------------------
    Route::get('/usuarios',[Usuarios::class, 'index'])->name('Usuarios');
    Route::patch('/usuarios/{user}/toggle', [Usuarios::class, 'toggleActivo'])->name('usuarios.toggle');
    Route::post('/usuarios/respondedor/guardar',[Usuarios::class, 'GuardarRespondedor'])->name('usuarios.respondedor.guardar');

    // ------------------------------------------------------
    // 🔹 Configuración y concentrado
    // ------------------------------------------------------
    Route::get('/formularios/{id}/configuracion', [FormularioController::class, 'configuracion'])->name('formularios.configuracion');
    Route::get('/formularios/{id}/concentrado', [FormularioController::class, 'mostrarConcentrado'])->name('formularios.concentrado');
    Route::get('/formularios/{id}/concentrado/export', [FormularioController::class, 'concentrarRespuestas'])->name('formularios.concentrarRespuestas');

    // ------------------------------------------------------
    // 🔹 Otros
    // ------------------------------------------------------
    Route::get('/loginAnonimo', function () {
        return view('formularios.loginAnonimo');
    })->name('loginAnonimo');

    Route::get('/formularios/{formulario}', [Contestar_FormularioController::class, 'mostrar'])->name('mostrar');
});
