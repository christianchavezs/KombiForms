<div 
    class="bg-white rounded-xl shadow p-5 mb-4 border border-gray-200"
    x-data="questionComponent({
        questionId: '{{ $question->id ?? '' }}',
        type: '{{ $question->tipo ?? 'texto_corto' }}',
        text: @js($question->texto ?? ''),
        required: {{ $question->obligatoria ? 'true' : 'false' }},
        escalaMin: {{ $question->escala_min ?? 1 }},
        escalaMax: {{ $question->escala_max ?? 5 }},
        opciones: @js($question->opciones ?? []),
    })"
>

    <!-- ░░░ HEADER DE LA PREGUNTA ░░░ -->
    <div class="flex items-start justify-between mb-4">
        <input 
            type="text"
            class="w-full border-b border-gray-300 focus:border-blue-500 focus:ring-0 text-lg font-semibold"
            placeholder="Escribe la pregunta..."
            x-model="text"
        />
        
        <!-- Tipo de pregunta -->
        <select
            class="ml-4 border-gray-300 rounded-lg"
            x-model="type"
            @change="onTypeChange"
        >
            <option value="texto_corto">Respuesta corta</option>
            <option value="texto_largo">Párrafo</option>
            <option value="opcion_multiple">Opción múltiple</option>
            <option value="casillas">Casillas (checkbox)</option>
            <option value="desplegable">Desplegable</option>
            <option value="escala">Escala lineal</option>
            <option value="cuadricula_opciones">Cuadrícula (opciones)</option>
            <option value="cuadricula_casillas">Cuadrícula (casillas)</option>
        </select>
    </div>

    <!-- ░░░ OPCIONES (solo si el tipo admite opciones) ░░░ -->
    <template x-if="showOptions">
        <div class="ml-2 mt-3 space-y-3">

            <template x-for="(op, oi) in opciones" :key="op.id ?? oi">
                <div class="flex items-center space-x-2">
                    
                    <span x-show="type === 'opcion_multiple'" class="text-gray-500">●</span>
                    <span x-show="type === 'casillas'" class="text-gray-500">☐</span>

                    <input 
                        type="text"
                        class="flex-1 border-b border-gray-300 focus:border-blue-500 focus:ring-0"
                        x-model="op.texto"
                    />

                    <button 
                        class="text-red-600 hover:text-red-800"
                        @click="removeOption(oi)"
                    >
                        ✕
                    </button>
                </div>
            </template>

            <button 
                class="text-blue-600 hover:text-blue-800 text-sm"
                @click="addOption"
            >
                + Agregar opción
            </button>
        </div>
    </template>

    <!-- ░░░ ESCALA (si aplica) ░░░ -->
    <template x-if="type === 'escala'">
        <div class="mt-4">
            <label class="block text-sm text-gray-600">Escala del:</label>
            <div class="flex space-x-4 mt-1">
                <input type="number" class="w-20 border-gray-300 rounded" x-model="escalaMin" min="1">
                <span>a</span>
                <input type="number" class="w-20 border-gray-300 rounded" x-model="escalaMax" min="2">
            </div>
        </div>
    </template>

    <!-- ░░░ FOOTER ░░░ -->
    <div class="flex items-center justify-between mt-5 border-t pt-3">

        <label class="flex items-center space-x-2 cursor-pointer">
            <input type="checkbox" x-model="required" class="rounded">
            <span class="text-sm">Obligatoria</span>
        </label>

        <div class="space-x-3 text-gray-600 text-sm">
            <button @click="$dispatch('duplicate-question')" class="hover:text-gray-900">Duplicar</button>
            <button @click="$dispatch('delete-question')" class="hover:text-red-700">Eliminar</button>
        </div>
    </div>

</div>


<script>
function questionComponent(data) {
    return {
        ...data,

        get showOptions() {
            return [
                'opcion_multiple',
                'casillas',
                'desplegable'
            ].includes(this.type);
        },

        onTypeChange() {
            if (this.showOptions && this.opciones.length === 0) {
                this.addOption();
            }
        },

        addOption() {
            this.opciones.push({
                id: null,
                texto: 'Opción ' + (this.opciones.length + 1)
            });
        },

        removeOption(index) {
            this.opciones.splice(index, 1);
        }
    };
}
</script>
