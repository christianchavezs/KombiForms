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


    public function mostrar(Formulario $formulario)
    {
        // Cargar relaciones necesarias
        $formulario->load('secciones.preguntas.opciones');

        // Caso: formulario inactivo
        if (!$formulario->activo) {
            return view('formularios.formularioCerrado', compact('formulario'));
        }

        // Caso: requiere correo y es de una sola respuesta
        if ($formulario->requiere_correo && $formulario->una_respuesta && Auth::check()) {
            $yaContestado = Respuesta::where('formulario_id', $formulario->id)
                                    ->where('correo_respondedor', Auth::user()->email)
                                    ->exists();

            if ($yaContestado) {
                return view('formularios.formularioYaContestado', compact('formulario'));
            }
        }

        // Si no aplica la restricción, mostrar el formulario normalmente
        return view('Contestar_formulario', compact('formulario'));
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

        return redirect()->route('gracias');
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

                if ($pregunta->tipo === 'opcion_multiple' && $ri->opcion && $ri->opcion->es_correcta) {
                    $ri->puntaje = $pregunta->ponderacion;
                    $ri->estado = 'correcta';
                    $ri->save();
                }

                if ($pregunta->tipo === 'casillas' && $ri->opcion && $ri->opcion->es_correcta) {
                    $totalCorrectas = $pregunta->opciones->where('es_correcta', 1)->count();
                    $ri->puntaje = $totalCorrectas > 0 ? $pregunta->ponderacion / $totalCorrectas : 0;
                    $ri->estado = 'correcta';
                    $ri->save();
                }
            }

            // Recalcular puntaje total
            $respuesta->puntaje_total = $respuesta->respuestasIndividuales->sum('puntaje');

            // Cambiar estado automáticamente si todas las preguntas evaluables ya están calificadas
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

            if ($pregunta->tipo === 'opcion_multiple' && $ri->opcion && $ri->opcion->es_correcta) {
                $ri->puntaje = $pregunta->ponderacion;
                $ri->estado = 'correcta';
                $ri->save();
            }

            if ($pregunta->tipo === 'casillas' && $ri->opcion && $ri->opcion->es_correcta) {
                // Puntaje proporcional si hay varias correctas
                $totalCorrectas = $pregunta->opciones->where('es_correcta', 1)->count();
                $ri->puntaje = $totalCorrectas > 0 ? $pregunta->ponderacion / $totalCorrectas : 0;
                $ri->estado = 'correcta';
                $ri->save();
            }
        }

        // Recalcular puntaje total
        $respuesta->puntaje_total = $respuesta->respuestasIndividuales->sum('puntaje');

        // 🆕 Cambiar estado automáticamente a "evaluado" si todas las respuestas que deben evaluarse ya están calificadas
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

        // Ordenar respuestas para la vista
        $respuesta->respuestasIndividuales = $respuesta->respuestasIndividuales
            ->sortBy('pregunta_id')
            ->values();

        return view('formularios.evaluarRespuesta', compact('respuesta', 'formulario'));
    }


    public function guardarEvaluacionManual(Request $request, $id)
    {
        $ri = RespuestaIndividual::findOrFail($id);

        $ri->estado = $request->input('estado');
        $ri->puntaje = $request->input('puntaje');
        $ri->save();

        $respuesta = $ri->respuesta;
        $respuesta->puntaje_total = $respuesta->respuestasIndividuales->sum('puntaje');
        $respuesta->estado = $respuesta->respuestasIndividuales->contains(fn($r) => $r->estado === 'pendiente')
            ? 'pendiente'
            : 'evaluado';
        $respuesta->save();

        return response()->json([
            'estado' => $ri->estado,
            'puntaje' => number_format($ri->puntaje, 2),
            'puntaje_total' => number_format($respuesta->puntaje_total, 2),
            'maxima_calificacion' => number_format($respuesta->maxima_calificacion, 2),
        ]);
    }


}