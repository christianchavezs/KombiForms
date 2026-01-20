<?php

namespace App\Http\Controllers;

use App\Models\Formulario;
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

        // 1️⃣ Respuesta general
        $respuesta = Respuesta::create([
            'formulario_id'      => $formulario->id,
            'usuario_id'         => Auth::id(),
            'correo_respondedor' => $request->correo_respondedor,
        ]);

        // 2️⃣ Respuestas individuales
        foreach ($request->input('respuestas', []) as $preguntaId => $valor) {

            // 🔷 ARRAYS (casillas / cuadrículas)
            if (is_array($valor)) {

                foreach ($valor as $key => $item) {

                    // 🟦 CUADRÍCULA (fila => columnas)
                    if (is_array($item)) {
                        foreach ($item as $columnaId) {
                            RespuestaIndividual::create([
                                'respuesta_id' => $respuesta->id,
                                'pregunta_id'  => $preguntaId,
                                'fila_id'      => $key,
                                'opcion_id'    => $columnaId,
                            ]);
                        }
                    }

                    // 🟩 CHECKBOX NORMAL
                    else {
                        RespuestaIndividual::create([
                            'respuesta_id' => $respuesta->id,
                            'pregunta_id'  => $preguntaId,
                            'opcion_id'    => $item,
                        ]);
                    }
                }
            }

            // 🔶 TEXTO / RADIO / ESCALA LINEAL
            else {
                RespuestaIndividual::create([
                    'respuesta_id'    => $respuesta->id,
                    'pregunta_id'     => $preguntaId,
                    'texto_respuesta' => $valor,
                ]);
            }
        }
    });

    return redirect()->route('dashboard')->with('success', 'Formulario enviado correctamente');
}


}
