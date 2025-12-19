@extends('layouts.app')

@section('content')
<div 
    x-data="{
        ...formBuilder(@json($formulario->secciones ?? []), {{ $formulario->id }}),
        confirmarEliminarSeccion: null
    }"
    class="flex w-full min-h-screen bg-gray-100 p-6">

    {{-- Panel lateral --}}
    <aside class="w-64 bg-white shadow p-4 space-y-4">
        <button @click="addSection()" class="w-full bg-blue-600 text-white px-3 py-2 rounded">
            + Agregar sección
        </button>

        <button @click="addPregunta(seleccionado.seccion)" 
                class="w-full bg-green-600 text-white px-3 py-2 rounded"
                :disabled="seleccionado.seccion === null">
            + Agregar pregunta
        </button>

        <button @click="duplicatePregunta(seleccionado.seccion, seleccionado.pregunta)"
                class="w-full bg-purple-600 text-white px-3 py-2 rounded"
                :disabled="seleccionado.pregunta === null">
            Duplicar pregunta
        </button>

        <button @click="removePregunta(seleccionado.seccion, seleccionado.pregunta)"
                class="w-full bg-red-600 text-white px-3 py-2 rounded"
                :disabled="seleccionado.pregunta === null">
            Eliminar pregunta
        </button>

        <button @click="guardar()" class="w-full bg-gray-800 text-white px-3 py-2 rounded">
            Guardar formulario
        </button>
    </aside>

    {{-- Área principal --}}
