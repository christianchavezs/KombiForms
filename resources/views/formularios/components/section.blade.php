<div 
    x-data 
    class="bg-white shadow rounded-lg border border-gray-200 p-6 mb-6"
>

    {{-- HEADER DE LA SECCIÓN --}}
    <div class="flex justify-between items-start mb-4">
        <div class="w-full">
            <input 
                type="text" 
                class="w-full text-xl font-semibold border-b border-gray-300 focus:border-indigo-500 outline-none"
                placeholder="Título de la sección"
                x-model="section.titulo"
            >

            <textarea 
                class="w-full mt-2 text-sm border-b border-gray-300 focus:border-indigo-500 outline-none resize-none"
                placeholder="Descripción de la sección (opcional)"
                x-model="section.descripcion"
                rows="2"
            ></textarea>
        </div>

        {{-- BOTONES DE ACCIONES --}}
        <div class="flex flex-col gap-2 ml-4">

            <button 
                class="p-2 rounded-full hover:bg-gray-100 text-gray-600"
                @click="$dispatch('duplicate-section', { id: section.id })"
                title="Duplicar sección"
            >
                ⧉
            </button>

            <button 
                class="p-2 rounded-full hover:bg-gray-100 text-red-600"
                @click="$dispatch('delete-section', { id: section.id })"
                title="Eliminar sección"
            >
                🗑
            </button>
        </div>
    </div>

    {{-- LISTA DE PREGUNTAS --}}
    <template x-for="pregunta in section.preguntas" :key="pregunta.uid">
        <div class="mt-4">
            @include('formularios.components.question')
        </div>
    </template>

    {{-- BOTÓN PARA AGREGAR PREGUNTA --}}
    <div class="mt-4">
        <button 
            @click="$dispatch('add-question', { sectionId: section.id })"
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow"
        >
            ➕ Agregar pregunta
        </button>
    </div>

</div>
