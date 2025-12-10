<div 
    class="flex items-center gap-3 py-1"
    x-data="{ texto: option.texto }"
>
    {{-- Icono de arrastrar --}}
    <span class="cursor-move text-gray-400">
        ⋮⋮
    </span>

    {{-- Input de texto --}}
    <input 
        type="text"
        class="flex-1 border-b border-gray-300 focus:border-blue-500 outline-none py-1"
        placeholder="Opción"
        x-model="texto"
        @input="option.texto = texto"
    >

    {{-- Botón eliminar --}}
    <button 
        type="button" 
        class="text-red-500 hover:text-red-700"
        @click="removeOption(index)"
    >
        ✕
    </button>
</div>
