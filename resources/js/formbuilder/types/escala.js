// resources/js/formbuilder/types/escala.js

export function createScaleQuestion(texto = "Nueva pregunta") {
    return {
        tipo: "escala_lineal",   // Corresponde a la escala lineal en la tabla preguntas
        texto: texto,
        obligatorio: false,
        orden: 0,
        escala_min: 1,
        escala_max: 5,
        opciones: [],            // Las opciones se generan dinámicamente según el rango
        filas: [],
        columnas: []
    };
}
