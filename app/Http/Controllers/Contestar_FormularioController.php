<?php

namespace App\Http\Controllers;

use App\Models\Formulario;
use App\Models\Pregunta;
use App\Models\Respuesta;
use App\Models\RespuestaIndividual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class Contestar_FormularioController extends Controller
{
    public function gracias()
    {
        if (Auth::check()) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }
        return view('gracias');
    }



    /*public function mostrar(Formulario $formulario)
    {
        // ===============================
        // VALIDAR ACCESO ANÓNIMO
        // ===============================
        if (!Auth::check()) {

            $formularioPermitido = session('acceso_anonimo_formulario');

            if ($formularioPermitido != $formulario->id) {
                return redirect()->route('loginAnonimo');
            }
        }

        // Cargar relaciones necesarias
        $formulario->load('secciones.preguntas.opciones');

        // ===============================
        // FORMULARIO INACTIVO
        // ===============================
        if (!$formulario->activo) {
            return response()
                ->view('formularios.formularioCerrado', compact('formulario'))
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }

        // ===============================
        // UNA SOLA RESPUESTA POR USUARIO
        // ===============================
        if (
            $formulario->requiere_correo &&
            $formulario->una_respuesta &&
            Auth::check()
        ) {

            $yaContestado = Respuesta::where('formulario_id', $formulario->id)
                ->where('correo_respondedor', Auth::user()->email)
                ->exists();

            if ($yaContestado) {
                return response()
                    ->view('formularios.formularioYaContestado', compact('formulario'))
                    ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
            }
        }

        // ===============================
        // MOSTRAR FORMULARIO
        // ===============================
        return response()
            ->view('Contestar_formulario', compact('formulario'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }*/

    
    public function mostrar(Formulario $formulario)
    {
        $ahora = now();

        // ===============================
        // ACTUALIZAR ESTADO SEGÚN FECHAS
        // ===============================
        if ($formulario->fecha_inicio && $formulario->fecha_inicio <= $ahora &&
            (!$formulario->fecha_fin || $formulario->fecha_fin > $ahora)) {
            $formulario->activo = true;
        } else {
            $formulario->activo = false;
        }
        $formulario->save();

        // ===============================
        // VALIDAR ACCESO ANÓNIMO
        // ===============================
        if (!Auth::check()) {
            $formularioPermitido = session('acceso_anonimo_formulario');

            if ($formularioPermitido != $formulario->id) {
                return redirect()->route('loginAnonimo');
            }
        }

        // Cargar relaciones necesarias
        $formulario->load('secciones.preguntas.opciones');

        // ===============================
        // FORMULARIO INACTIVO
        // ===============================
        if (!$formulario->activo) {
            return response()
                ->view('formularios.formularioCerrado', compact('formulario'))
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }

        // ===============================
        // UNA SOLA RESPUESTA POR USUARIO
        // ===============================
        if (
            $formulario->requiere_correo &&
            $formulario->una_respuesta &&
            Auth::check()
        ) {
            $yaContestado = Respuesta::where('formulario_id', $formulario->id)
                ->where('correo_respondedor', Auth::user()->email)
                ->exists();

            if ($yaContestado) {
                return response()
                    ->view('formularios.formularioYaContestado', compact('formulario'))
                    ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
            }
        }

        // ===============================
        // MOSTRAR FORMULARIO
        // ===============================
        return response()
            ->view('Contestar_formulario', compact('formulario'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }




    public function responder(Request $request, Formulario $formulario)
    {
        DB::transaction(function () use ($request, $formulario) {

            $data = [ 'formulario_id' => $formulario->id ];

            if (!$formulario->permitir_anonimo && Auth::check()) {
                $data['usuario_id'] = Auth::id();
                $data['correo_respondedor'] = Auth::user()->email;
            }

            // Crear respuesta con máxima calificación calculada vía secciones → preguntas
            $respuesta = new Respuesta($data);
            $respuesta->puntaje_total = 0;

            $formulario->load('secciones.preguntas');

            $respuesta->maxima_calificacion = $formulario->secciones
                ->flatMap(fn($s) => $s->preguntas)
                ->filter(function ($pregunta) {
                    return $pregunta->tipo === 'opcion_multiple'
                        || $pregunta->tipo === 'casillas'
                        || (
                            in_array($pregunta->tipo, ['texto_corto','parrafo'])
                            && $pregunta->requiere_evaluador
                        );
                })
                ->sum('ponderacion');

            $respuesta->estado = 'pendiente';
            $respuesta->save();

            // Guardar respuestas individuales
            foreach ($request->input('respuestas', []) as $preguntaId => $valor) {
                $pregunta = Pregunta::find($preguntaId);

                switch ($pregunta->tipo) {
                    case 'texto_corto':
                    case 'parrafo':
                        RespuestaIndividual::create([
                            'respuesta_id'    => $respuesta->id,
                            'pregunta_id'     => $preguntaId,
                            'texto_respuesta' => $valor,
                        ]);
                        break;

                    case 'opcion_multiple':
                        RespuestaIndividual::create([
                            'respuesta_id' => $respuesta->id,
                            'pregunta_id'  => $preguntaId,
                            'opcion_id'    => $valor,
                        ]);
                        break;

                    case 'escala_lineal':
                        RespuestaIndividual::create([
                            'respuesta_id'   => $respuesta->id,
                            'pregunta_id'    => $preguntaId,
                            'valor_numerico' => $valor,
                        ]);
                        break;

                    case 'casillas':
                        foreach ($valor as $opcionId) {
                            RespuestaIndividual::create([
                                'respuesta_id' => $respuesta->id,
                                'pregunta_id'  => $preguntaId,
                                'opcion_id'    => $opcionId,
                            ]);
                        }
                        break;

                    case 'cuadricula_opciones':
                        foreach ($valor as $filaId => $opcionId) {
                            RespuestaIndividual::create([
                                'respuesta_id' => $respuesta->id,
                                'pregunta_id'  => $preguntaId,
                                'fila_id'      => $filaId,
                                'opcion_id'    => $opcionId,
                            ]);
                        }
                        break;

                    case 'cuadricula_casillas':
                        foreach ($valor as $filaId => $columnas) {
                            foreach ($columnas as $opcionId) {
                                RespuestaIndividual::create([
                                    'respuesta_id' => $respuesta->id,
                                    'pregunta_id'  => $preguntaId,
                                    'fila_id'      => $filaId,
                                    'opcion_id'    => $opcionId,
                                ]);
                            }
                        }
                        break;
                }
            }
        });

        session()->forget('acceso_anonimo_formulario');
        
        // 🔹 Redirección limpia a la vista de gracias
        return redirect()->route('gracias')->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma'        => 'no-cache',
            'Expires'       => '0',
        ]);
    }



    public function evaluaciones($id)
    {
        $formulario = Formulario::with('secciones.preguntas.opciones')->findOrFail($id);

        $respuestas = Respuesta::with([
            'usuario',
            'respuestasIndividuales.pregunta',
            'respuestasIndividuales.opcion'
        ])
        ->where('formulario_id', $id)
        ->orderByDesc('enviado_en')
        ->get()
        ->map(function ($respuesta) use ($formulario) {

            // Recalcular máxima calificación
            $respuesta->maxima_calificacion = $formulario->secciones
                ->flatMap(fn($s) => $s->preguntas)
                ->filter(function ($pregunta) {
                    return $pregunta->tipo === 'opcion_multiple'
                        || $pregunta->tipo === 'casillas'
                        || (
                            in_array($pregunta->tipo, ['texto_corto','parrafo'])
                            && $pregunta->requiere_evaluador
                        );
                })
                ->sum('ponderacion');

            // Autoevaluar opción múltiple y casillas
            foreach ($respuesta->respuestasIndividuales as $ri) {
                $pregunta = $ri->pregunta;

                // Opción múltiple
                if ($pregunta->tipo === 'opcion_multiple' && $ri->opcion) {
                    if ($ri->opcion->es_correcta) {
                        $ri->puntaje = $pregunta->ponderacion;
                        $ri->estado = 'correcta';
                    } else {
                        $ri->puntaje = 0.00;
                        $ri->estado = 'incorrecta'; // 🆕 marcar como incorrecta
                    }
                    $ri->save();
                }

                // Casillas
                if ($pregunta->tipo === 'casillas' && $ri->opcion) {
                    if ($ri->opcion->es_correcta) {
                        $totalCorrectas = $pregunta->opciones->where('es_correcta', 1)->count();
                        $ri->puntaje = $totalCorrectas > 0 ? $pregunta->ponderacion / $totalCorrectas : 0;
                        $ri->estado = 'correcta';
                    } else {
                        $ri->puntaje = 0.00;
                        $ri->estado = 'incorrecta'; // 🆕 marcar como incorrecta
                    }
                    $ri->save();
                }

                // Preguntas no evaluables → N/A
                if (!(
                    $pregunta->tipo === 'opcion_multiple'
                    || $pregunta->tipo === 'casillas'
                    || (
                        in_array($pregunta->tipo, ['texto_corto','parrafo'])
                        && $pregunta->requiere_evaluador
                    )
                )) {
                    $ri->puntaje = null;
                    $ri->estado = 'N/A';
                    $ri->save();
                }
            }

            // Recalcular puntaje total
            $respuesta->puntaje_total = $respuesta->respuestasIndividuales
                ->where('estado', '!=', 'N/A')
                ->sum('puntaje');

            // Cambiar estado general automáticamente
            $pendientes = $respuesta->respuestasIndividuales->filter(function ($ri) {
                $pregunta = $ri->pregunta;
                return (
                    $pregunta->tipo === 'opcion_multiple'
                    || $pregunta->tipo === 'casillas'
                    || (
                        in_array($pregunta->tipo, ['texto_corto','parrafo'])
                        && $pregunta->requiere_evaluador
                    )
                ) && $ri->estado === 'pendiente';
            });

            $respuesta->estado = $pendientes->isEmpty() ? 'evaluado' : 'pendiente';
            $respuesta->save();

            return $respuesta;
        });

        return view('formularios.evaluaciones', compact('formulario', 'respuestas'));
    }



    
    // ===============================================
    // VER DETALLE DE EVALUACIÓN
    // ===============================================

    


    public function evaluarRespuesta($id)
    {
        $respuesta = Respuesta::with([
            'usuario',
            'formulario.secciones.preguntas.opciones',
            'respuestasIndividuales.pregunta',
            'respuestasIndividuales.opcion'
        ])->findOrFail($id);

        $formulario = $respuesta->formulario;

        // 🆕 Recalcular máxima calificación cada vez que se abre la vista
        $formulario->load('secciones.preguntas');
        $respuesta->maxima_calificacion = $formulario->secciones
            ->flatMap(fn($s) => $s->preguntas)
            ->filter(function ($pregunta) {
                return $pregunta->tipo === 'opcion_multiple'
                    || $pregunta->tipo === 'casillas'
                    || (
                        in_array($pregunta->tipo, ['texto_corto','parrafo'])
                        && $pregunta->requiere_evaluador
                    );
            })
            ->sum('ponderacion');
        $respuesta->save();

        // Calcular automáticamente puntajes de opción múltiple y casillas
        foreach ($respuesta->respuestasIndividuales as $ri) {
            $pregunta = $ri->pregunta;

            if ($pregunta->tipo === 'opcion_multiple' && $ri->opcion) {
                if ($ri->opcion->es_correcta) {
                    $ri->puntaje = $pregunta->ponderacion;
                    $ri->estado = 'correcta';
                } else {
                    $ri->puntaje = 0.00;
                    $ri->estado = 'incorrecta'; // 🆕 marcar como incorrecta
                }
                $ri->save();
            } elseif ($pregunta->tipo === 'casillas' && $ri->opcion) {
                if ($ri->opcion->es_correcta) {
                    $totalCorrectas = $pregunta->opciones->where('es_correcta', 1)->count();
                    $ri->puntaje = $totalCorrectas > 0 ? $pregunta->ponderacion / $totalCorrectas : 0;
                    $ri->estado = 'correcta';
                } else {
                    $ri->puntaje = 0.00;
                    $ri->estado = 'incorrecta'; // 🆕 marcar como incorrecta
                }
                $ri->save();
            } else {
                // 🆕 Si la pregunta no es evaluable → marcar como N/A
                if (!(
                    $pregunta->tipo === 'opcion_multiple'
                    || $pregunta->tipo === 'casillas'
                    || (
                        in_array($pregunta->tipo, ['texto_corto','parrafo'])
                        && $pregunta->requiere_evaluador
                    )
                )) {
                    $ri->puntaje = null;
                    $ri->estado = 'N/A';
                    $ri->save();
                }
            }
        }

        // Recalcular puntaje total (solo suma de evaluables)
        $respuesta->puntaje_total = $respuesta->respuestasIndividuales
            ->where('estado', '!=', 'N/A')
            ->sum('puntaje');
        $respuesta->save();

        // ❌ Ya no recalculamos ni sobrescribimos el campo estado aquí
        // Ese cambio solo lo hace guardarEvaluacionManual en tiempo real

        // Ordenar respuestas para la vista
        $respuesta->respuestasIndividuales = $respuesta->respuestasIndividuales
            ->sortBy('pregunta_id')
            ->values();

        return view('formularios.evaluarRespuesta', compact('respuesta', 'formulario'));
    }




    public function guardarEvaluacionManual(Request $request, $id)
    {
        $ri = RespuestaIndividual::findOrFail($id);
        $pregunta = $ri->pregunta;

        // Guardar estado y puntaje de la respuesta individual
        $ri->estado = $request->input('estado');
        $ri->puntaje = $request->input('puntaje');

        // 🆕 Validar automáticamente opción múltiple y casillas
        if ($pregunta) {
            if ($pregunta->tipo === 'opcion_multiple' && $ri->opcion) {
                if ($ri->opcion->es_correcta) {
                    $ri->puntaje = $pregunta->ponderacion;
                    $ri->estado = 'correcta';
                } else {
                    $ri->puntaje = 0.00;
                    $ri->estado = 'incorrecta'; // marcar como incorrecta
                }
            }

            if ($pregunta->tipo === 'casillas' && $ri->opcion) {
                if ($ri->opcion->es_correcta) {
                    $totalCorrectas = $pregunta->opciones->where('es_correcta', 1)->count();
                    $ri->puntaje = $totalCorrectas > 0 ? $pregunta->ponderacion / $totalCorrectas : 0;
                    $ri->estado = 'correcta';
                } else {
                    $ri->puntaje = 0.00;
                    $ri->estado = 'incorrecta'; // marcar como incorrecta
                }
            }
        }

        $ri->save();

        $respuesta = $ri->respuesta;
        $respuesta->load('respuestasIndividuales.pregunta'); // 🔑 asegurar carga fresca

        // Recalcular puntaje total (solo evaluables, excluyendo N/A)
        $respuesta->puntaje_total = $respuesta->respuestasIndividuales
            ->where('estado', '!=', 'N/A')
            ->sum('puntaje');

        // Revisar si quedan pendientes en preguntas evaluables
        $pendientes = $respuesta->respuestasIndividuales->filter(function ($ri) {
            $pregunta = $ri->pregunta;
            if (!$pregunta) return false;

            $esEvaluable = in_array($pregunta->tipo, ['opcion_multiple','casillas'])
                || (in_array($pregunta->tipo, ['texto_corto','parrafo']) && $pregunta->requiere_evaluador);

            return $esEvaluable && $ri->estado === 'pendiente';
        });

        // Cambiar estado general automáticamente en tiempo real
        $respuesta->estado = $pendientes->isEmpty() ? 'evaluado' : 'pendiente';
        $respuesta->save();

        // Devolver datos para actualizar en la vista vía AJAX
        return response()->json([
            'estado' => $ri->estado,
            'puntaje' => number_format($ri->puntaje, 2),
            'puntaje_total' => number_format($respuesta->puntaje_total, 2),
            'maxima_calificacion' => number_format($respuesta->maxima_calificacion, 2),
            'estado_general' => $respuesta->estado, // 🆕 estado general actualizado
        ]);
    }





}