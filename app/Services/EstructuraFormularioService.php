<?php

namespace App\Services;

use App\Models\Formulario;
use App\Models\Seccion;
use App\Models\Pregunta;
use App\Models\Opcion;
use Illuminate\Support\Facades\DB;

class EstructuraFormularioService
{
    public function guardarEstructura(Formulario $formulario, array $estructura)
    {
        DB::transaction(function () use ($formulario, $estructura) {

            // 1️⃣ Obtener las secciones enviadas desde el frontend
            $secciones = $estructura; 

            // 2️⃣ Eliminar estructura anterior
            $formulario->secciones()->each(function ($seccion) {
                $seccion->preguntas()->each(function ($pregunta) {
                    $pregunta->opciones()->delete();
                });
                $seccion->preguntas()->delete();
            });
            $formulario->secciones()->delete();

            // 3️⃣ Guardar nueva estructura
            foreach ($secciones as $ordenSeccion => $dataSeccion) {

                $seccion = Seccion::create([
                    'formulario_id' => $formulario->id,
                    'titulo'        => $dataSeccion['titulo'] ?? null,
                    'descripcion'   => $dataSeccion['descripcion'] ?? null,
                    'orden'         => $ordenSeccion + 1,
                ]);

                foreach ($dataSeccion['preguntas'] ?? [] as $ordenPregunta => $dataPregunta) {

                    // Guardamos la pregunta
                    $pregunta = Pregunta::create([
                        'seccion_id'  => $seccion->id,
                        'tipo'        => $dataPregunta['tipo'] ?? null,
                        'texto'       => $dataPregunta['texto'] ?? null,
                        'obligatorio' => $dataPregunta['obligatorio'] ?? 0,
                        'orden'       => $ordenPregunta + 1,
                        'escala_min'  => $dataPregunta['escala_min'] ?? null,
                        'escala_max'  => $dataPregunta['escala_max'] ?? null,
                    ]);

                    // Guardar opciones según tipo
                    foreach ($dataPregunta['opciones'] ?? [] as $opcionData) {

                        // Para cuadricula, usamos fila y columna
                        $fila = $opcionData['fila'] ?? null;
                        $columna = $opcionData['columna'] ?? null;

                        Opcion::create([
                            'pregunta_id' => $pregunta->id,
                            'texto'       => $opcionData['texto'] ?? null,
                            'orden'       => $opcionData['orden'] ?? null,
                            'fila'        => $fila,
                            'columna'     => $columna,
                        ]);
                    }
                }
            }
        });
    }
}
