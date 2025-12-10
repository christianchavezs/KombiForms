<div 
    x-show="previewMode" 
    class="w-full min-h-screen bg-gray-100 p-6 rounded-lg border border-gray-300 shadow-inner"
>
    <!-- Header -->
    <div class="bg-white p-6 rounded-lg shadow mb-6 border border-gray-200">
        <h2 class="text-2xl font-semibold text-gray-800 mb-2" x-text="formData.titulo || 'Vista previa del formulario'"></h2>
        <p class="text-gray-600" x-text="formData.descripcion"></p>
    </div>

    <!-- Secciones -->
    <template x-for="(seccion, sIndex) in secciones" :key="sIndex">
        <div class="bg-white p-6 rounded-lg shadow mb-6 border border-gray-200">

            <!-- Título de sección -->
            <h3 class="text-xl font-semibold text-gray-700 mb-3" x-text="seccion.titulo || 'Sección sin título'"></h3>

            <!-- Descripción de sección -->
            <p class="text-gray-600 mb-4" x-text="seccion.descripcion"></p>

            <!-- Preguntas -->
            <template x-for="(pregunta, pIndex) in seccion.preguntas" :key="pIndex">

                <div class="mb-6 border-b border-gray-200 pb-4">

                    <!-- Texto de pregunta -->
                    <label class="block text-gray-800 font-medium mb-2">
                        <span x-text="(pIndex+1) + '. ' + (pregunta.texto || 'Pregunta sin título')"></span>
                        <span class="text-red-500" x-show="pregunta.obligatorio">*</span>
                    </label>

                    <!-- ====================== -->
                    <!--     TIPOS DE INPUT     -->
                    <!-- ====================== -->

                    <!-- TEXTO CORTO -->
                    <template x-if="pregunta.tipo === 'texto_corto'">
                        <input type="text" class="w-full border border-gray-300 rounded-lg p-2"
                               placeholder="Respuesta corta" disabled>
                    </template>

                    <!-- PÁRRAFO -->
                    <template x-if="pregunta.tipo === 'parrafo'">
                        <textarea class="w-full border border-gray-300 rounded-lg p-2" rows="3"
                                  placeholder="Respuesta larga" disabled></textarea>
                    </template>

                    <!-- OPCIÓN ÚNICA (RADIO) -->
                    <template x-if="pregunta.tipo === 'opcion_unica'">
                        <div>
                            <template x-for="(op, oIndex) in pregunta.opciones" :key="oIndex">
                                <label class="flex items-center mb-1">
                                    <input type="radio" class="mr-2" disabled>
                                    <span x-text="op.texto || 'Opción sin texto'"></span>
                                </label>
                            </template>
                        </div>
                    </template>

                    <!-- VARIAS OPCIONES (CHECKBOX) -->
                    <template x-if="pregunta.tipo === 'varias_opciones'">
                        <div>
                            <template x-for="(op, oIndex) in pregunta.opciones" :key="oIndex">
                                <label class="flex items-center mb-1">
                                    <input type="checkbox" class="mr-2" disabled>
                                    <span x-text="op.texto || 'Opción sin texto'"></span>
                                </label>
                            </template>
                        </div>
                    </template>

                    <!-- LISTA DESPLEGABLE -->
                    <template x-if="pregunta.tipo === 'desplegable'">
                        <select class="border border-gray-300 rounded-lg p-2 w-full" disabled>
                            <template x-for="(op, oIndex) in pregunta.opciones" :key="oIndex">
                                <option x-text="op.texto || 'Opción sin texto'"></option>
                            </template>
                        </select>
                    </template>

                    <!-- ESCALA LINEAL -->
                    <template x-if="pregunta.tipo === 'escala_lineal'">
                        <div class="flex items-center gap-3">
                            <span x-text="pregunta.escala_min || 1"></span>

                            <template x-for="num in (pregunta.escala_max || 5)">
                                <label class="flex flex-col items-center">
                                    <input type="radio" class="mb-1" disabled>
                                    <span x-text="num"></span>
                                </label>
                            </template>

                            <span x-text="pregunta.escala_max || 5"></span>
                        </div>
                    </template>

                    <!-- CUADRÍCULA DE OPCIÓN ÚNICA -->
                    <template x-if="pregunta.tipo === 'cuadricula_unica'">
                        <table class="w-full border-collapse text-center">
                            <tr>
                                <th></th>
                                <template x-for="col in pregunta.columnas" :key="col">
                                    <th x-text="col"></th>
                                </template>
                            </tr>

                            <template x-for="fila in pregunta.filas" :key="fila">
                                <tr>
                                    <th class="text-left" x-text="fila"></th>
                                    <template x-for="col in pregunta.columnas">
                                        <td><input type="radio" disabled></td>
                                    </template>
                                </tr>
                            </template>
                        </table>
                    </template>

                    <!-- CUADRÍCULA DE VARIAS OPCIONES -->
                    <template x-if="pregunta.tipo === 'cuadricula_multiple'">
                        <table class="w-full border-collapse text-center">
                            <tr>
                                <th></th>
                                <template x-for="col in pregunta.columnas" :key="col">
                                    <th x-text="col"></th>
                                </template>
                            </tr>

                            <template x-for="fila in pregunta.filas" :key="fila">
                                <tr>
                                    <th class="text-left" x-text="fila"></th>
                                    <template x-for="col in pregunta.columnas">
                                        <td><input type="checkbox" disabled></td>
                                    </template>
                                </tr>
                            </template>
                        </table>
                    </template>

                    <!-- FECHA -->
                    <template x-if="pregunta.tipo === 'fecha'">
                        <input type="date" class="border p-2 rounded-lg" disabled>
                    </template>

                    <!-- HORA -->
                    <template x-if="pregunta.tipo === 'hora'">
                        <input type="time" class="border p-2 rounded-lg" disabled>
                    </template>

                </div>

            </template>
        </div>
    </template>

    <!-- Botón para cerrar vista previa -->
    <div class="text-center mt-6">
        <button 
            @click="previewMode = false"
            class="bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700 transition"
        >
            Cerrar vista previa
        </button>
    </div>
</div>
