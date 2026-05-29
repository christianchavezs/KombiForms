<?php

namespace App\Http\Controllers;

use App\Models\Formulario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Seccion;
use App\Models\Pregunta;
use App\Models\Opcion;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

use App\Services\EstructuraFormularioService;

class FormularioController extends Controller
{
    // ===============================================
    // LISTAR FORMULARIOS
    // ===============================================
  
public function index()
{
    $formularios = Formulario::withCount('respuestas')
        ->orderBy('id', 'desc')
        ->get();

    return view('formularios.index', compact('formularios'));
}
    

    // ===============================================
    // CREAR FORMULARIO
    // ===============================================
    public function crear(Request $request)
    {
        // Si no viene el parámetro, por defecto regresa a la lista de formularios
        $from = $request->query('from', 'index');

        return view('formularios.crear', compact('from'));
    }


    // ===============================================
    // GUARDAR FORMULARIO
    // ===============================================
    /*public function guardar(Request $request)
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
    }*/



    public function guardar(Request $request)
{
        //dd($request->all());
    // 🔹 Validación
    $data = $request->validate([
        'titulo' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'fecha_inicio' => 'nullable|date',
        'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        'config_respuesta' => 'required|in:anonimo,correo',
        // quitamos la validación de activo
    ]);

    // 🔹 Mapear la opción seleccionada a los booleanos
    $data['permitir_anonimo'] = $request->config_respuesta === 'anonimo';
    $data['requiere_correo'] = $request->config_respuesta === 'correo';

    // 🔹 Checkbox de restricción
    $data['una_respuesta'] = $request->boolean('una_respuesta');

    // 🔹 Estado del formulario (toggle)
    $data['activo'] = $request->boolean('activo'); // convierte "true"/"false" en 1/0

    // 🔹 Asignación del creador
    $data['creador_id'] = auth()->id();

    // 🔹 Crear el formulario
    $formulario = Formulario::create($data);

    return redirect()
        ->route('formularios.editar', $formulario->id)
        ->with('success', 'Formulario creado correctamente.');
}

/*
// ===============================================
// EDITAR FORMULARIO (Constructor)
// ===============================================
public function editar($id)
{
    $formulario = Formulario::with(['secciones.preguntas.opciones'])->findOrFail($id);

    $formulario->secciones->each(function ($seccion) {
        $seccion->preguntas->each(function ($pregunta) {

            if (in_array($pregunta->tipo, ['cuadricula_opciones', 'cuadricula_casillas'])) {
                $pregunta->filas = $pregunta->opciones
                    ->whereNotNull('fila')
                    ->whereNull('columna')
                    ->map(fn($o) => ['texto' => $o->texto, 'fila' => $o->fila])
                    ->values();

                $pregunta->columnas = $pregunta->opciones
                    ->whereNotNull('columna')
                    ->whereNull('fila')
                    ->map(fn($o) => ['texto' => $o->texto, 'columna' => $o->columna])
                    ->values();

                $pregunta->opciones_cuadricula = $pregunta->opciones
                    ->whereNotNull('fila')
                    ->whereNotNull('columna')
                    ->map(fn($o) => [
                        'texto' => $o->texto,
                        'fila' => $o->fila,
                        'columna' => $o->columna
                    ])
                    ->values();
            }

            if ($pregunta->tipo === 'escala_lineal') {
                $pregunta->etiqueta_inicial = $pregunta->etiqueta_inicial ?? '';
                $pregunta->etiqueta_final   = $pregunta->etiqueta_final ?? '';
            }
        });
    });

    // 🔥 IMPORTANTE: NO usar toArray()
    return view('formularios.editar', [
        'formulario' => $formulario,
        'dataSecciones' => $formulario->secciones, // 👈 mantener colección Eloquent
    ]);
}*/


// ===============================================
// EDITAR FORMULARIO (Constructor)
// ===============================================
public function editar($id)
{
    $formulario = Formulario::with(['secciones.preguntas.opciones'])
        ->findOrFail($id);

    $formulario->secciones->each(function ($seccion) {

        $seccion->preguntas->each(function ($pregunta) {

            // ==================================================
            // NORMALIZAR BOOLEANOS / FLAGS
            // ==================================================
            $pregunta->obligatorio = (int) $pregunta->obligatorio;
            $pregunta->requiere_evaluador = (int) $pregunta->requiere_evaluador;

            // ==================================================
            // CUADRÍCULAS
            // ==================================================
            if (in_array($pregunta->tipo, [
                'cuadricula_opciones',
                'cuadricula_casillas'
            ])) {

                // FILAS
                $pregunta->filas = $pregunta->opciones
                    ->whereNotNull('fila')
                    ->whereNull('columna')
                    ->map(function ($o) {
                        return [
                            'id' => $o->id,
                            'texto' => $o->texto,
                            'fila' => $o->fila,
                        ];
                    })
                    ->values();

                // COLUMNAS
                $pregunta->columnas = $pregunta->opciones
                    ->whereNotNull('columna')
                    ->whereNull('fila')
                    ->map(function ($o) {
                        return [
                            'id' => $o->id,
                            'texto' => $o->texto,
                            'columna' => $o->columna,
                        ];
                    })
                    ->values();

                // OPCIONES CUADRÍCULA
                $pregunta->opciones_cuadricula = $pregunta->opciones
                    ->whereNotNull('fila')
                    ->whereNotNull('columna')
                    ->map(function ($o) {
                        return [
                            'id' => $o->id,
                            'texto' => $o->texto,
                            'fila' => $o->fila,
                            'columna' => $o->columna,
                        ];
                    })
                    ->values();
            }

            // ==================================================
            // ESCALA LINEAL
            // ==================================================
            if ($pregunta->tipo === 'escala_lineal') {

                $pregunta->escala_min = $pregunta->escala_min ?? 1;
                $pregunta->escala_max = $pregunta->escala_max ?? 5;

                $pregunta->etiqueta_inicial =
                    $pregunta->etiqueta_inicial ?? '';

                $pregunta->etiqueta_final =
                    $pregunta->etiqueta_final ?? '';
            }
        });
    });

    // ==================================================
    // PREPARAR DATA LIMPIA PARA ALPINE
    // ==================================================
    $dataSecciones = $formulario->secciones->map(function ($seccion) {

        return [
            'id' => $seccion->id,
            'titulo' => $seccion->titulo,
            'descripcion' => $seccion->descripcion,
            'orden' => $seccion->orden,

            'preguntas' => $seccion->preguntas->map(function ($p) {

                return [

                    'id' => $p->id,
                    'tipo' => $p->tipo,
                    'texto' => $p->texto,

                     // 🔥 IMPORTANTE: PONDERACIÓN
                    'ponderacion' => (float) $p->ponderacion,

                    // 🔥 IMPORTANTES
                    'obligatorio' => (int) $p->obligatorio,
                    'requiere_evaluador' => (int) $p->requiere_evaluador,

                    'orden' => $p->orden,

                    // ESCALA
                    'escala_min' => $p->escala_min,
                    'escala_max' => $p->escala_max,
                    'etiqueta_inicial' => $p->etiqueta_inicial,
                    'etiqueta_final' => $p->etiqueta_final,

                    // FILAS
                    'filas' => $p->filas ?? [],

                    // COLUMNAS
                    'columnas' => $p->columnas ?? [],

                    // OPCIONES
                    'opciones' => $p->opciones
                        ->map(function ($o) {

                            return [
                                'id' => $o->id,
                                'texto' => $o->texto,
                                'fila' => $o->fila,
                                'columna' => $o->columna,
                                'es_correcta' => (int) $o->es_correcta,

                            ];
                        })
                        ->values(),

                    // OPCIONES CUADRÍCULA
                    'opciones_cuadricula' =>
                        $p->opciones_cuadricula ?? [],
                ];
            })->values(),
        ];
    })->values();

    return view('formularios.editar', [
        'formulario' => $formulario,
        'dataSecciones' => $dataSecciones,
    ]);
}




