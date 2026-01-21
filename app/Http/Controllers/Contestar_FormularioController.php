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
    public function mostrar(Formulario $formulario)
    {
        $formulario->load('secciones.preguntas.opciones');

        return view('Contestar_formulario', compact('formulario'));
    }

    public function responder(Request $request, Formulario $formulario)
    {
        DB::transaction(function () use ($request, $formulario) {

            // Datos del usuaria o persona que responde la encuesta
            $data = [ 'formulario_id' => $formulario->id, ];

            //cuando no es anonimo guarda el id del usuario y el correo 
            if ($formulario->permitir_anonimo === false) {
                $data['usuario_id'] = Auth::id();
                $data['correo_respondedor'] = $request->correo_respondedor;
            }

            $respuesta =  Respuesta::create($data);
            
            // Respuestas individuales
            foreach ($request->input('respuestas', []) as $preguntaId => $valor) {

                $pregunta = Pregunta::find($preguntaId);

                switch ($pregunta->tipo) {

                    // 📝 TEXTO
                    case 'texto_corto':
                    case 'parrafo':
                        RespuestaIndividual::create([
                            'respuesta_id'    => $respuesta->id,
                            'pregunta_id'     => $preguntaId,
                            'texto_respuesta' => $valor,
                        ]);
                        break;

                    // 🔘 OPCIÓN ÚNICA / ESCALA
                    case 'opcion_multiple':
                    case 'escala_lineal':
                        RespuestaIndividual::create([
                            'respuesta_id' => $respuesta->id,
                            'pregunta_id'  => $preguntaId,
                            'opcion_id'    => $valor,
                        ]);
                        break;

                    // ☑️ CASILLAS
                    case 'casillas':
                        foreach ($valor as $opcionId) {
                            RespuestaIndividual::create([
                                'respuesta_id' => $respuesta->id,
                                'pregunta_id'  => $preguntaId,
                                'opcion_id'    => $opcionId,
                            ]);
                        }
                        break;

                    // 📊 CUADRÍCULA OPCIÓN ÚNICA
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

                    // 📋 CUADRÍCULA CASILLAS
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

        return redirect()->route('dashboard')->with('success', 'Formulario enviado correctamente');
    }


}
