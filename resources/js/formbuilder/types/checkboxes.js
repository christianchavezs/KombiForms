// resources/js/formbuilder/types/checkboxes.js

export function createCheckboxQuestion(texto = "Nueva pregunta") {
    return {
        tipo: "varias_opciones", // coincide con BD
        texto: texto,
        obligatorio: false,
        orden: 0,
        opciones: [
            { texto: "Opción 1" },
            { texto: "Opción 2" }
        ],
        // Campos adicionales para cuadricula si se implementa
        filas: [],
        columnas: [],
        escala_min: null,
        escala_max: null
    };
}
