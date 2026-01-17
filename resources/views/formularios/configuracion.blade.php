@extends('layouts.app')

@section('title', 'Configurar Formulario')

@section('content')
<div class="max-w-3xl mx-auto">

    <h1 class="text-3xl font-bold text-gray-800 mb-6">Configuración del Formulario</h1>

    <div class="bg-white rounded-2xl shadow p-6 border border-gray-100">

        <form action="{{ route('formularios.actualizar', $formulario->id) }}" method="POST"
              x-data="{ anonimo: {{ $formulario->permitir_anonimo ? 'true' : 'false' }}, 
                        correo: {{ $formulario->requiere_correo ? 'true' : 'false' }},
                        mostrarError: false }"
              @submit.prevent="if(!anonimo && !correo){ mostrarError = true } else { $el.submit() }">
            @csrf
            @method('PUT')

            {{-- Título --}}
            <div class="mb-5">
                <label class="block text-gray-700 font-medium mb-1">Título del formulario *</label>
                <input type="text" name="titulo" value="{{ old('titulo', $formulario->titulo) }}"
                       class="w-full rounded-lg border-gray-300 focus:ring-indigo-500" required>
            </div>

            {{-- Descripción --}}
            <div class="mb-5">
                <label class="block text-gray-700 font-medium mb-1">Descripción</label>
                <textarea name="descripcion" rows="3"
                          class="w-full rounded-lg border-gray-300 focus:ring-indigo-500">{{ old('descripcion', $formulario->descripcion) }}</textarea>
            </div>

            {{-- Configuraciones --}}
            <h2 class="text-xl font-semibold text-gray-800 mt-8 mb-4">Configuraciones</h2>

            <div class="space-y-4">
                {{-- Permitir anónimo --}}
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="permitir_anonimo" x-model="anonimo"
                           @change="if(anonimo) correo = false"
                           class="rounded">
                    <span class="text-gray-700">Permitir respuestas anónimas</span>
                </label>

                {{-- Requiere correo --}}
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="requiere_correo" x-model="correo"
                           @change="if(correo) anonimo = false"
                           class="rounded">
                    <span class="text-gray-700">Requerir correo electrónico</span>
                </label>

                {{-- Una respuesta por persona --}}
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="una_respuesta" class="rounded"
                           {{ $formulario->una_respuesta ? 'checked' : '' }}>
                    <span class="text-gray-700">Permitir solo 1 respuesta por persona</span>
                </label>
            </div>

            {{-- Alerta si no se selecciona ninguna --}}
            <div x-show="mostrarError" class="mt-4 p-3 bg-red-100 text-red-700 rounded-lg">
                Debes seleccionar al menos una opción: anónimo o correo electrónico.
            </div>

            {{-- Fechas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Fecha de inicio</label>
                    <input type="datetime-local" name="fecha_inicio"
                           value="{{ old('fecha_inicio', $formulario->fecha_inicio) }}"
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-1">Fecha de fin</label>
                    <input type="datetime-local" name="fecha_fin"
                           value="{{ old('fecha_fin', $formulario->fecha_fin) }}"
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500">
                </div>
            </div>

            {{-- Botón --}}
            <div class="mt-8">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-xl shadow">
                    Guardar cambios
                </button>
            </div>

        </form>

    </div>

</div>
@endsection