    // ===============================================
    // CONFIGURACIÓN DEL FORMULARIO
    // ===============================================
   /* public function configuracion($id)
    {
        $formulario = Formulario::findOrFail($id);
        return view('formularios.configuracion', compact('formulario'));
    }*/

    // ===============================================
    // CONFIGURACIÓN DEL FORMULARIO
    // ===============================================
    /*public function configuracion(Request $request, $id)
    {
        $formulario = Formulario::findOrFail($id);
        $from = $request->query('from', 'index'); // por defecto lista de formularios

        return view('formularios.configuracion', compact('formulario', 'from'));
    }*/

        // ===============================================
        // CONFIGURACIÓN DEL FORMULARIO
        // ===============================================
        public function configuracion(Request $request, $id)
        {
            $formulario = Formulario::findOrFail($id);

            // Validar si la fecha de fin ya pasó
            if ($formulario->fecha_fin && now()->greaterThan($formulario->fecha_fin)) {
                $formulario->activo = 0;              // Apagar el formulario
                $formulario->fecha_inicio = null;     // Limpiar fecha inicio
                $formulario->fecha_fin = null;        // Limpiar fecha fin
                $formulario->save();                  // Guardar cambios en la BD
            }

            $from = $request->query('from', 'index'); // por defecto lista de formularios

            return view('formularios.configuracion', compact('formulario', 'from'));
        }