<main class="flex-1 p-6 space-y-6">
    <template x-for="(seccion, sIndex) in secciones" :key="seccion.id">
        <div
            class="bg-white p-6 shadow rounded space-y-4"
            @click="selectSection(sIndex)"
        >

            {{-- HEADER DE SECCIÓN --}}
            <div class="flex justify-between items-start gap-4">

                <div class="flex-1 space-y-2">
                    <input
                        x-model="seccion.titulo"
                        class="text-xl font-bold border-b w-full"
                        placeholder="Título de la sección"
                    >

                    <textarea
                        x-model="seccion.descripcion"
                        class="border p-2 w-full resize-none"
                        rows="2"
                        placeholder="Descripción de la sección"
                    ></textarea>
                </div>

                {{-- BOTÓN ELIMINAR SECCIÓN --}}
                <button
                    @click.stop="confirmarEliminarSeccion = sIndex"
                    class="w-9 h-9 flex items-center justify-center
                           rounded-full bg-red-600 text-white font-bold
                           hover:bg-red-700 transition shrink-0"
                    title="Eliminar sección"
                >
                    ✕
                </button>
            </div>

            {{-- PREGUNTAS --}}
            <template x-for="(pregunta, pIndex) in seccion.preguntas" :key="pregunta.id">
                <div
                    class="border p-4 rounded bg-gray-50"
                    :class="{'ring-2 ring-indigo-300': seleccionado.seccion === sIndex && seleccionado.pregunta === pIndex}"
                    @click.stop="selectPregunta(sIndex, pIndex)"
                >

                    <input
                        x-model="pregunta.texto"
                        class="border-b w-full font-medium mb-3"
                        placeholder="Pregunta"
                    >

                    <select
                        x-model="pregunta.tipo"
                        @change="changeTipo(sIndex, pIndex, pregunta.tipo)"
                        class="border p-2 rounded mb-3 w-full"
                    >
                        <template x-for="tipo in tipos" :key="tipo.value">
                            <option :value="tipo.value" x-text="tipo.label"></option>
                        </template>
                    </select>

                    {{-- OPCIONES --}}
                    <template x-if="isChoice(pregunta)">
                        <div class="space-y-2 mb-4">
                            <template x-for="(op, oIndex) in pregunta.opciones" :key="op.id">
                                <div class="flex gap-2">
                                    <input x-model="op.texto" class="border p-1 rounded w-full">
                                    <button
                                        @click="removeOption(sIndex, pIndex, oIndex)"
                                        class="text-red-500"
                                    >
                                        ✕
                                    </button>
                                </div>
                            </template>

                            <button
                                @click="addOption(sIndex, pIndex)"
                                class="text-blue-600 text-sm"
                            >
                                + Agregar opción
                            </button>
                        </div>
                    </template>

                    {{-- ESCALA LINEAL --}}
                    <template x-if="pregunta.tipo === 'escala_lineal'">
                        <div class="grid grid-cols-2 gap-3 mb-4 bg-blue-50 p-3 rounded">
                            <div>
                                <label class="text-xs">Desde</label>
                                <input type="number" x-model.number="pregunta.escala_min"
                                       class="border p-1 w-full">
                            </div>

                            <div>
                                <label class="text-xs">Hasta</label>
                                <input type="number" x-model.number="pregunta.escala_max"
                                       class="border p-1 w-full">
                            </div>

                            <div>
                                <label class="text-xs">Etiqueta inicial</label>
                                <input x-model="pregunta.etiqueta_min" class="border p-1 w-full">
                            </div>

                            <div>
                                <label class="text-xs">Etiqueta final</label>
                                <input x-model="pregunta.etiqueta_max" class="border p-1 w-full">
                            </div>
                        </div>
                    </template>

                    {{-- CUADRÍCULA --}}
                    <template x-if="['cuadricula_opciones','cuadricula_casillas'].includes(pregunta.tipo)">
                        <div class="grid grid-cols-2 gap-4 mb-4 bg-indigo-50 p-3 rounded">
                            <div>
                                <h4 class="text-sm font-semibold mb-2">Filas</h4>
                                <template x-for="(f, fIndex) in pregunta.filas" :key="f.id">
                                    <div class="flex gap-2 mb-1">
                                        <input x-model="f.texto" class="border p-1 w-full">
                                        <button
                                            @click="pregunta.filas.splice(fIndex,1)"
                                            class="text-red-500"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                </template>

                                <button
                                    @click="pregunta.filas.push({ id: Date.now(), texto: 'Nueva fila' })"
                                    class="text-blue-600 text-xs mt-1"
                                >
                                    + Agregar fila
                                </button>
                            </div>

                            <div>
                                <h4 class="text-sm font-semibold mb-2">Columnas</h4>
                                <template x-for="(c, cIndex) in pregunta.columnas" :key="c.id">
                                    <div class="flex gap-2 mb-1">
                                        <input x-model="c.texto" class="border p-1 w-full">
                                        <button
                                            @click="pregunta.columnas.splice(cIndex,1)"
                                            class="text-red-500"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                </template>

                                <button
                                    @click="pregunta.columnas.push({ id: Date.now(), texto: 'Nueva columna' })"
                                    class="text-blue-600 text-xs mt-1"
                                >
                                    + Agregar columna
                                </button>
                            </div>
                        </div>
                    </template>

                    {{-- VISTA PREVIA --}}
                    <div class="mt-4">
                        <div class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">
                            Vista previa del modal de la pregunta
                        </div>

                        <div
                            class="p-3 bg-white border rounded"
                            x-html="renderPregunta(pregunta)"
                        ></div>
                    </div>

                </div>
            </template>
        </div>
    </template>
</main>


    {{-- MODAL CONFIRMACIÓN ELIMINAR SECCIÓN --}}
    <div x-show="confirmarEliminarSeccion !== null"
         x-cloak
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

        <div class="bg-white rounded shadow-lg w-full max-w-md p-6">
            <h2 class="text-lg font-bold text-red-600 mb-3">
                ¿Eliminar sección?
            </h2>

            <p class="text-sm text-gray-600 mb-6">
                Esta acción eliminará la sección y todas sus preguntas.
                <br>
                <strong>No se puede deshacer.</strong>
            </p>

            <div class="flex justify-end gap-3">
                <button @click="confirmarEliminarSeccion = null"
                        class="px-4 py-2 border rounded">
                    No
                </button>

                <button
                    @click="removeSection(confirmarEliminarSeccion); confirmarEliminarSeccion = null"
                    class="px-4 py-2 bg-red-600 text-white rounded">
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
