<?php

namespace App\Http\Controllers;

use App\Models\Formulario;
use App\Models\Respuesta;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Totales
        $totalFormularios = Formulario::count();
        $totalRespuestas = Respuesta::count();
        $totalPreguntas = DB::table('preguntas')->count();

        $hoy = now();
        $formulariosActivos = Formulario::where(function($q) use ($hoy){
            $q->whereNull('fecha_inicio')->orWhere('fecha_inicio','<=',$hoy);
        })->where(function($q) use ($hoy){
            $q->whereNull('fecha_fin')->orWhere('fecha_fin','>=',$hoy);
        })->count();

        // Últimos formularios
        $ultimosFormularios = Formulario::withCount('respuestas')
            ->latest()
            ->take(5)
            ->get();

        // Últimas respuestas con relación formulario (Eloquent)
        $ultimasRespuestas = Respuesta::with('formulario')
            ->latest('enviado_en')
            ->take(5)
            ->get();

        // Datos para la gráfica
        $graficaData = $this->graficaDashboard();

        return view('dashboard', compact(
            'totalFormularios',
            'totalRespuestas',
            'totalPreguntas',
            'formulariosActivos',
            'ultimosFormularios',
            'ultimasRespuestas',
            'graficaData'
        ));
    }

    /**
     * Prepara los datos para la gráfica del dashboard
     */
    public function graficaDashboard()
    {
        $formulariosRecientes = Formulario::withCount('respuestas')
            ->latest()
            ->take(6)
            ->get();

        $hoy = now();
        return $formulariosRecientes->map(function($f) use ($hoy) {
            $activo = (!$f->fecha_inicio || $f->fecha_inicio <= $hoy) &&
                      (!$f->fecha_fin || $f->fecha_fin >= $hoy);

            return [
                'titulo'     => $f->titulo,
                'respuestas' => $f->respuestas_count,
                'estatus'    => $activo ? 'Activo' : 'Inactivo'
            ];
        });
    }
}