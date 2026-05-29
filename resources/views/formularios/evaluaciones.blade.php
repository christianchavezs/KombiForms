@extends('layouts.app')

@section('content')
<div class="p-6">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Evaluaciones
            <span class="text-gray-500 text-base">
                — {{ $formulario->titulo }}
            </span>
        </h1>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">

        <table class="w-full text-sm">

            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">Usuario</th>
                    <th class="p-3">Correo</th>
                    <th class="p-3">Enviado</th>
                    <th class="p-3">Puntaje</th>
                    <th class="p-3">Estado</th>
                    <th class="p-3">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @foreach($respuestas as $respuesta)
                    <tr class="border-t">

                        <td class="p-3">
                            {{ $respuesta->usuario->name ?? 'Anónimo' }}
                        </td>

                        <td class="p-3">
                            {{ $respuesta->correo_respondedor ?? 'N/A' }}
                        </td>

                        <td class="p-3">
                            {{ $respuesta->enviado_en }}
                        </td>

                        <td class="p-3">
                            {{ $respuesta->total_puntaje }}
                        </td>

                        <td class="p-3">
    <span class="px-2 py-1 rounded text-xs font-bold
        {{ $respuesta->estado === 'evaluado'
            ? 'bg-green-100 text-green-700'
            : 'bg-yellow-100 text-yellow-700' }}">
        
        {{ ucfirst($respuesta->estado) }}
    </span>
</td>

                        <td class="p-3">
                         <a href="{{ route('respuestas.evaluar', $respuesta->id) }}"
   class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-lg shadow-sm hover:bg-indigo-700 hover:shadow-md transition-all duration-200">

    <!-- icono -->
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
    </svg>

    Evaluar
</a>
                        </td>

                    </tr>
                @endforeach
            </tbody>

        </table>

    </div>

</div>
@endsection