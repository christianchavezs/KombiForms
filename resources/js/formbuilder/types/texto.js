// resources/js/formbuilder/types/texto.js

// Constructor de la pregunta
export function createTextoQuestion(texto = "Nueva pregunta") {
    return {
        tipo: "texto_corto",
        texto: texto,
        obligatorio: false,
        orden: 0,
        opciones: [],      // No aplica
        escala_min: null,
        escala_max: null,
        filas: [],
        columnas: []
    };
}

// ==============================
// RENDERER para Texto Corto
// (Lo que se muestra en pantalla)
// ==============================
export function renderOptions(pregunta, container) {
    container.innerHTML = "";

    const wrapper = document.createElement("div");
    wrapper.className = "fb-texto-preview";

    const input = document.createElement("input");
    input.type = "text";
    input.className = "fb-input-preview";
    input.placeholder = "Respuesta corta (vista previa)";
    input.disabled = true;

    wrapper.appendChild(input);
    container.appendChild(wrapper);
}
