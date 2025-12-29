@extends('layouts.app')

@section('content')
<div
    x-data="{
        ...formBuilder(@json($formulario->secciones ?? []), {{ $formulario->id }}),
        confirmarEliminarSeccion: null,
        menuColapsado: false,
        mostrarModalTipos: false,
        seccionActualModal: null,
        preguntaActualModal: null,

        
        addPreguntaConTipo(tipo) {
            if (this.seccionActualModal === null) return;

            if (this.preguntaActualModal !== null) {
                // 🔁 Cambiar tipo de pregunta existente
                this.changeTipo(
                    this.seccionActualModal,
                    this.preguntaActualModal,
                    tipo
                );
            } else {
                // ➕ Crear nueva pregunta
                this.addPregunta(this.seccionActualModal);
                const i = this.secciones[this.seccionActualModal].preguntas.length - 1;
                this.changeTipo(this.seccionActualModal, i, tipo);
            }

            this.mostrarModalTipos = false;
            this.preguntaActualModal = null;
        },


        abrirModalTipos() {
            if (this.seleccionado.seccion === null) {
                alert('Por favor selecciona una sección primero');
                return;
            }

            this.seccionActualModal = this.seleccionado.seccion;
            this.preguntaActualModal = null; // 👈 nueva pregunta
            this.mostrarModalTipos = true;
        },

        abrirModalCambiarTipo(sIndex, pIndex) {
            this.seccionActualModal = sIndex;
            this.preguntaActualModal = pIndex;
            this.selectPregunta(sIndex, pIndex);
            this.mostrarModalTipos = true;
        },


       
    }"
    class="flex w-full min-h-screen bg-gradient-to-br from-gray-50 to-gray-100"
