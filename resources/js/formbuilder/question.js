// ============================================================================
// QUESTIONS MODULE
// Maneja la creación, edición, cambio de tipo y estructura de las preguntas
// ============================================================================

export function createQuestion(texto = "Nueva pregunta") {
    const q = createEmptyQuestion();
    q.texto = texto;
    return q;
}


export function createEmptyQuestion() {
    return {
        id: null,
        tipo: "texto_corto",
        texto: "",
        obligatoria: false,
        escala_min: 1,
        escala_max: 5,
        opciones: [],
        filas: [],
        columnas: [],
    };
}

export function questionTypes() {
    return [
        { value: "texto_corto", label: "Respuesta corta" },
        { value: "parrafo", label: "Párrafo" },
        { value: "opcion_multiple", label: "Opción múltiple" },
        { value: "casillas", label: "Casillas (checkbox)" },
        { value: "desplegable", label: "Desplegable" },
        { value: "escala_lineal", label: "Escala lineal" },
        { value: "cuadricula_opciones", label: "Cuadrícula de opciones" },
        { value: "cuadricula_casillas", label: "Cuadrícula casillas" },
    ];
}

export function formatQuestionForSave(question, index) {
    return {
        tipo: question.tipo,
        texto: question.texto,
        obligatorio: question.obligatoria ? 1 : 0,
        orden: index + 1,
        escala_min: question.escala_min ?? null,
        escala_max: question.escala_max ?? null,
        opciones: question.opciones?.map((opt, i) => ({
            texto: opt.texto || "",
            orden: i + 1,
        })) ?? []
    };
}
