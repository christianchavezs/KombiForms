@extends('layouts.app')

@section('title', 'Configurar Formulario')

@section('content')
<div class="max-w-4xl mx-auto mt-20 text-[1.05rem]">

    {{-- Botón Regresar dinámico --}}
    <div class="mt-6 mb-6">
        @php
            $from = request('from');
            if ($from === 'editar') {
                $backRoute = route('formularios.editar', $formulario->id);
            } else {
                $backRoute = route('formularios.index');
            }
        @endphp

        <a href="{{ $backRoute }}"
        class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold px-4 py-2 rounded-lg shadow transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 19l-7-7 7-7" />
            </svg>
            Regresar
        </a>
    </div>

    <h1 class="text-4xl font-extrabold text-gray-800 mb-8">Configuración del Formulario</h1>

    <div class="bg-white rounded-2xl shadow-xl p-10 border border-gray-100">

        <!--form action="{{ route('formularios.actualizar', $formulario->id) }}" method="POST"
              x-data="{
    opcion: '{{ $formulario->permitir_anonimo ? 'anonimo' : ($formulario->requiere_correo ? 'correo' : '') }}',
    titulo: '{{ old('titulo', $formulario->titulo) }}',
    activo: {{ (int) $formulario->activo }},
    mostrarModal: false
}"


              @submit.prevent="if(opcion === ''){ alert('Debes seleccionar una configuraci贸n de respuestas'); } else { $el.submit() }"-->

        <form action="{{ route('formularios.actualizar', $formulario->id) }}" method="POST"
            x-data="{
                opcion: '{{ $formulario->permitir_anonimo ? 'anonimo' : ($formulario->requiere_correo ? 'correo' : '') }}',
                titulo: '{{ old('titulo', $formulario->titulo) }}',
                activo: {{ (int) $formulario->activo }},
                mostrarModal: false,
                fechaInicio: '{{ old('fecha_inicio', $formulario->fecha_inicio) }}',
                fechaFin: '{{ old('fecha_fin', $formulario->fecha_fin) }}',
                errorFechas: ''
            }"
            @submit.prevent="
                let inicio = fechaInicio ? new Date(fechaInicio) : null;
                let fin = fechaFin ? new Date(fechaFin) : null;
                let ahora = new Date();

                errorFechas = '';

                if(opcion === ''){
                    alert('Debes seleccionar una configuración de respuestas');
                } else if(fin && inicio && fin <= inicio){
                    errorFechas = 'La fecha de cierre debe ser mayor que la fecha de inicio.';
                } else if(fin && fin <= ahora){
                    errorFechas = 'La fecha de cierre no puede ser pasada.';
                } else {
                    $el.submit();
                }
            "
        >
            @csrf
            @method('PUT')

            {{-- Campo oculto para origen --}}
            <input type="hidden" name="from" value="{{ $from }}">

            {{-- Campo oculto para enviar el estado del toggle --}}

            <input type="hidden" name="activo" :value="activo">

            {{-- Título --}}
            <div class="mb-10">
                <label class="block text-gray-700 font-semibold mb-3 text-3xl">Título del formulario *</label>
                <input type="text" name="titulo" x-model="titulo"
                       value="{{ old('titulo', $formulario->titulo) }}"
                       :class="titulo.length > 0 
                            ? 'w-full rounded-lg border-green-500 shadow-sm text-green-700 focus:border-green-600 focus:ring-green-600 transition text-3xl font-semibold' 
                            : 'w-full rounded-lg border-gray-300 shadow-sm text-gray-700 focus:border-[#025742] focus:ring-[#025742] transition text-3xl'"
                       required>
            </div>

            {{-- Descripción --}}
            <div class="mb-8">
                <label class="block text-gray-700 font-semibold mb-2 text-lg">Descripción</label>
                <textarea name="descripcion" rows="3"
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#025742] focus:ring-[#025742] transition text-lg">{{ old('descripcion', $formulario->descripcion) }}</textarea>
            </div>

            {{-- Configuración principal (selector) --}}
            <h2 class="text-2xl font-semibold text-gray-800 mt-12 mb-5 flex items-center gap-2">
                <i class="bi bi-gear-fill text-[#025742]"></i> Configuración de respuestas
            </h2>

            <select name="config_respuesta" x-model="opcion"
                    :class="opcion === '' 
                        ? 'w-full rounded-lg border-gray-300 shadow-sm text-gray-500 bg-gray-100 focus:border-[#025742] focus:ring-[#025742] transition text-lg' 
                        : 'w-full rounded-lg border-green-500 shadow-sm text-green-700 bg-green-50 focus:border-green-600 focus:ring-green-600 transition text-lg font-semibold'">
                <option value="" disabled>Selecciona una opción...</option>
                <option value="anonimo" {{ $formulario->permitir_anonimo ? 'selected' : '' }}>Permitir respuestas anónimas</option>
                <option value="correo" {{ $formulario->requiere_correo ? 'selected' : '' }}>Requerir correo electrónico</option>
            </select>

            {{-- Restricciones --}}
            <h2 class="text-2xl font-semibold text-gray-800 mt-12 mb-5 flex items-center gap-2">
                <i class="bi bi-check2-square text-[#025742]"></i> Restricciones
            </h2>

            <label class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 hover:border-[#025742] hover:bg-green-50 transition cursor-pointer">
                <input type="checkbox" name="una_respuesta"
                       class="rounded text-[#025742] focus:ring-[#025742] w-6 h-6"
                       {{ $formulario->una_respuesta ? 'checked' : '' }}>
                <span class="text-gray-700 font-medium text-lg">Permitir solo 1 respuesta por persona</span>
            </label>

 {{-- Fechas --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <div>
    <label class="block text-gray-700 font-medium mb-2 text-lg">Fecha de inicio</label>
    <input type="datetime-local" name="fecha_inicio"
           x-model="fechaInicio"
           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#025742] focus:ring-[#025742] transition text-lg">
    <!-- Aquí ya no hay min -->
</div>

    <div>
        <label class="block text-gray-700 font-medium mb-2 text-lg">Fecha de fin</label>
        <input type="datetime-local" name="fecha_fin"
               x-model="fechaFin"
               min="{{ now()->format('Y-m-d\TH:i') }}"
               @change="
                   if($event.target.value){
                       let fecha = new Date($event.target.value);
                       let inicio = fechaInicio ? new Date(fechaInicio) : null;
                       let ahora = new Date();
                       if(fecha > ahora && (!inicio || fecha > inicio)){
                           activo = 1;
                           errorFechas = '';
                       } else {
                           activo = 0;
                           if(inicio && fecha <= inicio){
                               errorFechas = 'La fecha de cierre debe ser mayor que la fecha de inicio.';
                           } else if(fecha <= ahora){
                               errorFechas = 'La fecha de cierre no puede ser pasada.';
                           }
                       }
                   }
               "
               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#025742] focus:ring-[#025742] transition text-lg">
    </div>
</div>

<!-- Mensaje dinámico de error -->
<div x-show="errorFechas" class="mt-2 text-red-600 text-sm" x-text="errorFechas"></div>

         
       {{-- Estado del formulario (toggle deslizable con modal) --}}
<h2 class="text-2xl font-semibold text-gray-800 mt-12 mb-5 flex items-center gap-2">
    <i class="bi bi-toggle-on text-[#025742]"></i> Estado del formulario
</h2>

<div class="flex items-center gap-3">
    <!-- Toggle deslizable -->
    <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox"
               :checked="activo === 1"
               @change="activo = $event.target.checked ? 1 : 0; mostrarModal = true"
               class="sr-only peer">
        <div class="w-16 h-8 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:bg-green-600 transition"></div>
        <div class="absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition peer-checked:translate-x-8"></div>
    </label>
    <span class="ml-3 text-lg font-semibold" x-text="activo === 1 ? 'Activo' : 'Inactivo'"></span>

    <!-- Campo oculto -->
    <input type="hidden" name="activo" :value="activo">
</div>

<!-- Modal de confirmación -->
<div x-show="mostrarModal"
     class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
     x-transition>
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Confirmación</h3>
        <p class="text-gray-700 mb-6">
            Desea <span x-text="activo === 1 ? 'activar' : 'desactivar'"></span> el formulario?
        </p>
        <div class="flex justify-end gap-3">
            <!-- Cancelar revierte el cambio -->
            <button type="button"
                    @click="mostrarModal = false; activo = activo === 1 ? 0 : 1"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded-lg shadow">
                Cancelar
            </button>
            <!-- Confirmar mantiene el valor elegido -->
            <button type="button"
                    @click="mostrarModal = false"
                    class="bg-[#025742] hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg shadow">
                Confirmar
            </button>
        </div>
    </div>
</div>

            {{-- Botones Guardar y Cancelar --}}
            <div class="mt-12 flex gap-4">
                <button type="submit"
                    class="bg-[#025742] hover:bg-green-700 text-white font-semibold px-8 py-4 rounded-xl shadow-lg transition transform hover:scale-105 text-lg">
                    Guardar cambios
                </button>

                <a href="{{ $backRoute }}"
                class="bg-red-500 hover:bg-red-600 text-white font-semibold px-8 py-4 rounded-xl shadow-lg transition transform hover:scale-105 text-lg inline-flex items-center gap-2">
                    <i class="bi bi-x-circle"></i> Cancelar
                </a>
            </div>

        </form>

    </div>
</div>
@endsection