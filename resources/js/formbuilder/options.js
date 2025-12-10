// ============================================================================
// OPTIONS MODULE
// Maneja agregar, eliminar y manipular opciones de las preguntas
// ============================================================================

export function createEmptyOption() {
    return {
        id: null,
        texto: ""
    };
}

export function addOption(question) {
    if (!Array.isArray(question.opciones)) {
        question.opciones = [];
    }
    question.opciones.push(createEmptyOption());
}

export function removeOption(question, index) {
    if (!Array.isArray(question.opciones)) return;
    question.opciones.splice(index, 1);
}

export function addGridRow(question) {
    if (!Array.isArray(question.filas)) {
        question.filas = [];
    }
    question.filas.push({ texto: "" });
}

export function removeGridRow(question, index) {
    if (!Array.isArray(question.filas)) return;
    question.filas.splice(index, 1);
}

export function addGridColumn(question) {
    if (!Array.isArray(question.columnas)) {
        question.columnas = [];
    }
    question.columnas.push({ texto: "" });
}

export function removeGridColumn(question, index) {
    if (!Array.isArray(question.columnas)) return;
    question.columnas.splice(index, 1);
}
