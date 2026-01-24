<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class Usuarios extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('email','like','%'.$request->buscar.'%')
                ->orWhere('name','like','%'.$request->buscar.'%');
            });
        }

        if ($request->estatus === 'Activos') {
            $query->where('activo',1);
        }

        if ($request->estatus === 'Inactivos') {
            $query->where('activo',0);
        }

        $mostrar = $request->input('mostrar',25);

        $usuarios = $query->paginate($mostrar)->withQueryString();

        if ($request->ajax()) {
            return view('profile.partials.tabla_usuario', compact('usuarios'))->render();
        }

        return view('usuarios',compact('usuarios'));
    }


    
    public function toggleActivo(Request $request, User $user)
{
    // Usar el valor que viene del request
    $user->activo = $request->activo;
    $user->save();

    return response()->json([
        'success' => true,
        'activo' => $user->activo
    ]);
}
}
