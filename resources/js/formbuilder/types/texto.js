// resources/js/formbuilder/types/texto.js

export function createTextoQuestion(texto = "Nueva pregunta") {
    return {
        tipo: "texto_corto",  // Corresponde al tipo texto corto en la tabla preguntas
        texto: texto,
        obligatorio: false,
        orden: 0,
        opciones: [],          // No aplica para texto corto
        escala_min: null,
        escala_max: null,
        filas: [],
        columnas: []
    };
}
