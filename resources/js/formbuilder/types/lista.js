// resources/js/formbuilder/types/lista.js

export function createListaQuestion(texto = "Nueva pregunta") {
    return {
        tipo: "desplegable",   // Corresponde al tipo desplegable en la tabla preguntas
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
