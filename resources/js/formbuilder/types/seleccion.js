// resources/js/formbuilder/types/seleccion.js

export function createSeleccionQuestion(texto = "Nueva pregunta") {
    return {
        tipo: "opcion_multiple",  // Corresponde al tipo opción múltiple en la tabla preguntas
        texto: texto,
        obligatorio: false,
        orden: 0,
        opciones: [
            { texto: "Opción 1" },
            { texto: "Opción 2" }
        ],
        escala_min: null,
        escala_max: null,
        filas: [],
        columnas: []
    };
}
