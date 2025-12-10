@extends('layouts.app')

@section('content')
<div 
    x-data="formBuilder(@json($formulario->secciones ?? []))"
    class="flex w-full min-h-screen bg-gray-100">

    {{-- Panel lateral --}}
    <aside class="w-64 bg-white shadow p-4 space-y-4">

        <button @click="addSection()" 
            class="w-full bg-blue-600 text-white px-3 py-2 rounded shadow">
            + Agregar sección
        </button>

        <button @click="addPregunta(seleccionado.seccion)" 
            class="w-full bg-green-600 text-white px-3 py-2 rounded shadow"
            :disabled="seleccionado.seccion === null">
            + Agregar pregunta
        </button>

        <button 
            @click="duplicarPregunta(seleccionado.seccion, seleccionado.pregunta)"
            class="w-full bg-purple-600 text-white px-3 py-2 rounded shadow"
            :disabled="seleccionado.pregunta === null">
            Duplicar pregunta
        </button>

        <button 
            @click="removePregunta(seleccionado.seccion, seleccionado.pregunta)"
            class="w-full bg-red-600 text-white px-3 py-2 rounded shadow"
            :disabled="seleccionado.pregunta === null">
            Eliminar pregunta
        </button>

        <button @click="guardar(formId)" 
            class="w-full bg-gray-800 text-white px-3 py-2 rounded shadow">
            Guardar formulario
        </button>

    </aside>

    {{-- Área principal --}}
    <main class="flex-1 p-6 space-y-6">

        <template x-for="(seccion, sIndex) in secciones" :key="sIndex">
            <div class="bg-white p-6 shadow rounded" @click="selectSection(sIndex)">

                <input x-model="seccion.titulo"
                       class="text-xl font-bold border-b w-full mb-4"
                       placeholder="Título de la sección">

                <textarea x-model="seccion.descripcion"
                          class="border p-2 w-full mb-6"
                          placeholder="Descripción de la sección"></textarea>

                {{-- Preguntas --}}
                <template x-for="(pregunta, pIndex) in seccion.preguntas">
                    <div class="border p-4 rounded mb-4 bg-gray-50 relative"
                         @click.stop="selectPregunta(sIndex, pIndex)">

                        <input x-model="pregunta.texto"
                               class="border-b w-full font-medium mb-3"
                               placeholder="Pregunta">

                        <select x-model="pregunta.tipo"
                                @change="changeTipo(sIndex, pIndex, pregunta.tipo)"
                                class="border p-2 rounded mb-3 w-full">
                            <template x-for="tipo in tipos">
                                <option :value="tipo.value" x-text="tipo.label"></option>
                            </template>
                        </select>

                        {{-- Opciones --}}
                        <template x-if="pregunta.opciones && pregunta.opciones.length">
                            <div class="space-y-2">
                                <template x-for="(op, oIndex) in pregunta.opciones">
                                    <div class="flex items-center space-x-2">
                                        <input x-model="op.texto"
                                               class="border p-1 rounded w-full">

                                        <button @click="removeOption(sIndex, pIndex, oIndex)"
                                                class="text-red-500">✕</button>
                                    </div>
                                </template>

                                <button @click="addOption(sIndex, pIndex)"
                                        class="text-blue-600 text-sm">
                                    + Agregar opción
                                </button>
                            </div>
                        </template>

                    </div>
                </template>

            </div>
        </template>

    </main>
</div>



<script>
    document.addEventListener("alpine:init", () => {
        Alpine.data("formBuilder", window.formBuilder);
    });
</script>


@endsection
