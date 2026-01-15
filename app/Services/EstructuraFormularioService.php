<?php

namespace App\Services;

use App\Models\Formulario;
use App\Models\Seccion;
use App\Models\Pregunta;
use App\Models\Opcion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EstructuraFormularioService
{
    public function guardarEstructura(Formulario $formulario, array $estructura)
    {
        DB::transaction(function () use ($formulario, $estructura) {

            // 1 Obtener las secciones enviadas desde el frontend
            $secciones = $estructura;

            // 2 Eliminar estructura anterior
            $formulario->secciones()->each(function ($seccion) {
                $seccion->preguntas()->each(function ($pregunta) {
                    $pregunta->opciones()->delete();
                });
                $seccion->preguntas()->delete();
            });
            $formulario->secciones()->delete();

            // 3 Guardar nueva estructura
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
                       // 'obligatorio' => $dataPregunta['obligatorio'] ?? 0,
                       'obligatorio' => isset($dataPregunta['obligatorio']) 
                            ? (int) $dataPregunta['obligatorio'] 
                            : 0, 
                       'orden'       => $ordenPregunta + 1,
                        'escala_min'  => $dataPregunta['escala_min'] ?? null,
                        'escala_max'  => $dataPregunta['escala_max'] ?? null,
                    ]);

                    // ===============================
                    // GUARDAR OPCIONES SEGÚN TIPO
                    // ===============================

                    // Tipos simples (opción múltiple, casillas, desplegable)
                    if (in_array($pregunta->tipo, ['opcion_multiple', 'casillas', 'desplegable'])) {
                        foreach ($dataPregunta['opciones'] ?? [] as $opcionData) {
                            Opcion::create([
                                'pregunta_id' => $pregunta->id,
                                'texto'       => !empty($opcionData['texto']) ? $opcionData['texto'] : 'Opción Default',
                                'fila'        => null,
                                'columna'     => null,
                            ]);
                        }
                    }

                    // Tipos de cuadrícula (opciones/casillas)
                    if (in_array($pregunta->tipo, ['cuadricula_opciones', 'cuadricula_casillas'])) {
                        // Guardar filas como encabezados
                        foreach ($dataPregunta['filas'] ?? [] as $filaData) {
                            Opcion::create([
                                'pregunta_id' => $pregunta->id,
                                'texto'       => !empty($filaData['texto']) ? $filaData['texto'] : 'Fila Default',
                                'fila'        => $filaData['fila'] ?? null,
                                'columna'     => null,
                            ]);
                        }

                        // Guardar columnas como encabezados
                        foreach ($dataPregunta['columnas'] ?? [] as $colData) {
                            Opcion::create([
                                'pregunta_id' => $pregunta->id,
                                'texto'       => !empty($colData['texto']) ? $colData['texto'] : 'Columna Default',
                                'fila'        => null,
                                'columna'     => $colData['columna'] ?? null,
                            ]);
                        }

                        //  Ya no guardamos las combinaciones fila/columna (opciones_cuadricula)
                        Log::info('Guardando cuadrícula', [
                            'tipo'        => $pregunta->tipo,
                            'pregunta_id' => $pregunta->id,
                            'filas'       => $dataPregunta['filas'] ?? [],
                            'columnas'    => $dataPregunta['columnas'] ?? [],
                        ]);
                    }
                }
            }
        });
    }
}