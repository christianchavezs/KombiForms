// resources/js/formbuilder/types/cuadricula.js

export function createGridQuestion(texto = "Nueva pregunta") {
    return {
        tipo: "cuadricula_unica", // Para opción única en cuadrícula
        texto: texto,
        obligatorio: false,
        orden: 0,
        opciones: [],  // Se generarán según filas y columnas
        filas: ["Fila 1"],
        columnas: ["Columna 1", "Columna 2"],
        escala_min: null,
        escala_max: null
    };
}
