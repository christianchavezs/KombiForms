// resources/js/formbuilder/render.js
// ==========================================
// Render dinámico del contenido en pantalla
// (Secciones y Preguntas del Form Builder)
// ==========================================

import { QUESTION_TYPES } from "./types/questionTypes";

// -----------------------------------------------------
// Render de una sección completa
// -----------------------------------------------------
export function renderSection(section, container, onUpdate) {
    container.innerHTML = "";

    const sectionDiv = document.createElement("div");
    sectionDiv.className = "fb-section";

    // =======================
    // TÍTULO
    // =======================
    const titleInput = document.createElement("input");
    titleInput.type = "text";
    titleInput.className = "fb-section-title";
    titleInput.value = section.titulo || "Nueva sección";

    titleInput.addEventListener("input", () => {
        section.titulo = titleInput.value;
        onUpdate();
    });

    // =======================
    // DESCRIPCIÓN
    // =======================
    const descInput = document.createElement("textarea");
    descInput.className = "fb-section-desc";
    descInput.value = section.descripcion || "";

    descInput.addEventListener("input", () => {
        section.descripcion = descInput.value;
        onUpdate();
    });

    sectionDiv.appendChild(titleInput);
    sectionDiv.appendChild(descInput);

    // =======================
    // CONTENEDOR DE PREGUNTAS
    // =======================
    const preguntasWrap = document.createElement("div");
    preguntasWrap.className = "fb-questions-wrapper";

    section.preguntas.forEach((pregunta) => {
        const preguntaEl = renderPregunta(pregunta, onUpdate);
        preguntasWrap.appendChild(preguntaEl);
    });

    sectionDiv.appendChild(preguntasWrap);
    container.appendChild(sectionDiv);
}


// ============================================================
// Render de una pregunta (Control completo del comportamiento)
// ============================================================
export function renderPregunta(pregunta, onUpdate) {
    const div = document.createElement("div");
    div.className = "fb-question-item";

    // =======================
    // TEXTO DE LA PREGUNTA
    // =======================
    const label = document.createElement("input");
    label.type = "text";
    label.className = "fb-question-label";
    label.value = pregunta.texto || "Nueva pregunta";

    label.addEventListener("input", () => {
        pregunta.texto = label.value;
        onUpdate();
    });

    div.appendChild(label);

    // =======================
    // SELECTOR DE TIPO
    // =======================
    const select = document.createElement("select");
    select.className = "fb-question-type";

    for (const typeKey in QUESTION_TYPES) {
        const opt = document.createElement("option");
        opt.value = typeKey;
        opt.textContent = QUESTION_TYPES[typeKey].label;

        if (pregunta.tipo === typeKey) {
            opt.selected = true;
        }

        select.appendChild(opt);
    }

    select.addEventListener("change", () => {
        const nuevoTipo = select.value;

        // Guardar tipo
        pregunta.tipo = nuevoTipo;

        // NO BORRAR opciones si no corresponde
        if (QUESTION_TYPES[nuevoTipo].resetOnChange) {
            pregunta.opciones = [];
            pregunta.filas = [];
            pregunta.columnas = [];
            pregunta.escala_min = 1;
            pregunta.escala_max = 5;
        }

        renderOpciones(pregunta, opcionesWrap);
        onUpdate();
    });

    div.appendChild(select);

    // =======================
    // CONTENEDOR PARA INPUTS DINÁMICOS
    // =======================
    const opcionesWrap = document.createElement("div");
    opcionesWrap.className = "fb-options-wrapper";

    renderOpciones(pregunta, opcionesWrap);

    div.appendChild(opcionesWrap);

    return div;
}


// ============================================================
// Render específico según el tipo de pregunta
// ============================================================
function renderOpciones(pregunta, container) {
    container.innerHTML = "";

    const tipo = pregunta.tipo;

    if (!QUESTION_TYPES[tipo]) {
        container.innerHTML = "<p style='color:red'>Tipo no válido</p>";
        return;
    }

    // ===============================
    // TIPOS SIMPLES: texto y párrafo
    // ===============================
    if (tipo === "texto" || tipo === "texto_corto") {
        container.innerHTML = `
            <input type="text" disabled
                   class="border p-2 mt-2 rounded w-full"
                   placeholder="Respuesta corta">
        `;
        return;
    }

    if (tipo === "parrafo") {
        container.innerHTML = `
            <textarea disabled
                      class="border p-2 mt-2 rounded w-full"
                      placeholder="Respuesta larga"></textarea>
        `;
        return;
    }

    // ========================================
    // TIPOS QUE REQUIEREN renderOptions()
    // ========================================
    const typeDef = QUESTION_TYPES[tipo];

    if (typeDef.renderOptions) {
        typeDef.renderOptions(pregunta, container);
        return;
    }

    container.innerHTML = `<em>No hay render para este tipo</em>`;
}