    // ===============================================
    // ACTUALIZAR FORMULARIO
    // ===============================================
    /*public function actualizar(Request $request, $id)
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
    }*/

/*
   public function actualizar(Request $request, $id)
{
    // Ver todo lo que llega del formulario
    //dd($request->all());

    $formulario = Formulario::findOrFail($id);

    // Convertir el valor del select en booleanos
    $config = $request->input('config_respuesta');
    $permitirAnonimo = $config === 'anonimo';
    $requiereCorreo = $config === 'correo';

    $formulario->update([
        'titulo' => $request->input('titulo', $formulario->titulo),
        'descripcion' => $request->input('descripcion', $formulario->descripcion),
        'permitir_anonimo' => $permitirAnonimo,
        'requiere_correo' => $requiereCorreo,
        'una_respuesta' => $request->boolean('una_respuesta'),
        'fecha_inicio' => $request->input('fecha_inicio'),
        'fecha_fin' => $request->input('fecha_fin'),
        // Guardar como entero 0/1 para alinearse con tinyint(1)
        'activo' => (int) $request->input('activo', 0),
    ]);

    // Redirigir según origen
    $from = $request->input('from');
    if ($from === 'editar') {
        return redirect()->route('formularios.editar', $id)
            ->with('success', 'Cambios guardados correctamente.');
    }

    return redirect()->route('formularios.index')
        ->with('success', 'Cambios guardados correctamente.');
}*/

public function actualizar(Request $request, $id)
{
    $formulario = Formulario::findOrFail($id);

    // ==============================
    // CONFIGURACIÓN DEL FORMULARIO
    // ==============================
    $config = $request->input('config_respuesta');
    $permitirAnonimo = $config === 'anonimo';
    $requiereCorreo  = $config === 'correo';

    $formulario->update([
        'titulo'           => $request->input('titulo', $formulario->titulo),
        'descripcion'      => $request->input('descripcion', $formulario->descripcion),
        'permitir_anonimo' => $permitirAnonimo,
        'requiere_correo'  => $requiereCorreo,
        'una_respuesta'    => $request->boolean('una_respuesta'),
        'fecha_inicio'     => $request->input('fecha_inicio'),
        'fecha_fin'        => $request->input('fecha_fin'),
        'activo'           => $request->boolean('activo'),
    ]);

    // ==============================
    // SECCIONES / PREGUNTAS / OPCIONES
    // ==============================
    foreach ($request->input('secciones', []) as $seccionData) {

        foreach ($seccionData['preguntas'] ?? [] as $preguntaData) {

            if (empty($preguntaData['id'])) {
                continue;
            }

            $pregunta = Pregunta::find($preguntaData['id']);

            if (!$pregunta) {
                \Log::warning('Pregunta no encontrada', [
                    'id' => $preguntaData['id']
                ]);
                continue;
            }

            // ==============================
            // ACTUALIZAR PREGUNTA
            // ==============================
            $pregunta->texto = $preguntaData['texto'] ?? $pregunta->texto;

            // obligatorio (seguro)
            $pregunta->obligatorio = isset($preguntaData['obligatorio'])
                ? (int) filter_var($preguntaData['obligatorio'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? 0
                : $pregunta->obligatorio;

            // requiere evaluador (seguro)
            $pregunta->requiere_evaluador = isset($preguntaData['requiere_evaluador'])
                ? (int) filter_var($preguntaData['requiere_evaluador'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? 0
                : $pregunta->requiere_evaluador;

            // ⭐ PONDERACIÓN (FIX DEFINITIVO)
            $pregunta->ponderacion = isset($preguntaData['ponderacion'])
                ? (float) $preguntaData['ponderacion']
                : $pregunta->ponderacion;

            \Log::info('PREGUNTA ACTUALIZADA:', [
                'id' => $pregunta->id,
                'obligatorio' => $pregunta->obligatorio,
                'requiere_evaluador' => $pregunta->requiere_evaluador,
                'ponderacion' => $pregunta->ponderacion,
            ]);

            $pregunta->save();

            // ==============================
            // OPCIONES
            // ==============================
            foreach ($preguntaData['opciones'] ?? [] as $opcionData) {

                if (empty($opcionData['id'])) {
                    continue;
                }

                $opcion = Opcion::find($opcionData['id']);

                if ($opcion) {
                    $opcion->texto = $opcionData['texto'] ?? $opcion->texto;
                    $opcion->es_correcta = !empty($opcionData['es_correcta']) ? 1 : 0;
                    $opcion->save();
                }
            }
        }
    }

    return redirect()
        ->route('formularios.editar', $id)
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

   
    // ===============================================
    // ACCEDER A FORMULARIO POR TOKEN (enlace público)
    // ===============================================
    /*public function acceder($token)
    {
        $formulario = Formulario::where('token', $token)->firstOrFail();

        // Si el formulario permite respuestas anónimas → vista loginAnonimo
        if ($formulario->permitir_anonimo) {
            return view('formularios.loginAnonimo', compact('formulario'));
        }

        // Guardamos a dónde debe volver después del login
        session(['url.intended' => route('mostrar', $formulario)]);

        // Si requiere usuario registrado → redirigir al login normal
        return redirect()->route('login');
    }

    // ===============================================
    // RESPONDER FORMULARIO (vista de encuesta)
    // ===============================================
    public function responder($id)
    {
        $formulario = Formulario::with(['secciones.preguntas.opciones'])->findOrFail($id);

        return view('formularios.responder', compact('formulario'));
    }*/
    
    public function acceder($token)
    {
        $formulario = Formulario::where('token', $token)->firstOrFail();

        // Si está inactivo → mostrar vista de cerrado
        if (!$formulario->activo) {
            return view('formularios.formularioCerrado', compact('formulario'));
        }

        // Si el formulario permite respuestas anónimas → vista loginAnonimo
        if ($formulario->permitir_anonimo) {
            return view('formularios.loginAnonimo', compact('formulario'));
        }

        // Guardamos a dónde debe volver después del login
        session(['url.intended' => route('mostrar', $formulario)]);

        // Si requiere usuario registrado → redirigir al login normal
        return redirect()->route('login');
    }

  public function responder($id)
{
    $formulario = Formulario::with(['secciones.preguntas.opciones'])->findOrFail($id);

    // Si está inactivo → mostrar vista de cerrado
    if (!$formulario->activo) {
        return view('formularios.formularioCerrado', compact('formulario'));
    }

    // Si requiere correo y es de una sola respuesta
    if ($formulario->requiere_correo && $formulario->una_respuesta) {
        $existe = Respuesta::where('formulario_id', $formulario->id)
                           ->where('email', auth()->user()->email) // correo del usuario logueado
                           ->exists();

        if ($existe) {
            // Mostrar vista de "ya contestado"
            return view('formularios.formularioYaContestado', compact('formulario'));
        }
    }

    return view('formularios.responder', compact('formulario'));
}



    /*public function mostrarConcentrado($id)
    {
        $formulario = Formulario::with(['secciones.preguntas.opciones', 'respuestas'])->findOrFail($id);

        // Contadores por opción
        $estadisticas = [];
        foreach ($formulario->secciones as $seccion) {
            foreach ($seccion->preguntas as $pregunta) {
                $estadisticas[$pregunta->id] = $pregunta->opciones->map(function ($opcion) use ($pregunta) {
                    $conteo = DB::table('respuestas_individuales')
                        ->where('pregunta_id', $pregunta->id)
                        ->where('opcion_id', $opcion->id)
                        ->count();

                    return [
                        'opcion' => $opcion->texto,
                        'conteo' => $conteo,
                    ];
                });
            }
        }

        return view('formularios.concentradoRespuestas', compact('formulario', 'estadisticas'));
}*/

public function mostrarConcentrado($id)
{
    $formulario = Formulario::with([
        'secciones.preguntas.opciones',
        'respuestas.usuario',
        'respuestas.respuestasIndividuales.opcion'
    ])->findOrFail($id);

    $estadisticas = [];

    foreach ($formulario->secciones as $seccion) {
        foreach ($seccion->preguntas as $pregunta) {
            if ($pregunta->opciones->count() > 0) {
                // Preguntas con opciones (opción múltiple, casillas, etc.)
                $estadisticas[$pregunta->id] = $pregunta->opciones->map(function ($opcion) use ($pregunta) {
                    $conteo = DB::table('respuestas_individuales')
                        ->where('pregunta_id', $pregunta->id)
                        ->where('opcion_id', $opcion->id)
                        ->count();

                    return [
                        'opcion' => $opcion->texto,
                        'conteo' => $conteo,
                    ];
                });
            } else {
                // Preguntas abiertas según tipo
                switch ($pregunta->tipo) {
                    case 'texto_corto':
                    case 'parrafo':
                        // Contar respuestas con texto_respuesta no vacío
                        $conteo = DB::table('respuestas_individuales')
                            ->where('pregunta_id', $pregunta->id)
                            ->whereNotNull('texto_respuesta')
                            ->where('texto_respuesta', '!=', '')
                            ->count();

                        $estadisticas[$pregunta->id] = collect([[
                            'opcion' => 'Respuestas abiertas',
                            'conteo' => $conteo,
                        ]]);
                        break;

                    default:
                        // Fallback genérico para otros tipos
                        $conteo = DB::table('respuestas_individuales')
                            ->where('pregunta_id', $pregunta->id)
                            ->count();

                        $estadisticas[$pregunta->id] = collect([[
                            'opcion' => 'Respuestas registradas',
                            'conteo' => $conteo,
                        ]]);
                        break;
                }
            }
        }
    }

    return view('formularios.concentradoRespuestas', compact('formulario', 'estadisticas'));
}

public function concentrarRespuestas($id)
{
    $formulario = Formulario::with([
        'secciones.preguntas.opciones',
        'respuestas.usuario',
        'respuestas.respuestas_individuales.pregunta',
        'respuestas.respuestas_individuales.opcion'
    ])->findOrFail($id);

    $spreadsheet = new Spreadsheet();

// ============================
// Hoja 1: Concentrado por pregunta
// ============================
$sheet1 = $spreadsheet->getActiveSheet();
$sheet1->setTitle('Concentrado');

$row = 1;
foreach ($formulario->secciones as $seccion) {
    // Título de sección
    $sheet1->setCellValue("A{$row}", "Sección: " . $seccion->titulo);
    $sheet1->getStyle("A{$row}")->getFont()->setBold(true);
    $row++;

    foreach ($seccion->preguntas as $pregunta) {
        // Encabezado de pregunta
        $sheet1->setCellValue("A{$row}", "Pregunta: " . $pregunta->texto);
        $sheet1->getStyle("A{$row}")->getFont()->setBold(true);
        $row++;

        $sheet1->setCellValue("A{$row}", "Tipo: " . $pregunta->tipo);
        $row++;

        // Total de respuestas
        $totalRespuestas = $formulario->respuestas
            ->flatMap->respuestas_individuales
            ->where('pregunta_id', $pregunta->id)
            ->count();

        $sheet1->setCellValue("A{$row}", "Total respuestas: {$totalRespuestas}");
        $row++;

        // --- Caso cuadrícula ---
        if (in_array($pregunta->tipo, ['cuadricula_opciones','cuadricula_casillas'])) {
            $filas = $pregunta->filas->sortBy('fila')->values();
            $columnas = $pregunta->columnas->sortBy('columna')->values();

            $conteos = [];
            foreach ($filas as $fila) {
                foreach ($columnas as $columna) {
                    $key = $fila->id . '_' . $columna->id;
                    $conteos[$key] = ['fila' => $fila->texto, 'columna' => $columna->texto, 'count' => 0];
                }
            }

            foreach ($formulario->respuestas as $r) {
                $ri = collect($r->respuestas_individuales ?? []);
                $riFor = $ri->where('pregunta_id', $pregunta->id)->values();

                foreach ($riFor as $index => $it) {
                    if (empty($it->opcion_id)) continue;
                    $opcionElegida = $pregunta->opciones->firstWhere('id', $it->opcion_id);
                    if (!$opcionElegida) continue;
                    if (!isset($filas[$index])) continue;
                    $fila = $filas[$index];
                    $key = $fila->id . '_' . $opcionElegida->id;
                    if (isset($conteos[$key])) {
                        $conteos[$key]['count']++;
                    }
                }
            }

            foreach ($filas as $fila) {
                $sheet1->setCellValue("A{$row}", "Fila: " . $fila->texto);
                $sheet1->getStyle("A{$row}")->getFont()->setBold(true);
                $row++;

                $totalFila = 0;
                foreach ($columnas as $columna) {
                    $key = $fila->id . '_' . $columna->id;
                    $totalFila += $conteos[$key]['count'];
                }

                foreach ($columnas as $columna) {
                    $key = $fila->id . '_' . $columna->id;
                    $c = $conteos[$key];
                    $pct = $totalFila > 0 ? round(($c['count'] / $totalFila) * 100, 1) : 0;

                    $sheet1->setCellValue("A{$row}", "Columna: " . $columna->texto);
                    $sheet1->setCellValue("B{$row}", "{$c['count']} respuestas");
                    $sheet1->setCellValue("C{$row}", "{$pct}%");
                    $row++;
                }

                $row++;
            }
        }
        // --- Caso escala lineal ---
        elseif ($pregunta->tipo === 'escala_lineal') {
            $min = $pregunta->escala_min;
            $max = $pregunta->escala_max;

            for ($i = $min; $i <= $max; $i++) {
                // Contar respuestas usando valor_numerico
                $conteo = $formulario->respuestas
                    ->flatMap->respuestas_individuales
                    ->where('pregunta_id', $pregunta->id)
                    ->where('valor_numerico', $i)
                    ->count();

                $porcentaje = $totalRespuestas > 0
                    ? round(($conteo / $totalRespuestas) * 100, 1)
                    : 0;

                // Mostrar etiquetas inicial/final si existen
                $etiqueta = '';
                if ($i == $min && !empty($pregunta->etiqueta_inicial)) {
                    $etiqueta = " ({$pregunta->etiqueta_inicial})";
                }
                if ($i == $max && !empty($pregunta->etiqueta_final)) {
                    $etiqueta = " ({$pregunta->etiqueta_final})";
                }

                $sheet1->setCellValue("A{$row}", "{$i}{$etiqueta}");
                $sheet1->setCellValue("B{$row}", "{$conteo} respuestas");
                $sheet1->setCellValue("C{$row}", "{$porcentaje}%");
                $row++;
            }
        }
        // --- Caso opciones simples ---
        elseif ($pregunta->opciones->count() > 0) {
            foreach ($pregunta->opciones as $opcion) {
                $conteo = $formulario->respuestas
                    ->flatMap->respuestas_individuales
                    ->where('pregunta_id', $pregunta->id)
                    ->where('opcion_id', $opcion->id)
                    ->count();

                $porcentaje = $totalRespuestas > 0
                    ? round(($conteo / $totalRespuestas) * 100, 1)
                    : 0;

                $sheet1->setCellValue("A{$row}", $opcion->texto);
                $sheet1->setCellValue("B{$row}", "{$conteo} respuestas");
                $sheet1->setCellValue("C{$row}", "{$porcentaje}%");
                $row++;
            }
        }
        // --- Caso preguntas abiertas ---
        else {
            $respuestasTexto = $formulario->respuestas
                ->flatMap->respuestas_individuales
                ->where('pregunta_id', $pregunta->id);

            foreach ($respuestasTexto as $ri) {
                $sheet1->setCellValue("A{$row}", $ri->texto ?? 'Sin respuesta');
                $row++;
            }
        }

        $row++;
    }

    $row++;
}

 // ============================
// Hoja 2: Respuestas por persona
// ============================
$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('Respuestas');

// Encabezados fijos (sin Departamento)
$sheet2->setCellValue('A1', 'ID');
$sheet2->setCellValue('B1', 'Usuario');
$sheet2->setCellValue('C1', 'Correo');
$sheet2->setCellValue('D1', 'Fecha');

// Encabezados dinámicos: cada pregunta es una columna
$col = 'E';
$preguntasMap = [];
foreach ($formulario->secciones as $seccion) {
    foreach ($seccion->preguntas as $pregunta) {
        $sheet2->setCellValue("{$col}1", $pregunta->texto);
        $preguntasMap[$pregunta->id] = $col;
        $col++;
    }
}

// Llenar filas: cada persona = una fila
$row = 2;
$contadorAnonimo = 1;
foreach ($formulario->respuestas as $respuesta) {
    if ($respuesta->usuario_id === null) {
        $usuario = 'Persona ' . $contadorAnonimo++;
        $correo = 'N/A';
    } else {
        $usuario = $respuesta->usuario->name ?? 'Sin nombre';
        $correo = $respuesta->usuario->email ?? 'N/A';
    }

    $sheet2->setCellValue("A{$row}", $respuesta->id);
    $sheet2->setCellValue("B{$row}", $usuario);
    $sheet2->setCellValue("C{$row}", $correo);

    // Usar la fecha de la columna enviado_en
    $fecha = $respuesta->enviado_en ? \Carbon\Carbon::parse($respuesta->enviado_en)->format('d/m/Y H:i') : 'N/A';
    $sheet2->setCellValue("D{$row}", $fecha);

    // Recorremos todas las preguntas para mantener estructura compacta
    foreach ($formulario->secciones as $seccion) {
        foreach ($seccion->preguntas as $pregunta) {
            $col = $preguntasMap[$pregunta->id] ?? null;
            if ($col) {
                $riFor = collect($respuesta->respuestas_individuales ?? [])
                    ->where('pregunta_id', $pregunta->id);

                $vals = [];
                foreach ($riFor as $it) {
                    // Pregunta abierta o casillas con texto
                    if (!empty($it->texto_respuesta)) {
                        $vals[] = $it->texto_respuesta;
                        continue;
                    }
                    // Escala lineal
                    if (!empty($it->valor_numerico)) {
                        $vals[] = $it->valor_numerico;
                        continue;
                    }
                    // Opción seleccionada
                    if (!empty($it->opcion) && !empty($it->opcion->texto)) {
                        $vals[] = $it->opcion->texto;
                        continue;
                    }
                    // Fallback: opcion_id
                    if (!empty($it->opcion_id)) {
                        $vals[] = 'Opción #' . $it->opcion_id;
                        continue;
                    }
                    // Fechas/horas si aplica
                    if (!empty($it->valor_fecha)) {
                        $vals[] = $it->valor_fecha;
                        continue;
                    }
                    if (!empty($it->valor_hora)) {
                        $vals[] = $it->valor_hora;
                        continue;
                    }
                }

                $display = count($vals) ? implode('; ', $vals) : 'Sin respuesta';
                $sheet2->setCellValue("{$col}{$row}", $display);
            }
        }
    }

    $row++;
}

// Descargar Excel
$writer = new Xlsx($spreadsheet);
$filename = 'concentrado_respuestas.xlsx';

return response()->streamDownload(function() use ($writer) {
    $writer->save('php://output');
}, $filename);
}


public function actualizarModo(Request $request, $id)
{
    $formulario = Formulario::findOrFail($id);
    $formulario->modo = $request->modo;
    $formulario->save();

    return response()->json(['success' => true, 'modo' => $formulario->modo]);
}



}