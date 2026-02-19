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

    // ===============================================
    // EDITAR FORMULARIO (Constructor)
    // ===============================================
   public function editar($id)
    {
        $formulario = Formulario::with(['secciones.preguntas.opciones'])->findOrFail($id);

        // Mapear estructura para separar filas, columnas y celdas
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

                // 👇 Asegurar que escala_lineal tenga etiquetas
                if ($pregunta->tipo === 'escala_lineal') {
                    $pregunta->etiqueta_inicial = $pregunta->etiqueta_inicial ?? '';
                    $pregunta->etiqueta_final   = $pregunta->etiqueta_final ?? '';
                }
            });
        });

        return view('formularios.editar', compact('formulario'));
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

            // Inicializar mapa fila-columna
            $conteos = [];
            foreach ($filas as $fila) {
                foreach ($columnas as $columna) {
                    $key = $fila->id . '_' . $columna->id;
                    $conteos[$key] = [
                        'fila' => $fila->texto,
                        'columna' => $columna->texto,
                        'count' => 0
                    ];
                }
            }

            // Contar respuestas
            foreach ($formulario->respuestas as $r) {
                $ri = collect($r->respuestas_individuales ?? []);
                $riFor = $ri->where('pregunta_id', $pregunta->id)->values();

                foreach ($riFor as $index => $it) {
                    if (empty($it->opcion_id)) continue;

                    // Buscar la opción seleccionada
                    $opcionElegida = $pregunta->opciones->firstWhere('id', $it->opcion_id);
                    if (!$opcionElegida || empty($opcionElegida->columna)) continue;

                    // Reconstruir fila por posición
                    if (!isset($filas[$index])) continue;
                    $fila = $filas[$index];

                    $key = $fila->id . '_' . $opcionElegida->id;
                    if (isset($conteos[$key])) {
                        $conteos[$key]['count']++;
                    }
                }
            }

            // Imprimir resultados
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

                $row++; // espacio entre filas
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

        $row++; // Espacio entre preguntas
    }

    $row++; // Espacio entre secciones
}

    // ============================
    // Hoja 2: Respuestas por persona
    // ============================
    $sheet2 = $spreadsheet->createSheet();
    $sheet2->setTitle('Respuestas');

    // Encabezados fijos
    $sheet2->setCellValue('A1', 'ID');
    $sheet2->setCellValue('B1', 'Usuario');
    $sheet2->setCellValue('C1', 'Correo');
    $sheet2->setCellValue('D1', 'Departamento');
    $sheet2->setCellValue('E1', 'Fecha');

    // Encabezados dinámicos
    $col = 'F';
    $preguntasMap = [];
    foreach ($formulario->secciones as $seccion) {
        foreach ($seccion->preguntas as $pregunta) {
            $sheet2->setCellValue("{$col}1", $pregunta->texto);
            $preguntasMap[$pregunta->id] = $col;
            $col++;
        }
    }

    // Llenar filas
    $row = 2;
    $contadorAnonimo = 1;
    foreach ($formulario->respuestas as $respuesta) {
        if ($respuesta->usuario_id === null) {
            $usuario = 'Persona ' . $contadorAnonimo++;
            $correo = 'N/A';
            $departamento = 'N/A';
        } else {
            $usuario = $respuesta->usuario->name ?? 'Sin nombre';
            $correo = $respuesta->usuario->email ?? 'N/A';
            $departamento = $respuesta->usuario->departamento ?? 'N/A';
        }

        $sheet2->setCellValue("A{$row}", $respuesta->id);
        $sheet2->setCellValue("B{$row}", $usuario);
        $sheet2->setCellValue("C{$row}", $correo);
        $sheet2->setCellValue("D{$row}", $departamento);
        //$sheet2->setCellValue("E{$row}", $respuesta->created_at->format('Y-m-d H:i'));
        $fecha = $respuesta->created_at ? $respuesta->created_at->format('Y-m-d H:i') : 'N/A';
        $sheet2->setCellValue("E{$row}", $fecha);

        foreach ($respuesta->respuestas_individuales as $ri) {
            $col = $preguntasMap[$ri->pregunta->id] ?? null;
            if ($col) {
                $valor = $ri->texto ?? $ri->opcion->texto ?? 'Sin respuesta';
                $celdaActual = $sheet2->getCell("{$col}{$row}")->getValue();
                if (!empty($celdaActual)) {
                    $valor = $celdaActual . '; ' . $valor;
                }
                $sheet2->setCellValue("{$col}{$row}", $valor);
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





}