// resources/js/formbuilder/types/questionTypes.js

export const QUESTION_TYPES = {

    // ======================
    // TEXTO CORTO
    // ======================
    texto_corto: {
        label: "Respuesta corta",
        resetOnChange: true,
        renderOptions: (pregunta, container) => {
            container.innerHTML = `
                <label class="block text-gray-700 text-sm mb-1">Vista previa:</label>
                <input type="text"
                       disabled
                       class="w-full px-3 py-2 border rounded bg-gray-100 text-gray-600"
                       placeholder="Texto de respuesta corta">
            `;
        }
    },

    // ======================
    // PÁRRAFO
    // ======================
    parrafo: {
        label: "Párrafo",
        resetOnChange: true,
        renderOptions: (pregunta, container) => {
            container.innerHTML = `
                <label class="block text-gray-700 text-sm mb-1">Vista previa:</label>
                <textarea disabled
                          class="w-full px-3 py-2 border rounded bg-gray-100 text-gray-600 h-20"
                          placeholder="Texto de párrafo"></textarea>
            `;
        }
    },

    // ======================
    // OPCIÓN ÚNICA
    // ======================
    opcion_unica: {
        label: "Opción única",
        resetOnChange: true,
        renderOptions: (pregunta, container) => {
            container.innerHTML = ""; // Limpieza
            import("./seleccion.js").then(m => m.renderSeleccion(pregunta, container));
        }
    },

    // ======================
    // VARIAS OPCIONES
    // ======================
    varias_opciones: {
        label: "Casillas",
        resetOnChange: true,
        renderOptions: (pregunta, container) => {
            container.innerHTML = "";
            import("./checkboxes.js").then(m => m.renderCheckboxes(pregunta, container));
        }
    },

    // ======================
    // LISTA DESPLEGABLE
    // ======================
    desplegable: {
        label: "Lista desplegable",
        resetOnChange: true,
        renderOptions: (pregunta, container) => {
            container.innerHTML = "";
            import("./lista.js").then(m => m.renderLista(pregunta, container));
        }
    },

    // ======================
    // ESCALA LINEAL
    // ======================
    escala_lineal: {
        label: "Escala lineal",
        resetOnChange: false,
        renderOptions: (pregunta, container) => {
            container.innerHTML = "";
            import("./escala.js").then(m => m.renderEscala(pregunta, container));
        }
    },

    // ======================
    // CUADRÍCULA ÚNICA
    // ======================
    cuadricula_unica: {
        label: "Cuadrícula (respuesta única)",
        resetOnChange: false,
        renderOptions: (pregunta, container) => {
            container.innerHTML = "";
            import("./cuadricula.js").then(m => m.renderCuadriculaUnica(pregunta, container));
        }
    },

    // ======================
    // CUADRÍCULA MÚLTIPLE
    // ======================
    cuadricula_multiple: {
        label: "Cuadrícula (varias respuestas)",
        resetOnChange: false,
        renderOptions: (pregunta, container) => {
            container.innerHTML = "";
            import("./cuadricula.js").then(m => m.renderCuadriculaMultiple(pregunta, container));
        }
    },

    // ======================
    // FECHA
    // ======================
    fecha: {
        label: "Fecha",
        resetOnChange: true,
        renderOptions: (pregunta, container) => {
            container.innerHTML = `
                <label class="block text-gray-700 text-sm mb-1">Vista previa:</label>
                <input type="date"
                       disabled
                       class="w-full px-3 py-2 border rounded bg-gray-100 text-gray-600">
            `;
        }
    },

    // ======================
    // HORA
    // ======================
    hora: {
        label: "Hora",
        resetOnChange: true,
        renderOptions: (pregunta, container) => {
            container.innerHTML = `
                <label class="block text-gray-700 text-sm mb-1">Vista previa:</label>
                <input type="time"
                       disabled
                       class="w-full px-3 py-2 border rounded bg-gray-100 text-gray-600">
            `;
        }
    }
};
