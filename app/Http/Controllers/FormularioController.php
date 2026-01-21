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
    public function configuracion($id)
    {
        $formulario = Formulario::findOrFail($id);
        return view('formularios.configuracion', compact('formulario'));
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

   
    // ===============================================
    // ACCEDER A FORMULARIO POR TOKEN (enlace público)
    // ===============================================
    public function acceder($token)
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
    }



    public function mostrarConcentrado($id)
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
}

public function concentrarRespuestas($id)
{
    $formulario = Formulario::with([
        'secciones.preguntas.opciones',
        'respuestas.usuario',
        'respuestas.respuestas_individuales.pregunta',
        'respuestas.respuestas_individuales.opcion'
    ])->findOrFail($id);

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

    // Crear Excel
    $spreadsheet = new Spreadsheet();

    // Hoja 1: Respuestas por persona (una fila = una persona)
    $sheet1 = $spreadsheet->getActiveSheet();
    $sheet1->setTitle('Respuestas');

    // Encabezados fijos
    $sheet1->setCellValue('A1', 'ID');
    $sheet1->setCellValue('B1', 'Usuario');
    $sheet1->setCellValue('C1', 'Correo');
    $sheet1->setCellValue('D1', 'Departamento');
    $sheet1->setCellValue('E1', 'Fecha');

    // Encabezados dinámicos: cada pregunta es una columna
    $col = 'F';
    $preguntasMap = []; // Mapear pregunta_id a columna
    foreach ($formulario->secciones as $seccion) {
        foreach ($seccion->preguntas as $pregunta) {
            $sheet1->setCellValue("{$col}1", $pregunta->texto);
            $preguntasMap[$pregunta->id] = $col;
            $col++;
        }
    }

    // Llenar filas: cada persona = una fila
    $row = 2;
    $contadorAnonimo = 1;
    foreach ($formulario->respuestas as $respuesta) {
        // Usuario o Persona genérica
        if ($formulario->permitir_anonimo) {
            $usuario = 'Persona ' . $contadorAnonimo++;
            $correo = 'N/A';
            $departamento = 'N/A';
        } else {
            $usuario = $respuesta->usuario->name ?? 'Sin nombre';
            $correo = $respuesta->usuario->email ?? 'N/A';
            $departamento = $respuesta->usuario->departamento ?? 'N/A';
        }

        // Datos fijos
        $sheet1->setCellValue("A{$row}", $respuesta->id);
        $sheet1->setCellValue("B{$row}", $usuario);
        $sheet1->setCellValue("C{$row}", $correo);
        $sheet1->setCellValue("D{$row}", $departamento);
        $sheet1->setCellValue("E{$row}", $respuesta->created_at);

        // Respuestas por pregunta
        foreach ($respuesta->respuestas_individuales as $ri) {
            $col = $preguntasMap[$ri->pregunta->id] ?? null;
            if ($col) {
                $valor = $ri->texto ?? $ri->opcion->texto ?? 'Sin respuesta';

                // Si ya hay algo en la celda (casillas múltiples, cuadrículas), concatenar
                $celdaActual = $sheet1->getCell("{$col}{$row}")->getValue();
                if (!empty($celdaActual)) {
                    $valor = $celdaActual . '; ' . $valor;
                }

                $sheet1->setCellValue("{$col}{$row}", $valor);
            }
        }

        $row++;
    }

    // Estilos encabezados hoja 1
    $lastCol = chr(ord('E') + count($preguntasMap));
    $sheet1->getStyle("A1:{$lastCol}1")->applyFromArray([
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'DDDDDD']
        ],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
    ]);
    foreach (range('A', $lastCol) as $col) {
        $sheet1->getColumnDimension($col)->setAutoSize(true);
    }

    // Hoja 2: Estadísticas (igual que ya tienes)
    $sheet2 = $spreadsheet->createSheet();
    $sheet2->setTitle('Estadísticas');
    $sheet2->setCellValue('A1', 'Pregunta');
    $sheet2->setCellValue('B1', 'Opción');
    $sheet2->setCellValue('C1', 'Conteo');

    $row = 2;
    foreach ($formulario->secciones as $seccion) {
        foreach ($seccion->preguntas as $pregunta) {
            foreach ($estadisticas[$pregunta->id] as $dato) {
                $sheet2->setCellValue("A{$row}", $pregunta->texto);
                $sheet2->setCellValue("B{$row}", $dato['opcion']);
                $sheet2->setCellValue("C{$row}", $dato['conteo']);
                $row++;
            }
        }
    }

    // Estilos encabezados hoja 2
    $sheet2->getStyle('A1:C1')->applyFromArray([
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'DDDDDD']
        ],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
    ]);
    foreach (range('A', 'C') as $col) {
        $sheet2->getColumnDimension($col)->setAutoSize(true);
    }

    // Descargar Excel
    $writer = new Xlsx($spreadsheet);
    $filename = 'concentrado_respuestas.xlsx';

    return response()->streamDownload(function() use ($writer) {
        $writer->save('php://output');
    }, $filename);
}






}
