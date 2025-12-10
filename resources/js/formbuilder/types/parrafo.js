// resources/js/formbuilder/types/parrafo.js

export function createParrafoQuestion(texto = "Nueva pregunta") {
    return {
        tipo: "texto_largo",  // Corresponde al tipo párrafo en la tabla preguntas
        texto: texto,
        obligatorio: false,
        orden: 0,
        opciones: [],          // No aplica para párrafo
        escala_min: null,
        escala_max: null,
        filas: [],
        columnas: []
    };
}
