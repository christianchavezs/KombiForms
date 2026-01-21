@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">
        Concentrado de Respuestas - {{ $formulario->titulo }}
    </h1>

    {{-- Botón para descargar Excel --}}
    <a href="{{ route('formularios.concentrarRespuestas', $formulario->id) }}"
       class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition-all duration-200 transform hover:scale-105">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 4v16h16V4H4zm8 4v8m0 0l-4-4m4 4l4-4"/>
        </svg>
        Descargar Excel
    </a>

    {{-- Concentrado de preguntas y respuestas --}}
    @foreach ($formulario->secciones as $seccion)
        <div class="mt-6">
            <h2 class="text-xl font-semibold">{{ $seccion->titulo }}</h2>

            @foreach ($seccion->preguntas as $pregunta)
                <div class="mt-4">
                    <p class="font-medium">{{ $pregunta->texto }}</p>
                    <ul class="list-disc ml-6">
                        @foreach ($estadisticas[$pregunta->id] as $dato)
                            <li>{{ $dato['opcion'] }}: {{ $dato['conteo'] }} respuestas</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    @endforeach

    <hr class="my-8">

    <h2 class="text-xl font-bold mb-4">Respuestas individuales</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-2 py-1">ID</th>
                    <th class="border px-2 py-1">Usuario</th>
                    <th class="border px-2 py-1">Correo</th>
                    <th class="border px-2 py-1">Departamento</th>
                    <th class="border px-2 py-1">Fecha</th>
                    {{-- Encabezados dinámicos: cada pregunta --}}
                    @foreach ($formulario->secciones as $seccion)
                        @foreach ($seccion->preguntas as $pregunta)
                            <th class="border px-2 py-1">{{ $pregunta->texto }}</th>
                        @endforeach
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php $contadorAnonimo = 1; @endphp
                @foreach ($formulario->respuestas as $respuesta)
                    <tr>
                        <td class="border px-2 py-1">{{ $respuesta->id }}</td>
                        @if ($formulario->permitir_anonimo)
                            <td class="border px-2 py-1">Persona {{ $contadorAnonimo++ }}</td>
                            <td class="border px-2 py-1">N/A</td>
                            <td class="border px-2 py-1">N/A</td>
                        @else
                            <td class="border px-2 py-1">{{ $respuesta->usuario->name ?? 'Sin nombre' }}</td>
                            <td class="border px-2 py-1">{{ $respuesta->usuario->email ?? 'N/A' }}</td>
                            <td class="border px-2 py-1">{{ $respuesta->usuario->departamento ?? 'N/A' }}</td>
                        @endif
                        <td class="border px-2 py-1">{{ $respuesta->created_at }}</td>

                        {{-- Respuestas dinámicas por pregunta --}}
                        @foreach ($formulario->secciones as $seccion)
                            @foreach ($seccion->preguntas as $pregunta)
                                @php
                                    // Buscar respuesta individual de esta persona para esta pregunta
                                    $ri = $respuesta->respuestas_individuales
                                        ->where('pregunta_id', $pregunta->id);
                                    $valor = $ri->map(function($r){
                                        return $r->texto ?? $r->opcion->texto ?? 'Sin respuesta';
                                    })->implode('; ');
                                @endphp
                                <td class="border px-2 py-1">{{ $valor }}</td>
                            @endforeach
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">
        Concentrado de Respuestas - {{ $formulario->titulo }}
    </h1>

    {{-- Botón para descargar Excel --}}
    <a href="{{ route('formularios.concentrarRespuestas', $formulario->id) }}"
       class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition-all duration-200 transform hover:scale-105">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 4v16h16V4H4zm8 4v8m0 0l-4-4m4 4l4-4"/>
        </svg>
        Descargar Excel
    </a>

    {{-- Concentrado de preguntas y respuestas --}}
    @foreach ($formulario->secciones as $seccion)
        <div class="mt-6">
            <h2 class="text-xl font-semibold">{{ $seccion->titulo }}</h2>

            @foreach ($seccion->preguntas as $pregunta)
                <div class="mt-4">
                    <p class="font-medium">{{ $pregunta->texto }}</p>
                    <ul class="list-disc ml-6">
                        @foreach ($estadisticas[$pregunta->id] as $dato)
                            <li>{{ $dato['opcion'] }}: {{ $dato['conteo'] }} respuestas</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    @endforeach

    <hr class="my-8">

    <h2 class="text-xl font-bold mb-4">Respuestas individuales</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-2 py-1">ID</th>
                    <th class="border px-2 py-1">Usuario</th>
                    <th class="border px-2 py-1">Correo</th>
                    <th class="border px-2 py-1">Departamento</th>
                    <th class="border px-2 py-1">Fecha</th>
                    {{-- Encabezados dinámicos: cada pregunta --}}
                    @foreach ($formulario->secciones as $seccion)
                        @foreach ($seccion->preguntas as $pregunta)
                            <th class="border px-2 py-1">{{ $pregunta->texto }}</th>
                        @endforeach
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php $contadorAnonimo = 1; @endphp
                @foreach ($formulario->respuestas as $respuesta)
                    <tr>
                        <td class="border px-2 py-1">{{ $respuesta->id }}</td>
                        @if ($formulario->permitir_anonimo)
                            <td class="border px-2 py-1">Persona {{ $contadorAnonimo++ }}</td>
                            <td class="border px-2 py-1">N/A</td>
                            <td class="border px-2 py-1">N/A</td>
                        @else
                            <td class="border px-2 py-1">{{ $respuesta->usuario->name ?? 'Sin nombre' }}</td>
                            <td class="border px-2 py-1">{{ $respuesta->usuario->email ?? 'N/A' }}</td>
                            <td class="border px-2 py-1">{{ $respuesta->usuario->departamento ?? 'N/A' }}</td>
                        @endif
                        <td class="border px-2 py-1">{{ $respuesta->created_at }}</td>

                        {{-- Respuestas dinámicas por pregunta --}}
                        @foreach ($formulario->secciones as $seccion)
                            @foreach ($seccion->preguntas as $pregunta)
                                @php
                                    // Buscar respuesta individual de esta persona para esta pregunta
                                    $ri = $respuesta->respuestas_individuales
                                        ->where('pregunta_id', $pregunta->id);
                                    $valor = $ri->map(function($r){
                                        return $r->texto ?? $r->opcion->texto ?? 'Sin respuesta';
                                    })->implode('; ');
                                @endphp
                                <td class="border px-2 py-1">{{ $valor }}</td>
                            @endforeach
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection