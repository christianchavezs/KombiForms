@extends('layouts.app')

@section('title', 'Crear Formulario')

@section('content')
<div class="max-w-4xl mx-auto mt-20 text-[1.05rem]">

    {{-- Botón Regresar dinámico --}}
    <div class="mt-6 mb-6">
        @php
            if ($from === 'dashboard') {
                $backRoute = route('dashboard');
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

    <!-- Encabezado -->
    <div class="flex items-center gap-3 mb-10">
        <div class="w-14 h-14 flex items-center justify-center bg-[#025742] text-white rounded-xl shadow">
            <i class="bi bi-ui-checks-grid text-3xl"></i>
        </div>
        <h1 class="text-4xl font-extrabold text-gray-800">Crear Nuevo Formulario</h1>
    </div>

    <!-- Card principal -->
    <div class="bg-white rounded-2xl shadow-xl p-10 border border-gray-100">

        <form action="{{ route('formularios.guardar') }}" method="POST"
              x-data="{ opcion: '', titulo: '', activo: true, mostrarModal: false }"
              @submit.prevent="if(opcion === ''){ alert('Debes seleccionar una configuración de respuestas'); } else { $el.submit() }">
            @csrf

            {{-- Título del formulario --}}
            <div class="mb-10">
                <label class="block text-gray-700 font-semibold mb-3 text-3xl">Título del formulario *</label>
                <input type="text" name="titulo" x-model="titulo"
                       :class="titulo.length > 0 
                            ? 'w-full rounded-lg border-green-500 shadow-sm text-green-700 focus:border-green-600 focus:ring-green-600 transition text-3xl font-semibold' 
                            : 'w-full rounded-lg border-gray-300 shadow-sm text-gray-700 focus:border-[#025742] focus:ring-[#025742] transition text-3xl'"
                       placeholder="Ej. Encuesta de satisfacción" required>
            </div>

            {{-- Descripción --}}
            <div class="mb-8">
                <label class="block text-gray-700 font-semibold mb-2 text-lg">Descripción</label>
                <textarea name="descripcion" rows="3"
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#025742] focus:ring-[#025742] transition text-lg"
                          placeholder="Agrega una breve descripción del formulario..."></textarea>
            </div>

            {{-- Configuración principal (selector) --}}
            <h2 class="text-2xl font-semibold text-gray-800 mt-12 mb-5 flex items-center gap-2">
                <i class="bi bi-gear-fill text-[#025742]"></i> Configuración de respuestas
            </h2>

            <select name="config_respuesta" x-model="opcion"
                    :class="opcion === '' 
                        ? 'w-full rounded-lg border-gray-300 shadow-sm text-gray-500 bg-gray-100 focus:border-[#025742] focus:ring-[#025742] transition text-lg' 
                        : 'w-full rounded-lg border-green-500 shadow-sm text-green-700 bg-green-50 focus:border-green-600 focus:ring-green-600 transition text-lg font-semibold'">
                <option value="" disabled selected>Selecciona una opción...</option>
                <option value="anonimo">Permitir respuestas anónimas</option>
                <option value="correo">Requerir correo electrónico</option>
            </select>

            {{-- Restricciones --}}
            <h2 class="text-2xl font-semibold text-gray-800 mt-12 mb-5 flex items-center gap-2">
                <i class="bi bi-check2-square text-[#025742]"></i> Restricciones
            </h2>

            <label class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 hover:border-[#025742] hover:bg-green-50 transition cursor-pointer">
                <input type="checkbox" name="una_respuesta"
                       class="rounded text-[#025742] focus:ring-[#025742] w-6 h-6">
                <span class="text-gray-700 font-medium text-lg">Permitir solo 1 respuesta por persona</span>
            </label>

            {{-- Fechas --}}
            <h2 class="text-2xl font-semibold text-gray-800 mt-12 mb-5 flex items-center gap-2">
                <i class="bi bi-calendar-event text-[#025742]"></i> Fechas
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-gray-700 font-medium mb-2 text-lg">Fecha de inicio</label>
                    <input type="datetime-local" name="fecha_inicio"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#025742] focus:ring-[#025742] transition text-lg">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2 text-lg">Fecha de fin</label>
                    <input type="datetime-local" name="fecha_fin"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#025742] focus:ring-[#025742] transition text-lg">
                </div>
            </div>

            {{-- Estado del formulario (toggle deslizable con modal) --}}
            <h2 class="text-2xl font-semibold text-gray-800 mt-12 mb-5 flex items-center gap-2">
                <i class="bi bi-toggle-on text-[#025742]"></i> Estado del formulario
            </h2>

            <div class="flex items-center gap-3">
                <!-- Toggle deslizable -->
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" x-model="activo" @change="mostrarModal = true" class="sr-only peer">
                    <div class="w-16 h-8 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:bg-green-600 transition"></div>
                    <div class="absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition peer-checked:translate-x-8"></div>
                </label>
                <span class="ml-3 text-lg font-semibold" x-text="activo ? 'Activo' : 'Inactivo'"></span>
            </div>

            <!-- Modal de confirmación -->
            <div x-show="mostrarModal"
                 class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                 x-transition>
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Confirmación</h3>
                    <p class="text-gray-700 mb-6">
                        Desea <span x-text="activo ? 'activar' : 'desactivar'"></span> el formulario?
                    </p>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="mostrarModal = false; activo = !activo"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded-lg shadow">
                            Cancelar
                        </button>
                        <button type="button" @click="mostrarModal = false"
                                class="bg-[#025742] hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg shadow">
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>

            {{-- Botones --}}
            <div class="mt-12 flex items-center gap-6">
                <button type="submit"
                    class="bg-[#025742] hover:bg-green-700 text-white font-semibold px-8 py-4 rounded-xl shadow-lg transition transform hover:scale-105 text-lg">
                    <i class="bi bi-check-circle me-2"></i> Guardar formulario
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