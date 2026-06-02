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

    /*
    public function responder(Request $request, Formulario $formulario)
    {
        DB::transaction(function () use ($request, $formulario) {

            // Datos del usuaria o persona que responde la encuesta
            $data = [ 'formulario_id' => $formulario->id, ];

            //cuando no es anonimo guarda el id del usuario y el correo 
            if (!$formulario->permitir_anonimo && Auth::check()) {
                $data['usuario_id'] = Auth::id();
                $data['correo_respondedor'] = Auth::user()->email;
            }

            $respuesta =  Respuesta::create($data);
            
            // Respuestas individuales
            foreach ($request->input('respuestas', []) as $preguntaId => $valor) {

                $pregunta = Pregunta::find($preguntaId);

                switch ($pregunta->tipo) {

                    //  TEXTO
                    case 'texto_corto':
                    case 'parrafo':
                        RespuestaIndividual::create([
                            'respuesta_id'    => $respuesta->id,
                            'pregunta_id'     => $preguntaId,
                            'texto_respuesta' => $valor,
                        ]);
                        break;

                    //  OPCIÓN ÚNICA
                    case 'opcion_multiple':
                        RespuestaIndividual::create([
                            'respuesta_id' => $respuesta->id,
                            'pregunta_id'  => $preguntaId,
                            'opcion_id'    => $valor,
                        ]);
                        break;
                    
                    // ESCALA LINEAL
                    case 'escala_lineal':
                        RespuestaIndividual::create([
                            'respuesta_id'   => $respuesta->id,
                            'pregunta_id'    => $preguntaId,
                            'valor_numerico' => $valor, // aquí se guarda directamente el número elegido (ej. 3 de un rango 1–7)
                        ]);
                        break;

                    //  CASILLAS
                    case 'casillas':
                        foreach ($valor as $opcionId) {
                            RespuestaIndividual::create([
                                'respuesta_id' => $respuesta->id,
                                'pregunta_id'  => $preguntaId,
                                'opcion_id'    => $opcionId,
                            ]);
                        }
                        break;

                    //  CUADRÍCULA OPCIÓN ÚNICA
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

                    //  CUADRÍCULA CASILLAS
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
        */



public function responder(Request $request, Formulario $formulario)
{
    DB::transaction(function () use ($request, $formulario) {

        $data = [ 'formulario_id' => $formulario->id ];

        if (!$formulario->permitir_anonimo && Auth::check()) {
            $data['usuario_id'] = Auth::id();
            $data['correo_respondedor'] = Auth::user()->email;
        }

        // 🆕 Crear respuesta con máxima calificación calculada vía secciones → preguntas
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
        $formulario = Formulario::findOrFail($id);

        $respuestas = Respuesta::with([
            'usuario',
            'respuestasIndividuales.pregunta',
            'respuestasIndividuales.opcion'
        ])
        ->where('formulario_id', $id)
        ->orderByDesc('enviado_en')
        ->get()
        ->map(function ($respuesta) {
            $total = $respuesta->respuestasIndividuales->sum('puntaje');
            $estadoGeneral = $respuesta->respuestasIndividuales
                ->contains(fn($r) => $r->estado === 'pendiente')
                ? 'pendiente'
                : 'evaluado';

            $respuesta->total_puntaje = $total;
            $respuesta->estado_general = $estadoGeneral;
            $respuesta->maxima_calificacion = $respuesta->maxima_calificacion; // ya guardado en BD

            return $respuesta;
        });


        return view('formularios.evaluaciones', compact('formulario', 'respuestas'));
    }

    
// ===============================================
// VER DETALLE DE EVALUACIÓN
// ===============================================

public function evaluarRespuesta($id)
{
    $respuesta = \App\Models\Respuesta::with([
        'usuario',
        'formulario',
        'respuestasIndividuales.pregunta',
        'respuestasIndividuales.opcion'
    ])->findOrFail($id);

    // ORDENAR RESPUESTAS POR PREGUNTA (IMPORTANTE PARA LISTADO)
    $respuesta->respuestasIndividuales = $respuesta->respuestasIndividuales
        ->sortBy('pregunta_id')
        ->values();

    $formulario = $respuesta->formulario;

    return view('formularios.evaluarRespuesta', compact(
        'respuesta',
        'formulario'
    ));
}

/*
public function guardarEvaluacionManual(Request $request, $id)
{
    $ri = RespuestaIndividual::findOrFail($id);

    $ri->estado = $request->input('estado');
    $ri->puntaje = $request->input('puntaje');
    $ri->save();

    // Recalcular puntaje total
    $respuesta = $ri->respuesta;
    $respuesta->puntaje_total = $respuesta->respuestasIndividuales->sum('puntaje');
    $respuesta->estado = $respuesta->respuestasIndividuales->contains(fn($r) => $r->estado === 'pendiente')
        ? 'pendiente'
        : 'evaluado';
    $respuesta->save();

    return response()->json([
        'estado' => $ri->estado,
        'puntaje' => $ri->puntaje,
        'puntaje_total' => $respuesta->puntaje_total
    ]);
}
    */

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