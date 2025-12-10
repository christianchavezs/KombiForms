<div 
    class="w-64 bg-white shadow-xl rounded-xl p-4 flex flex-col gap-4 border border-gray-200"
>

    {{-- TÍTULO DEL PANEL --}}
    <h2 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" 
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" 
                d="M3 7h18M3 12h18M3 17h18" />
        </svg>
        Constructor
    </h2>

    {{-- BOTÓN: AGREGAR SECCIÓN --}}
    <button 
        class="w-full flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-3 rounded-lg shadow transition"
        @click="addSection()"
    >
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 4v16m8-8H4" />
        </svg>
        Agregar sección
    </button>

    {{-- BOTÓN: AGREGAR PREGUNTA --}}
    <button 
        class="w-full flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white py-2 px-3 rounded-lg shadow transition"
        @click="addQuestion()"
    >
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 4v16m8-8H4" />
        </svg>
        Agregar pregunta
    </button>

    {{-- BOTÓN: DUPLICAR PREGUNTA --}}
    <button 
        class="w-full flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-3 rounded-lg shadow transition"
        @click="duplicateSelected()"
    >
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M8 7h8v10H8zM16 7h2v10h-2z" />
        </svg>
        Duplicar
    </button>

    {{-- BOTÓN: ELIMINAR --}}
    <button 
        class="w-full flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded-lg shadow transition"
        @click="removeSelected()"
    >
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M6 18L18 6M6 6l12 12" />
        </svg>
        Eliminar
    </button>

    {{-- BOTÓN: REORDENAR --}}
    <button 
        class="w-full flex items-center gap-2 bg-gray-700 hover:bg-gray-800 text-white py-2 px-3 rounded-lg shadow transition"
        @click="toggleSortMode()"
    >
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" 
                d="M4 6h16M4 12h10M4 18h6" />
        </svg>
        Reordenar
    </button>

    {{-- BOTÓN: VISTA PREVIA --}}
    <button 
        class="w-full flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white py-2 px-3 rounded-lg shadow transition"
        @click="preview()"
    >
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" 
                d="M15 10l4.553 2.276a1 1 0 010 1.448L15 16m-6 0l-4.553-2.276a1 1 0 010-1.448L9 10" />
        </svg>
        Vista previa
    </button>

    {{-- BOTÓN: GUARDAR --}}
    <button 
        class="w-full flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-lg shadow transition mt-4"
        @click="save()"
    >
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" 
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" 
                d="M5 13l4 4L19 7" />
        </svg>
        Guardar formulario
    </button>

</div>
