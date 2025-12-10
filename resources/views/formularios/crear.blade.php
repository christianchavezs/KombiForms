@extends('layouts.app')

@section('title', 'Crear Formulario')

@section('content')
<div class="max-w-3xl mx-auto">

    <h1 class="text-3xl font-bold text-gray-800 mb-6">Crear Nuevo Formulario</h1>

    <div class="bg-white rounded-2xl shadow p-6 border border-gray-100">
      
    <p>Usuario logueado: {{ auth()->id() }}</p>


        <form action="{{ route('formularios.guardar') }}" method="POST">
            @csrf

            {{-- Título --}}
            <div class="mb-5">
                <label class="block text-gray-700 font-medium mb-1">Título del formulario *</label>
                <input type="text" name="titulo" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500" required>
            </div>

            {{-- Descripción --}}
            <div class="mb-5">
                <label class="block text-gray-700 font-medium mb-1">Descripción</label>
                <textarea name="descripcion" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500"></textarea>
            </div>

            {{-- Configuraciones --}}
            <h2 class="text-xl font-semibold text-gray-800 mt-8 mb-4">Configuraciones</h2>

            <div class="space-y-4">

                {{-- Permitir anónimo --}}
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="permitir_anonimo" class="rounded">
                    <span class="text-gray-700">Permitir respuestas anónimas</span>
                </label>

                {{-- Requiere correo --}}
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="requiere_correo" class="rounded">
                    <span class="text-gray-700">Requerir correo electrónico</span>
                </label>

                {{-- Una respuesta por persona --}}
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="una_respuesta" class="rounded">
                    <span class="text-gray-700">Permitir solo 1 respuesta por persona</span>
                </label>

            </div>

            {{-- Fechas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

                <div>
                    <label class="block text-gray-700 font-medium mb-1">Fecha de inicio</label>
                    <input type="datetime-local" name="fecha_inicio"
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-1">Fecha de fin</label>
                    <input type="datetime-local" name="fecha_fin"
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500">
                </div>

            </div>

            {{-- Botón --}}
            <div class="mt-8">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-xl shadow">
                    Guardar formulario
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
