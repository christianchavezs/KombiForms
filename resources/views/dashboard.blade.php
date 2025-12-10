@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="p-6">

    {{-- Tarjetas superiores --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

        {{-- Total Formularios --}}
        <div class="bg-white rounded-2xl shadow p-6 border border-gray-100">
            <h2 class="text-sm text-gray-500">Formularios</h2>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalFormularios }}</p>
        </div>

        {{-- Respuestas totales --}}
        <div class="bg-white rounded-2xl shadow p-6 border border-gray-100">
            <h2 class="text-sm text-gray-500">Respuestas Recibidas</h2>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalRespuestas }}</p>
        </div>

        {{-- Preguntas Totales --}}
        <div class="bg-white rounded-2xl shadow p-6 border border-gray-100">
            <h2 class="text-sm text-gray-500">Preguntas Totales</h2>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalPreguntas }}</p>
        </div>

        {{-- Formularios Activos --}}
        <div class="bg-white rounded-2xl shadow p-6 border border-gray-100">
            <h2 class="text-sm text-gray-500">Formularios Activos</h2>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $formulariosActivos }}</p>
        </div>

    </div>

    {{-- Gráfica --}}
    <div class="bg-white rounded-2xl shadow p-6 border border-gray-100 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Respuestas por Formulario</h2>

        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-xl text-gray-400">
            <span>Gráfica próximamente…</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- Últimos formularios --}}
        <div class="bg-white rounded-2xl shadow p-6 border border-gray-100">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Últimos Formularios</h2>

            <ul class="divide-y divide-gray-100">
                @forelse ($ultimosFormularios as $form)
                <li class="py-4 flex items-center justify-between">
                    <div>
                        <p class="text-gray-900 font-medium">{{ $form->titulo }}</p>
                        <p class="text-gray-500 text-sm">
                            Respuestas: {{ $form->respuestas_count }}
                        </p>
                    </div>

                    @php
                        $hoy = now();
                        $activo = (!$form->fecha_inicio || $form->fecha_inicio <= $hoy) &&
                                  (!$form->fecha_fin || $form->fecha_fin >= $hoy);
                    @endphp

                    <span class="px-3 py-1 rounded-full text-xs 
                        {{ $activo ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                        {{ $activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </li>
                @empty
                <p class="text-gray-500 text-sm">No hay formularios recientes.</p>
                @endforelse
            </ul>
        </div>

        {{-- Últimas respuestas --}}
        <div class="bg-white rounded-2xl shadow p-6 border border-gray-100">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Últimas Respuestas</h2>

            <ul class="divide-y divide-gray-100">
                @forelse ($ultimasRespuestas as $respuesta)
                <li class="py-4">
                    <p class="text-gray-800 font-medium">
                        {{ $respuesta->formulario->titulo }}
                    </p>
                    <p class="text-gray-500 text-sm">
                        Recibida: {{ $respuesta->enviado_en->format('d/m/Y H:i') }}
                    </p>
                </li>
                @empty
                <p class="text-gray-500 text-sm">No hay respuestas recientes.</p>
                @endforelse
            </ul>
        </div>

    </div>
</div>
@endsection
