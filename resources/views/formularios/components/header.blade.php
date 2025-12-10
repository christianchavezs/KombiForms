<div class="w-full bg-white shadow-sm border-b px-6 py-4 flex items-center justify-between">
    
    {{-- Título del formulario --}}
    <div class="flex flex-col">
        <h1 class="text-xl font-semibold text-gray-800" x-text="formData.titulo"></h1>

        <p class="text-sm text-gray-500" x-show="formData.descripcion" x-text="formData.descripcion"></p>
    </div>

    {{-- Botones de acciones principales --}}
    <div class="flex space-x-3">

        {{-- Botón guardar estructura --}}
        <button 
            @click="guardarEstructura()"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow transition">
            Guardar
        </button>

        {{-- Botón vista previa --}}
        <button 
            @click="vistaPrevia = true"
            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md shadow transition">
            Vista Previa
        </button>

        {{-- Botón regresar --}}
        <a href="{{ route('formularios.index') }}"
            class="px-4 py-2 bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-md shadow transition">
            Regresar
        </a>

    </div>
</div>


{{-- MODAL DE VISTA PREVIA --}}
<div 
    x-show="vistaPrevia"
    x-transition
    class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">

    <div class="bg-white w-11/12 max-w-3xl rounded-lg shadow-lg p-6 relative"
         @click.away="vistaPrevia = false">

        {{-- Cerrar --}}
        <button 
            class="absolute top-3 right-3 text-gray-400 hover:text-gray-600"
            @click="vistaPrevia = false">
            ✕
        </button>

        <h2 class="text-xl font-semibold mb-4">Vista previa del formulario</h2>

        {{-- Render dinámico de todas las secciones y preguntas --}}
        <template x-for="sec in secciones" :key="sec.id">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700" x-text="sec.titulo"></h3>

                <p class="text-sm text-gray-500 mb-2" x-text="sec.descripcion"></p>

                <div class="space-y-4">

                    <template x-for="preg in sec.preguntas" :key="preg.id">
                        <div class="border rounded-md p-4 bg-gray-50">
                            <p class="font-medium text-gray-800" x-text="preg.texto"></p>

                            <small class="text-gray-500" x-show="preg.obligatoria">(Obligatoria)</small>

                            {{-- Opciones tipo multiple --}}
                            <template x-if="preg.tipo === 'multiple'">
                                <div class="mt-3 space-y-1">
                                    <template x-for="op in preg.opciones" :key="op.id">
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" disabled>
                                            <span x-text="op.texto"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            {{-- Opciones tipo checkbox --}}
                            <template x-if="preg.tipo === 'checkbox'">
                                <div class="mt-3 space-y-1">
                                    <template x-for="op in preg.opciones">
                                        <div class="flex items-center space-x-2">
                                            <input type="checkbox" disabled>
                                            <span x-text="op.texto"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            {{-- Texto corto --}}
                            <template x-if="preg.tipo === 'texto_corto'">
                                <input type="text"
                                       class="mt-3 w-full border rounded px-3 py-1 text-sm"
                                       placeholder="Respuesta corta"
                                       disabled>
                            </template>

                            {{-- Texto largo --}}
                            <template x-if="preg.tipo === 'texto_largo'">
                                <textarea rows="3"
                                          class="mt-3 w-full border rounded px-3 py-1 text-sm"
                                          placeholder="Respuesta larga"
                                          disabled></textarea>
                            </template>

                        </div>
                    </template>
                </div>
            </div>
        </template>

    </div>
</div>