>

    {{-- ================= PANEL LATERAL MEJORADO ================= --}}
    <aside
        class="bg-white shadow-2xl transition-all duration-300 flex flex-col
               sticky top-0 h-screen overflow-y-auto border-r border-gray-200"
        :class="menuColapsado ? 'w-20' : 'w-72'"
    >

        {{-- HEADER CON GRADIENTE MEJORADO --}}
        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-4 flex justify-between items-center text-white">
            <div x-show="!menuColapsado" class="flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="font-bold text-lg">Constructor</span>
            </div>

            <button @click="menuColapsado = !menuColapsado"
                    class="p-2 hover:bg-white/20 rounded-lg transition-all duration-200 transform hover:scale-110">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        {{-- ACCIONES CON DISEÑO MEJORADO --}}
        <div class="flex-1 p-4 space-y-3">

            {{-- Agregar Sección --}}
            <button @click="addSection()"
                    class="w-full flex items-center gap-3 bg-gradient-to-r from-blue-500 to-blue-600 
                           hover:from-blue-600 hover:to-blue-700 text-white px-4 py-3 rounded-xl
                           shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span x-show="!menuColapsado" class="font-semibold">Agregar sección</span>
            </button>

            {{-- Agregar Pregunta con Modal --}}
            <button
                @click="abrirModalTipos()"
                class="w-full flex items-center gap-3 bg-gradient-to-r from-green-500 to-emerald-600 
                       hover:from-green-600 hover:to-emerald-700 text-white px-4 py-3 rounded-xl
                       shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105
                       disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                :disabled="seleccionado.seccion === null">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span x-show="!menuColapsado" class="font-semibold">Agregar pregunta</span>
            </button>

            {{-- Separador --}}
            <div class="pt-3 border-t border-gray-200">
                <p x-show="!menuColapsado" class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-1">
                    Acciones
                </p>
            </div>

            {{-- Duplicar --}}
            <button @click="duplicatePregunta(seleccionado.seccion, seleccionado.pregunta)"
                    :disabled="seleccionado.pregunta === null"
                    class="w-full flex items-center gap-3 bg-gradient-to-r from-purple-500 to-purple-600 
                           hover:from-purple-600 hover:to-purple-700 text-white px-4 py-3 rounded-xl
                           shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105
                           disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <span x-show="!menuColapsado" class="font-semibold">Duplicar</span>
            </button>

            {{-- Eliminar --}}
            <button @click="removePregunta(seleccionado.seccion, seleccionado.pregunta)"
                    :disabled="seleccionado.pregunta === null"
                    class="w-full flex items-center gap-3 bg-gradient-to-r from-red-500 to-red-600 
                           hover:from-red-600 hover:to-red-700 text-white px-4 py-3 rounded-xl
                           shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105
                           disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                <span x-show="!menuColapsado" class="font-semibold">Eliminar</span>
            </button>
        </div>

        {{-- Botón Guardar Mejorado --}}
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            <button @click="guardar()"
                    class="w-full flex items-center justify-center gap-3 bg-gradient-to-r from-gray-800 to-gray-900 
                           hover:from-gray-900 hover:to-black text-white px-4 py-3 rounded-xl
                           shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
                <span x-show="!menuColapsado" class="font-bold">Guardar Formulario</span>
            </button>
        </div>
    </aside>

    {{-- ================= ÁREA PRINCIPAL ================= --}}
    <main class="flex-1 p-6 space-y-6 overflow-y-auto">
        <template x-for="(seccion, sIndex) in secciones" :key="seccion.id">
            <div
                class="bg-white p-6 shadow-xl rounded-2xl space-y-4 border border-gray-200 hover:shadow-2xl transition-shadow duration-200"
                @click="selectSection(sIndex)"
            >

                {{-- HEADER SECCIÓN --}}
                <div class="flex justify-between items-start gap-4">
                    <div class="flex-1 space-y-2">
                        <input x-model="seccion.titulo"
                               class="text-2xl font-bold border-b-2 border-gray-300 focus:border-indigo-500 w-full p-2 outline-none transition-colors"
                               placeholder="Título de la sección">

                        <textarea x-model="seccion.descripcion"
                                  class="border-2 border-gray-200 focus:border-indigo-500 p-3 w-full resize-none rounded-lg outline-none transition-colors"
                                  rows="2"
                                  placeholder="Descripción de la sección"></textarea>
                    </div>

                    <button
                        @click.stop="confirmarEliminarSeccion = sIndex"
                        class="w-10 h-10 flex items-center justify-center
                               rounded-full bg-red-500 hover:bg-red-600 text-white shadow-lg
                               transition-all duration-200 transform hover:scale-110">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- PREGUNTAS CON BOTÓN PARA CAMBIAR TIPO VÍA MODAL --}}
                <template x-for="(pregunta, pIndex) in seccion.preguntas" :key="pregunta.id">
                    <div
                        class="border-2 p-5 rounded-xl bg-gradient-to-br from-gray-50 to-white transition-all duration-200"
                        :class="{'ring-4 ring-indigo-400 shadow-lg': seleccionado.seccion === sIndex && seleccionado.pregunta === pIndex}"
                        @click.stop="selectPregunta(sIndex, pIndex)"
                    >

                        <input x-model="pregunta.texto"
                               class="border-b-2 border-gray-300 focus:border-indigo-500 w-full font-semibold mb-4 p-2 text-lg outline-none transition-colors"
                               placeholder="Escribe tu pregunta aquí">

                        {{-- Mostrar tipo actual con botón para abrir modal --}}
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex-1 px-4 py-2 bg-indigo-50 border border-indigo-200 rounded-lg">
                                <span class="text-sm text-gray-600">Tipo: </span>
                                <span class="font-semibold text-indigo-700" x-text="tipos.find(t => t.value === pregunta.tipo)?.label"></span>
                            </div>
                           
                            <button 
                                @click.stop="abrirModalCambiarTipo(sIndex, pIndex)"
                                class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 
                                    text-white rounded-lg font-medium shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                                Cambiar tipo
                            </button>

                        </div>

                        {{-- OPCIONES CON BOTÓN MEJORADO --}}
                        <template x-if="isChoice(pregunta)">
                            <div class="space-y-3 mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-sm font-bold text-blue-900">Opciones de respuesta</h4>
                                    <button @click="addOption(sIndex, pIndex)"
                                            class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-lg shadow-md transition-all duration-200 transform hover:scale-105">
                                        + Nueva opción
                                    </button>
                                </div>
                                
                                <template x-for="(op, oIndex) in pregunta.opciones" :key="op.id">
                                    <div class="flex gap-2 items-center">
                                        <span class="text-gray-400 font-mono text-sm" x-text="oIndex + 1 + '.'"></span>
                                        <input x-model="op.texto" 
                                               class="border-2 border-gray-300 focus:border-blue-500 p-2 rounded-lg w-full outline-none transition-colors"
                                               placeholder="Escribe una opción">
                                        <button @click="removeOption(sIndex, pIndex, oIndex)"
                                                class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- ESCALA LINEAL --}}
                        <template x-if="pregunta.tipo === 'escala_lineal'">
                            <div class="grid grid-cols-2 gap-4 mb-4 bg-purple-50 p-4 rounded-lg border border-purple-200">
                                <div>
                                    <label class="text-xs font-bold text-purple-900 uppercase tracking-wide">Desde</label>
                                    <input type="number" x-model.number="pregunta.escala_min" 
                                           class="border-2 border-gray-300 focus:border-purple-500 p-2 w-full rounded-lg outline-none mt-1">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-purple-900 uppercase tracking-wide">Hasta</label>
                                    <input type="number" x-model.number="pregunta.escala_max" 
                                           class="border-2 border-gray-300 focus:border-purple-500 p-2 w-full rounded-lg outline-none mt-1">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-purple-900 uppercase tracking-wide">Etiqueta inicial</label>
                                    <input x-model="pregunta.etiqueta_min" 
                                           class="border-2 border-gray-300 focus:border-purple-500 p-2 w-full rounded-lg outline-none mt-1">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-purple-900 uppercase tracking-wide">Etiqueta final</label>
                                    <input x-model="pregunta.etiqueta_max" 
                                           class="border-2 border-gray-300 focus:border-purple-500 p-2 w-full rounded-lg outline-none mt-1">
                                </div>
                            </div>
                        </template>

                        {{-- CUADRÍCULA --}}
                        <template x-if="['cuadricula_opciones','cuadricula_casillas'].includes(pregunta.tipo)">
                            <div class="grid grid-cols-2 gap-4 mb-4 bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                                <div>
                                    <h4 class="text-sm font-bold mb-3 text-indigo-900">Filas</h4>
                                    <template x-for="(f, fIndex) in pregunta.filas" :key="f.id">
                                        <div class="flex gap-2 mb-2">
                                            <input x-model="f.texto" 
                                                   class="border-2 border-gray-300 focus:border-indigo-500 p-2 w-full rounded-lg outline-none">
                                            <button @click="removeFila(sIndex, pIndex, fIndex)" 
                                                class="p-2 text-red-500 hover:bg-red-50 rounded-lg">

                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                    <button @click="addFila(sIndex, pIndex)"
                                        class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold mt-2 hover:underline">

                                        + Agregar fila
                                    </button>
                                </div>

                                <div>
                                    <h4 class="text-sm font-bold mb-3 text-indigo-900">Columnas</h4>
                                    <template x-for="(c, cIndex) in pregunta.columnas" :key="c.id">
                                        <div class="flex gap-2 mb-2">
                                            <input x-model="c.texto" 
                                                   class="border-2 border-gray-300 focus:border-indigo-500 p-2 w-full rounded-lg outline-none">
                                           <button @click="removeColumna(sIndex, pIndex, cIndex)" 
                                                class="p-2 text-red-500 hover:bg-red-50 rounded-lg">

                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                    <button @click="addColumna(sIndex, pIndex)"
                                            class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold mt-2 hover:underline">

                                        + Agregar columna
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- PREVIEW --}}
                        <div class="mt-4 pt-4 border-t-2 border-gray-200">
                            <div class="text-xs font-bold text-gray-500 mb-3 uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Vista previa
                            </div>

                            <div class="p-4 bg-white border-2 border-gray-200 rounded-lg shadow-inner"
                                 x-html="renderPregunta(pregunta)">
                            </div>
                        </div>

                    </div>
                </template>
            </div>
        </template>
    </main>

    {{-- MODAL PARA SELECCIONAR TIPO DE PREGUNTA --}}
    <div x-show="mostrarModalTipos"
         x-cloak
         @click.self="mostrarModalTipos = false"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto"
             @click.stop>
            
            <div class="sticky top-0 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-6 text-white rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold mb-1">Selecciona el tipo de pregunta</h2>
                        <p class="text-indigo-100 text-sm">Elige el formato que mejor se adapte a tu necesidad</p>
                    </div>
                    <button @click="mostrarModalTipos = false" 
                            class="p-2 hover:bg-white/20 rounded-lg transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                
                {{-- Texto Corto --}}
                <button @click="addPreguntaConTipo('texto_corto')" 
                        class="p-5 border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:shadow-lg 
                               transition-all duration-200 text-left group bg-gradient-to-br from-white to-blue-50">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-blue-500 rounded-lg text-white group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 mb-1">Texto corto</h3>
                            <p class="text-sm text-gray-600">Respuesta breve de una línea</p>
                        </div>
                    </div>
                </button>

                {{-- Párrafo --}}
                <button @click="addPreguntaConTipo('parrafo')" 
                        class="p-5 border-2 border-gray-200 rounded-xl hover:border-green-500 hover:shadow-lg 
                               transition-all duration-200 text-left group bg-gradient-to-br from-white to-green-50">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-green-500 rounded-lg text-white group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 mb-1">Párrafo</h3>
                            <p class="text-sm text-gray-600">Respuesta extensa de múltiples líneas</p>
                        </div>
                    </div>
                </button>

                {{-- Opción Múltiple --}}
                <button @click="addPreguntaConTipo('opcion_multiple')" 
                        class="p-5 border-2 border-gray-200 rounded-xl hover:border-purple-500 hover:shadow-lg 
                               transition-all duration-200 text-left group bg-gradient-to-br from-white to-purple-50">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-purple-500 rounded-lg text-white group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 mb-1">Opción múltiple</h3>
                            <p class="text-sm text-gray-600">Selección única entre varias opciones</p>
                        </div>
                    </div>
                </button>

                {{-- Casillas --}}
                <button @click="addPreguntaConTipo('casillas')" 
                        class="p-5 border-2 border-gray-200 rounded-xl hover:border-indigo-500 hover:shadow-lg 
                               transition-all duration-200 text-left group bg-gradient-to-br from-white to-indigo-50">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-indigo-500 rounded-lg text-white group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 mb-1">Casillas</h3>
                            <p class="text-sm text-gray-600">Selección múltiple de opciones</p>
                        </div>
                    </div>
                </button>

                {{-- Escala Lineal --}}
                <button @click="addPreguntaConTipo('escala_lineal')" 
                        class="p-5 border-2 border-gray-200 rounded-xl hover:border-pink-500 hover:shadow-lg 
                               transition-all duration-200 text-left group bg-gradient-to-br from-white to-pink-50">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-pink-500 rounded-lg text-white group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 mb-1">Escala lineal</h3>
                            <p class="text-sm text-gray-600">Calificación numérica con etiquetas</p>
                        </div>
                    </div>
                </button>

                {{-- Cuadrícula Opciones --}}
                <button @click="addPreguntaConTipo('cuadricula_opciones')" 
                        class="p-5 border-2 border-gray-200 rounded-xl hover:border-orange-500 hover:shadow-lg 
                               transition-all duration-200 text-left group bg-gradient-to-br from-white to-orange-50">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-orange-500 rounded-lg text-white group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 mb-1">Cuadrícula (opciones)</h3>
                            <p class="text-sm text-gray-600">Matriz con selección única por fila</p>
                        </div>
                    </div>
                </button>

                {{-- Cuadrícula Casillas --}}
                <button @click="addPreguntaConTipo('cuadricula_casillas')" 
                        class="p-5 border-2 border-gray-200 rounded-xl hover:border-teal-500 hover:shadow-lg 
                               transition-all duration-200 text-left group bg-gradient-to-br from-white to-teal-50">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-teal-500 rounded-lg text-white group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 mb-1">Cuadrícula (casillas)</h3>
                            <p class="text-sm text-gray-600">Matriz con selección múltiple</p>
                        </div>
                    </div>
                </button>

            </div>
        </div>
    </div>

    {{-- MODAL ELIMINAR SECCIÓN --}}
    <div x-show="confirmarEliminarSeccion !== null"
         x-cloak
         @click.self="confirmarEliminarSeccion = null"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full">
            <div class="flex items-center gap-4 mb-4">
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">¿Eliminar sección?</h2>
                </div>
            </div>
            <p class="text-gray-600 mb-6">
                Esta acción eliminará la sección y todas sus preguntas de forma permanente.
            </p>
            <div class="flex justify-end gap-3">
                <button @click="confirmarEliminarSeccion = null" 
                        class="px-6 py-3 border-2 border-gray-300 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button @click="removeSection(confirmarEliminarSeccion); confirmarEliminarSeccion = null"
                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold shadow-lg transition-all duration-200 transform hover:scale-105">
                    Sí, eliminar
                </button>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener("alpine:init", () => {
    if (window.formBuilder) {
        Alpine.data("formBuilder", window.formBuilder);
    }
});
</script>
@endsection
