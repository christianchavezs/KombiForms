<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Formulario;
use App\Models\Pregunta;
use App\Models\Respuesta;

class DashboardController extends Controller
{
    public function index()
    {
        $totalFormularios = Formulario::count();
        $totalPreguntas = Pregunta::count();
        $totalRespuestas = Respuesta::count();

        $hoy = now();

        $formulariosActivos = Formulario::where(function ($q) use ($hoy) {
                $q->whereNull('fecha_inicio')->orWhere('fecha_inicio', '<=', $hoy);
            })
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $hoy);
            })
            ->count();

        $ultimosFormularios = Formulario::withCount('respuestas')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        $ultimasRespuestas = Respuesta::with('formulario')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalFormularios',
            'totalPreguntas',
            'totalRespuestas',
            'formulariosActivos',
            'ultimosFormularios',
            'ultimasRespuestas'
        ));
    }
}
