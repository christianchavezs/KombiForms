@extends('layouts.app')

@section('content')
<div 
    x-data="formBuilder(@json($formulario->secciones ?? []), {{ $formulario->id }})"
    class="flex w-full min-h-screen bg-gray-100 p-6">

    {{-- Panel lateral --}}
    <aside class="w-64 bg-white shadow p-4 space-y-4">
        <button @click="addSection()" class="w-full bg-blue-600 text-white px-3 py-2 rounded shadow">
            + Agregar sección
        </button>

        <button @click="addPregunta(seleccionado.seccion)" 
                class="w-full bg-green-600 text-white px-3 py-2 rounded shadow"
                :disabled="seleccionado.seccion === null">
            + Agregar pregunta
        </button>

        <button @click="duplicatePregunta(seleccionado.seccion, seleccionado.pregunta)"
                class="w-full bg-purple-600 text-white px-3 py-2 rounded shadow"
                :disabled="seleccionado.pregunta === null">
            Duplicar pregunta
        </button>

        <button @click="removePregunta(seleccionado.seccion, seleccionado.pregunta)"
                class="w-full bg-red-600 text-white px-3 py-2 rounded shadow"
                :disabled="seleccionado.pregunta === null">
            Eliminar pregunta
        </button>

        <button @click="guardar()" class="w-full bg-gray-800 text-white px-3 py-2 rounded shadow">
            Guardar formulario
        </button>
    </aside>

    {{-- Área principal --}}
    <main class="flex-1 p-6 space-y-6">
        <template x-for="(seccion, sIndex) in secciones" :key="seccion.id">
            <div class="bg-white p-6 shadow rounded relative" @click="selectSection(sIndex)">

                {{-- BOTÓN ELIMINAR SECCIÓN --}}
                <button 
                    @click.stop="removeSection(sIndex)"
                    class="absolute top-3 right-3 text-red-600 font-bold text-xl"
                    title="Eliminar sección">
                    ✕
                </button>

                <input x-model="seccion.titulo"
                       class="text-xl font-bold border-b w-full mb-4"
                       placeholder="Título de la sección">

                <textarea x-model="seccion.descripcion"
                          class="border p-2 w-full mb-6"
                          placeholder="Descripción de la sección"></textarea>

                {{-- Preguntas --}}
                <template x-for="(pregunta, pIndex) in seccion.preguntas" :key="pregunta.id">
                    <div :class="{'ring-2 ring-indigo-200': seleccionado.seccion === sIndex && seleccionado.pregunta === pIndex}"
                         class="border p-4 rounded mb-4 bg-gray-50 relative"
                         @click.stop="selectPregunta(sIndex, pIndex)">

                        <input x-model="pregunta.texto"
                               class="border-b w-full font-medium mb-3"
                               placeholder="Pregunta">

                        {{-- SELECT: bind explícito con x-model para que respete el tipo --}}
                        <select
    :key="pregunta.id + '-tipo'"
    x-model="pregunta.tipo"
    @change="changeTipo(sIndex, pIndex, pregunta.tipo)"
    class="border p-2 rounded mb-3 w-full"
>
    <template x-for="tipo in tipos" :key="tipo.value">
        <option :value="tipo.value" x-text="tipo.label"></option>
    </template>
</select>


                        {{-- Opciones dinámicas --}}
                        <template x-if="pregunta.opciones && pregunta.opciones.length">
                            <div class="space-y-2">
                                <template x-for="(op, oIndex) in pregunta.opciones" :key="op.id">
                                    <div class="flex items-center space-x-2">
                                        <input x-model="op.texto" class="border p-1 rounded w-full">
                                        <button @click="removeOption(sIndex, pIndex, oIndex)" class="text-red-500">✕</button>
                                    </div>
                                </template>

                                <button @click="addOption(sIndex, pIndex)" class="text-blue-600 text-sm">
                                    + Agregar opción
                                </button>
                            </div>
                        </template>

                        {{-- indicadores de tipo / ayuda --}}
                        <div class="text-xs text-gray-500 mt-2">
                            <span x-text="pregunta.tipo"></span>
                            <span class="ml-4" x-html="preview(pregunta)"></span>
                        </div>
                    </div>
                </template>

            </div>
        </template>
    </main>
</div>

<script>
    window.preview = function(pregunta) {
        if (!pregunta || !pregunta.tipo) return "";

        switch (pregunta.tipo) {

            case "texto_corto":
            case "texto":
                return "📝 Respuesta corta";

            case "parrafo":
                return "📄 Respuesta larga";

            case "opcion_multiple":
                return `🔘 Opción múltiple (${pregunta.opciones?.length ?? 0} opciones)`;

            case "casillas":
                return `☑️ Casillas (${pregunta.opciones?.length ?? 0} opciones)`;

            case "desplegable":
                return `⬇️ Desplegable (${pregunta.opciones?.length ?? 0} opciones)`;

            case "escala_lineal":
                return `📊 Escala ${pregunta.escala_min ?? 1} – ${pregunta.escala_max ?? 5}`;

            case "cuadricula_opciones":
                return `🧩 Cuadrícula (radios) ${pregunta.filas?.length ?? 0} × ${pregunta.columnas?.length ?? 0}`;

            case "cuadricula_casillas":
                return `🧩 Cuadrícula (checks) ${pregunta.filas?.length ?? 0} × ${pregunta.columnas?.length ?? 0}`;

            default:
                return "Tipo no reconocido";
        }
    }
</script>




<script>
    // Si ya cargaste formBuilder en window (import en app.js) esta linea liga el nombre "formBuilder"
    // como Alpine.data disponible. Si no, asegúrate de importar formbuilder en resources/js/app.js
    document.addEventListener("alpine:init", () => {
        // register only if window.formBuilder exists and is a function
        if (window && typeof window.formBuilder === 'function') {
            Alpine.data("formBuilder", window.formBuilder);
        } else {
            console.warn("window.formBuilder no está disponible. Revisa resources/js/app.js o el build de Vite.");
        }
    });
</script>

@endsection
