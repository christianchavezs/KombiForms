/**
 * ==========================================================
 *  MÓDULO AUXILIAR PARA MANEJO DE CUADRÍCULAS DE PREGUNTAS
 * ==========================================================
 *  Este módulo centraliza TODA la lógica necesaria para:
 *
 *  ✔ mantener filas y columnas sincronizadas
 *  ✔ generar la matriz interna de opciones
 *  ✔ aplanar la matriz en una lista para enviar a Laravel
 *  ✔ agregar/eliminar filas y columnas correctamente
 *  ✔ actualizar fila/columna de cada opción al reordenar
 *
 *  Funciona para:
 *  - cuadricula_opciones (tipo radio)
 *  - cuadricula_casillas (tipo checkbox)
 *
 * ==========================================================
 */



/**
 * Asegura que la estructura base de la cuadrícula exista:
 *  - filas[]
 *  - columnas[]
 *  - opciones[fila][columna]
 */
export function ensureCuadriculaStructure(p) {

    if (!Array.isArray(p.filas)) p.filas = []
    if (!Array.isArray(p.columnas)) p.columnas = []

    if (!Array.isArray(p.opciones)) p.opciones = []

    for (let fi = 0; fi < p.filas.length; fi++) {

        if (!Array.isArray(p.opciones[fi])) {
            p.opciones[fi] = []
        }

        for (let ci = 0; ci < p.columnas.length; ci++) {

            if (!p.opciones[fi][ci]) {
                p.opciones[fi][ci] = {
                    texto: "",
                    fila: fi + 1,
                    columna: ci + 1
                }
            }
        }
    }

    return p
}



/**
 * Convierte la matriz de opciones en un arreglo plano
 * que Laravel espera como "opciones_cuadricula".
 */
export function updateCuadriculaOpciones(p) {

    const opciones = []

    for (let fi = 0; fi < p.filas.length; fi++) {
        for (let ci = 0; ci < p.columnas.length; ci++) {

            const op = p.opciones?.[fi]?.[ci]

            opciones.push({
                texto: op?.texto ?? "",
                fila: fi + 1,
                columna: ci + 1,
            })
        }
    }

    return opciones
}



/**
 * Actualiza los números de fila después de cambios
 */
export function syncFilaCambio(p) {
    p.opciones.forEach((colArray, fi) => {
        colArray.forEach((op) => {
            op.fila = fi + 1
        })
    })
}



/**
 * Actualiza los números de columna después de cambios
 */
export function syncColumnaCambio(p) {
    p.opciones.forEach((colArray) => {
        colArray.forEach((op, ci) => {
            op.columna = ci + 1
        })
    })
}



/**
 * Agrega una fila con sus opciones
 */
export function addFilaCuadricula(p) {

    // agregar fila
    p.filas.push({ texto: `Fila ${p.filas.length + 1}` })

    const fi = p.filas.length - 1

    // crear la fila en opciones si no existe
    p.opciones[fi] = []

    // agregar cada celda de la fila
    p.columnas.forEach((col, ci) => {
        p.opciones[fi][ci] = {
            texto: "",
            fila: fi + 1,
            columna: ci + 1
        }
    })
}



/**
 * Agrega una columna con sus opciones
 */
export function addColumnaCuadricula(p) {

    p.columnas.push({ texto: `Columna ${p.columnas.length + 1}` })

    const ci = p.columnas.length - 1

    // para cada fila, agregar la opción en la nueva columna
    p.filas.forEach((fila, fi) => {
        if (!Array.isArray(p.opciones[fi])) p.opciones[fi] = []

        p.opciones[fi][ci] = {
            texto: "",
            fila: fi + 1,
            columna: ci + 1
        }
    })
}



/**
 * Elimina una fila de la cuadrícula
 */
export function removeFilaCuadricula(p, index) {

    if (p.filas.length <= 1) return

    p.filas.splice(index, 1)
    p.opciones.splice(index, 1)

    syncFilaCambio(p)
}



/**
 * Elimina una columna de la cuadrícula
 */
export function removeColumnaCuadricula(p, index) {

    if (p.columnas.length <= 1) return

    p.columnas.splice(index, 1)

    // eliminar esa columna de cada fila en opciones
    p.opciones.forEach((filaOpciones) => {
        filaOpciones.splice(index, 1)
    })

    syncColumnaCambio(p)
}


/**
 * Genera opciones planas desde la matriz p.opciones
 * para enviarlas a Laravel como opciones_cuadricula[]
 */
export function generarOpcionesCuadricula(p) {

    const opciones = [];

    for (let fi = 0; fi < p.filas.length; fi++) {
        for (let ci = 0; ci < p.columnas.length; ci++) {

            const op = p.opciones?.[fi]?.[ci] ?? { texto: "" };

            opciones.push({
                texto: op.texto ?? "",
                fila: fi + 1,
                columna: ci + 1
            });
        }
    }

    return opciones;
}

