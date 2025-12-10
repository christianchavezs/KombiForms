<?php

namespace App\Http\Controllers;

use App\Models\Formulario;
use Illuminate\Http\Request;

class FormularioController extends Controller
{
    // ===============================================
    // LISTAR FORMULARIOS
    // ===============================================
    public function index()
    {
        $hoy = now();

        $formularios = Formulario::withCount('respuestas')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($form) use ($hoy) {
                if (
                    (!$form->fecha_inicio || $form->fecha_inicio <= $hoy) &&
                    (!$form->fecha_fin || $form->fecha_fin >= $hoy)
                ) {
                    $form->estado = 'Activo';
                } elseif ($form->fecha_inicio && $form->fecha_inicio > $hoy) {
                    $form->estado = 'Programado';
                } else {
                    $form->estado = 'Cerrado';
                }
                return $form;
            });

        return view('formularios.index', compact('formularios'));
    }




    // ===============================================
    // CREAR FORMULARIO
    // ===============================================
    public function crear()
    {
        return view('formularios.crear');
    }




    // ===============================================
    // GUARDAR FORMULARIO
    // ===============================================
    public function guardar(Request $request)
    {
        // 🔹 Validación
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        // 🔹 Campos booleanos (si no vienen, Laravel intenta validar boolean y falla)
        $data['permitir_anonimo'] = $request->boolean('permitir_anonimo');
        $data['requiere_correo'] = $request->boolean('requiere_correo');
        $data['una_respuesta'] = $request->boolean('una_respuesta');

        // 🔹 Asignación del creador
        $data['creador_id'] = auth()->id();

        // 🔹 Crear el formulario
        $formulario = Formulario::create($data);

        return redirect()
            ->route('formularios.editar', $formulario->id)
            ->with('success', 'Formulario creado correctamente.');
    }




    // ===============================================
    // EDITAR FORMULARIO (Constructor)
    // ===============================================
    public function editar($id)
    {
        $formulario = Formulario::with(['secciones.preguntas.opciones'])->findOrFail($id);


        return view('formularios.editar', compact('formulario'));
    }




    // ===============================================
    // ACTUALIZAR FORMULARIO
    // ===============================================
    public function actualizar(Request $request, $id)
    {
        $formulario = Formulario::findOrFail($id);

        $formulario->update([
            'titulo' => $request->input('titulo', $formulario->titulo),
            'descripcion' => $request->input('descripcion', $formulario->descripcion),
            'permitir_anonimo' => $request->boolean('permitir_anonimo'),
            'requiere_correo' => $request->boolean('requiere_correo'),
            'una_respuesta' => $request->boolean('una_respuesta'),
            'fecha_inicio' => $request->input('fecha_inicio'),
            'fecha_fin' => $request->input('fecha_fin'),
        ]);

        return redirect()->route('formularios.editar', $id)
            ->with('success', 'Cambios guardados correctamente.');
    }




    // ===============================================
    // ELIMINAR FORMULARIO
    // ===============================================
    public function destroy($id)
    {
        $formulario = Formulario::findOrFail($id);
        $formulario->delete();

        return redirect()->route('formularios.index')
            ->with('success', 'Formulario eliminado.');
    }

    /**
 * Guardar la estructura completa del formulario (secciones -> preguntas -> opciones)
 * El front envía un JSON con 'secciones' => [ { titulo, descripcion, orden, preguntas: [...] } ]
 */
public function guardarEstructura(Request $request, $id)
{
    $request->validate([
        'estructura' => 'required|array',
    ]);

    $estructura = $request->input('estructura');

    $formulario = Formulario::findOrFail($id);

    DB::transaction(function () use ($formulario, $estructura) {
        // Opción sencilla: eliminar las secciones actuales y recrear todo (limpio)
        // Si deseas mergear/upsert, podemos mejorar después.
        Seccion::where('formulario_id', $formulario->id)->delete();

        foreach ($estructura as $sIndex => $s) {
            $seccion = Seccion::create([
                'formulario_id' => $formulario->id,
                'titulo' => $s['titulo'] ?? null,
                'descripcion' => $s['descripcion'] ?? null,
                'orden' => $sIndex + 1,
            ]);

            // preguntas
            if (!empty($s['preguntas']) && is_array($s['preguntas'])) {
                foreach ($s['preguntas'] as $pIndex => $p) {
                    $pregunta = Pregunta::create([
                        'seccion_id' => $seccion->id,
                        'tipo' => $p['tipo'] ?? 'texto_corto',
                        'texto' => $p['texto'] ?? '',
                        'obligatorio' => !empty($p['obligatorio']) ? 1 : 0,
                        'orden' => $pIndex + 1,
                        'escala_min' => $p['escala_min'] ?? null,
                        'escala_max' => $p['escala_max'] ?? null,
                    ]);

                    // opciones (para tipos que requieren)
                    if (!empty($p['opciones']) && is_array($p['opciones'])) {
                        foreach ($p['opciones'] as $oIndex => $o) {
                            Opcion::create([
                                'pregunta_id' => $pregunta->id,
                                'texto' => $o['texto'] ?? '',
                                'fila' => $o['fila'] ?? null,
                                'columna' => $o['columna'] ?? null,
                                'orden' => $oIndex + 1,
                            ]);
                        }
                    }
                }
            }
        }
    });

    return response()->json(['ok' => true, 'message' => 'Estructura guardada']);
}

    

}
