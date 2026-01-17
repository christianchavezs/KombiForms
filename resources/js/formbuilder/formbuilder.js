// resources/js/formbuilder/formbuilder.js
// ======================================================
// Form Builder estable (versión Alpine) — 2025
// Incluye preview() y renderPregunta() para uso directo desde Blade
// ======================================================

import * as cuadriculaAux from './cuadriculaAux.js';


// ------------------------ UID -------------------------
function uid(prefix = "") {
  return prefix + Date.now().toString(36) + "-" + Math.random().toString(36).substr(2, 6)
}

// ------------------------ TIPOS -----------------------
const QUESTION_TYPES = [
  { value: "titulo", label: "Título" },
  { value: "texto", label: "Texto descriptivo" },

  { value: "texto_corto", label: "Respuesta corta" },
  { value: "parrafo", label: "Párrafo" },
  { value: "opcion_multiple", label: "Opción múltiple" },
  { value: "casillas", label: "Casillas (checkbox)" },
  { value: "desplegable", label: "Desplegable" },
  { value: "escala_lineal", label: "Escala lineal" },
  { value: "cuadricula_opciones", label: "Cuadrícula de opciones" },
  { value: "cuadricula_casillas", label: "Cuadrícula casillas" },

  { value: "fecha", label: "Fecha" },
  { value: "hora", label: "Hora" },
  { value: "fecha_hora", label: "Fecha y hora" },
]

function createQuestion(text = "Nuevo elemento", tipo = "texto_corto") {
  return {
    id: uid("q-"),
    tipo,
    texto: text ?? " ",
    obligatorio: 0,

    escala_min: 1,
    escala_max: 5,

    opciones: [],
    filas: [],
    columnas: [],
  }
}

// ----------------------- CREAR SECCIÓN ----------------
function createSection(title = "Nueva sección") {
  return {
    id: uid("s-"),
    titulo: title,
    descripcion: "",
    preguntas: [createQuestion("Pregunta 1")],
  }
}

// ---------------------- DEEP CLONE ---------------------
function deepClone(obj) {
  return JSON.parse(JSON.stringify(obj))
}

function normalizarSeccion(sec) {
  return {
    id: sec.id ?? uid("s-"),
    titulo: sec.titulo ?? "Sección",
    descripcion: sec.descripcion ?? "",
    orden: sec.orden ?? 1,

    preguntas: (sec.preguntas || []).map((p) => ({
      id: p.id ?? uid("p-"),
      tipo: p.tipo ?? "texto_corto",
      texto: p.texto ?? "",
   
      obligatorio: Number(p.obligatorio) === 1 ? 1 : 0,
      
      orden: p.orden ?? 1,
      escala_min: p.escala_min ?? 1,
      escala_max: p.escala_max ?? 5,

      filas: (p.filas || []).map((f) => ({
        id: f.id ?? uid("f-"),
        texto: f.texto ?? "",
      })),

      columnas: (p.columnas || []).map((c) => ({
        id: c.id ?? uid("c-"),
        texto: c.texto ?? "",
      })),

      opciones: (p.opciones || []).map((o) => ({
        id: o.id ?? uid("o-"),
        texto: o.texto ?? "",
        fila: o.fila ?? null,
        columna: o.columna ?? null,
      })),
    })),
  }
}

// ======================================================
// EXPORT PRINCIPAL PARA ALPINE
// ======================================================

export function formBuilder(initialSections = [], formId = null) {
 
 if (!Array.isArray(initialSections) || initialSections.length === 0) {
  initialSections = [createSection("Sección 1")];
  }
  
  const secciones =
    Array.isArray(initialSections) && initialSections.length
      ? initialSections.map(normalizarSeccion)
      : [createSection("Sección 1")]



  return {
    formId: formId ?? null,
    secciones: secciones, // <- nombre correcto

    // Variables que controlan el menú lateral
    menuColapsado: false,
    mostrarModalTipos: false,
    confirmarEliminarSeccion: null,

    seleccionado: { seccion: null, pregunta: null },


    modalCambio: { seccion: null, pregunta: null },

    abrirModalCambiarTipo(sIndex, pIndex) {
      this.modalCambio.seccion = sIndex
      this.modalCambio.pregunta = pIndex
      this.mostrarModalTipos = true // reutilizamos el mismo modal
    },

    confirmarCambioTipo(tipo) {
      if (this.modalCambio.seccion === null || this.modalCambio.pregunta === null) return

      this.changeTipo(this.modalCambio.seccion, this.modalCambio.pregunta, tipo)

      // reiniciar estado del modal
      this.modalCambio = { seccion: null, pregunta: null }
      this.mostrarModalTipos = false
    },

    abrirModalTipos() {
      if (this.seleccionado.seccion === null) {
        alert("Primero selecciona una sección.")
        return
      }
      this.mostrarModalTipos = true
    },

    cerrarModalTipos() {
      this.mostrarModalTipos = false
    },

    addPreguntaConTipo(tipo) {
      const secIndex = this.seleccionado.seccion
      if (secIndex === null) return

      const sec = this.secciones[secIndex]

      let textoDefault = ""
      if (tipo === "titulo") {
        textoDefault = "Nuevo título"
      } else if (tipo === "texto") {
        textoDefault = "Nuevo texto descriptivo"
      } else {
        textoDefault = "Nueva pregunta"
      }

      // Crear pregunta
      const q = {
        id: uid("q-"),
        texto: textoDefault, // Use default text instead of empty string
        tipo: tipo,
        obligatorio: 0,
        opciones: [],
        filas: [],
        columnas: [],
        escala_min: 1,
        escala_max: 5,
      }

      // Asegurar estructura según el tipo
      this._ensureStructureForTipo(q, tipo)

      // Agregar a la sección
      sec.preguntas.push(q)

      // Seleccionar la pregunta recién creada
      this.seleccionado.pregunta = sec.preguntas.length - 1

      // Cerrar modal
      this.mostrarModalTipos = false
    },

    get tipos() {
      return QUESTION_TYPES
    },

    _ensureStructureForTipo(q, tipo) {
      // inicializar campos con valores seguros
      q.opciones = Array.isArray(q.opciones) ? q.opciones : []
      q.filas = Array.isArray(q.filas) ? q.filas : []
      q.columnas = Array.isArray(q.columnas) ? q.columnas : []
      q.escala_min = q.escala_min ?? 1
      q.escala_max = q.escala_max ?? 5

      // Tipos de opción múltiple
      if (["opcion_multiple", "casillas", "desplegable"].includes(tipo)) {
        if (!q.opciones.length) {
          q.opciones = [
            { id: uid("o-"), texto: "Opción 1", fila: null, columna: null },
            { id: uid("o-"), texto: "Opción 2", fila: null, columna: null },
          ]
        } else {
          q.opciones = q.opciones.map(o => ({
            ...o,
            id: o.id ?? uid("o-")
          }))
        }
      } else {
        q.opciones = q.opciones.length ? q.opciones.map(o => ({
          ...o,
          id: o.id ?? uid("o-")
        })) : []
      }

      // Escala lineal
      if (tipo === "escala_lineal") {
        q.escala_min = q.escala_min ?? 1
        q.escala_max = q.escala_max ?? 5
      }

      // Cuadrículas
      if (["cuadricula_opciones", "cuadricula_casillas"].includes(tipo)) {
        if (!q.filas.length) {
          q.filas = [{ id: uid("f-"), texto: "Fila 1" }]
        } else {
          q.filas = q.filas.map(f => ({
            ...f,
            id: f.id ?? uid("f-")
          }))
        }

        if (!q.columnas.length) {
          q.columnas = [{ id: uid("c-"), texto: "Columna 1" }]
        } else {
          q.columnas = q.columnas.map(c => ({
            ...c,
            id: c.id ?? uid("c-")
          }))
        }
      } else {
        q.filas = q.filas.length ? q.filas.map(f => ({
          ...f,
          id: f.id ?? uid("f-")
        })) : []
        q.columnas = q.columnas.length ? q.columnas.map(c => ({
          ...c,
          id: c.id ?? uid("c-")
        })) : []
      }
    },

    // ------------------------------
    // PREVIEW: texto corto resumen (usado en Blade: x-html="preview(pregunta)")
    // ------------------------------
    preview(p) {
      if (!p || !p.tipo) return ""
      switch (p.tipo) {
        case "titulo":
          return "🅣 Título"
        case "texto":
          return "🅣 Texto descriptivo"

        case "texto_corto":
          return "📝 Respuesta corta"
        case "parrafo":
          return "📄 Respuesta larga"
        case "opcion_multiple":
          return `🔘 Opción múltiple (${p.opciones?.length ?? 0} opciones)`
        case "casillas":
          return `☑️ Casillas (${p.opciones?.length ?? 0} opciones)`
        case "desplegable":
          return `⬇️ Desplegable (${p.opciones?.length ?? 0} opciones)`
        case "escala_lineal":
          return `📊 Escala ${p.escala_min ?? 1} – ${p.escala_max ?? 5}`
        case "cuadricula_opciones":
          return `🧩 Cuadrícula (radios) ${p.filas?.length ?? 0}×${p.columnas?.length ?? 0}`
        case "cuadricula_casillas":
          return `🧩 Cuadrícula (checks) ${p.filas?.length ?? 0}×${p.columnas?.length ?? 0}`
        default:
          return "Tipo no reconocido"
      }
    },

    // ------------------------------
    // renderPregunta: HTML de vista previa por pregunta (usado en Blade con x-html si quieres)
    // ------------------------------
    renderPregunta(p) {
      if (!p || !p.tipo) return ""
      // Aseguramos estructura antes de render
      this._ensureStructureForTipo(p, p.tipo)

      switch (p.tipo) {
        case "titulo":
          return `
                        <h2 class="text-2xl font-bold text-gray-800">
                            ${p.texto || "Título"}
                        </h2>
                    `

        case "texto":
          return `
                        <p class="text-gray-700 whitespace-pre-line">
                            ${p.texto || "Texto descriptivo"}
                        </p>
                    `

        case "texto_corto":
          return `<input type="text" class="w-full border p-2 rounded" placeholder="Respuesta corta" disabled>`
        case "parrafo":
          return `<textarea class="w-full border p-2 rounded" rows="3" disabled placeholder="Respuesta larga"></textarea>`
        case "opcion_multiple":
          return (p.opciones || [])
            .map(
              (o) => `
                        <label class="flex items-center gap-2">
                            <input type="radio" disabled>
                            <span>${o.texto ?? "Opción"}</span>
                        </label>
                    `,
            )
            .join("")
        case "casillas":
          return (p.opciones || [])
            .map(
              (o) => `
                        <label class="flex items-center gap-2">
                            <input type="checkbox" disabled>
                            <span>${o.texto ?? "Opción"}</span>
                        </label>
                    `,
            )
            .join("")
        case "desplegable":
          return `<select class="w-full border p-2 rounded" disabled>
                        ${(p.opciones || []).map((o) => `<option>${o.texto ?? "Opción"}</option>`).join("")}
                    </select>`
        case "escala_lineal": {
          const min = Number(p.escala_min ?? 1)
          const max = Number(p.escala_max ?? 5)
          const labelMin = p.etiqueta_min ?? ""
          const labelMax = p.etiqueta_max ?? ""

          const total = max - min + 1

          let etiquetas = ""
          let radios = ""
          let numeros = ""

          for (let i = min; i <= max; i++) {
            radios += `
                            <div class="flex-1 text-center">
                                <input type="radio" disabled>
                            </div>
                        `

            numeros += `
                            <div class="flex-1 text-center text-xs text-gray-600">
                                ${i}
                            </div>
                        `
          }

          // Etiquetas SOLO en extremos
          etiquetas = `
                        <div class="flex">
                            <div class="flex-1 text-left text-xs text-gray-600">
                                ${labelMin}
                            </div>
                            <div class="flex-1 text-right text-xs text-gray-600">
                                ${labelMax}
                            </div>
                        </div>
                    `

          return `
                        <div class="space-y-2">
                            ${etiquetas}
                            <div class="flex gap-2">${radios}</div>
                            <div class="flex gap-2">${numeros}</div>
                        </div>
                    `
        }

        case "cuadricula_opciones":
        case "cuadricula_casillas": {
          const filas = p.filas || []
          const columnas = p.columnas || []

          if (!filas.length || !columnas.length) {
            return `<div class="text-sm text-gray-500">
                            Agrega filas/columnas para ver la cuadrícula
                        </div>`
          }

          const type = p.tipo === "cuadricula_opciones" ? "radio" : "checkbox"

          let html = `
                        <table class="w-full border-collapse text-center text-sm">
                            <thead>
                                <tr>
                                    <th class="border p-2"></th>
                                    ${columnas
                                      .map(
                                        (c) => `
                                        <th class="border p-2 font-medium">
                                            ${c.texto ?? ""}
                                        </th>
                                    `,
                                      )
                                      .join("")}
                                </tr>
                            </thead>
                            <tbody>
                    `

          filas.forEach((f, fIndex) => {
            html += `
                            <tr>
                                <th class="border p-2 text-left font-medium">
                                    ${f.texto ?? ""}
                                </th>
                                ${columnas
                                  .map(
                                    (_, cIndex) => `
                                    <td class="border p-2">
                                        <input
                                            type="${type}"
                                            ${p.tipo === "cuadricula_opciones" ? `name="fila_${p.id}_${fIndex}"` : ""}
                                            disabled
                                        >

                                    </td>
                                `,
                                  )
                                  .join("")}
                            </tr>
                        `
          })

          html += `
                    
                            </tbody>
                        </table>
                    `

          return html
        }

        default:
          return `<em>Tipo no soportado: ${p.tipo}</em>`
      }
    },

    // ==================================================
    // SECCIONES
    // ==================================================
    addSection() {
      const n = this.secciones.length + 1
      this.secciones.push(createSection(`Sección ${n}`))
    },

    removeSection(index) {
      if (this.secciones.length === 1) {
        alert("Debe haber al menos una sección.")
        return
      }
      this.secciones.splice(index, 1)
      this.seleccionado = { seccion: null, pregunta: null }
    },

    selectSection(index) {
      this.seleccionado = { seccion: index, pregunta: null }
    },

    // ==================================================
    // PREGUNTAS
    // ==================================================
    addPregunta(secIndex) {
      const sec = this.secciones[secIndex]
      const q = createQuestion(`Pregunta ${sec.preguntas.length + 1}`)
      sec.preguntas.push(q)
      this.selectPregunta(secIndex, sec.preguntas.length - 1)
    },

    selectPregunta(secIndex, pregIndex) {
      this.seleccionado = { seccion: secIndex, pregunta: pregIndex }
    },

    duplicatePregunta(secIndex, pregIndex) {
      const sec = this.secciones[secIndex]
      const original = sec.preguntas[pregIndex]
      if (!original) return

      const clone = deepClone(original)
      clone.id = uid("q-")

      if (clone.opciones?.length) {
        clone.opciones = clone.opciones.map((o) => ({ ...o, id: uid("o-") }))
      }
      if (clone.filas?.length) {
        clone.filas = clone.filas.map((f) => ({ ...f, id: uid("f-") }))
      }
      if (clone.columnas?.length) {
        clone.columnas = clone.columnas.map((c) => ({ ...c, id: uid("c-") }))
      }

      sec.preguntas.splice(pregIndex + 1, 0, clone)
      this.selectPregunta(secIndex, pregIndex + 1)

      // Forzar rebind visual del select en Alpine
      if (typeof this.$nextTick === "function") {
        this.$nextTick(() => {
          const q = sec.preguntas[pregIndex + 1]
          q.tipo = q.tipo
        })
      } else {
        setTimeout(() => {
          const q = sec.preguntas[pregIndex + 1]
          if (q) q.tipo = q.tipo
        }, 0)
      }
    },

    removePregunta(secIndex, pregIndex) {
      this.secciones[secIndex].preguntas.splice(pregIndex, 1)
      this.seleccionado.pregunta = null
    },

    // ==================================================
    // OPCIONES
    // ==================================================
    isChoice(q) {
      return ["opcion_multiple", "casillas", "desplegable"].includes(q.tipo)
    },

    addOption(secIndex, pregIndex) {
      const q = this.secciones[secIndex].preguntas[pregIndex]
      if (!q.opciones) q.opciones = []
      q.opciones.push({
        id: uid("o-"),
        texto: `Opción ${q.opciones.length + 1}`,
        fila: null,
        columna: null,
      })
    },

    removeOption(secIndex, pregIndex, optIndex) {
      const q = this.secciones[secIndex].preguntas[pregIndex]
      if (!q || !Array.isArray(q.opciones)) return

      // No permitir eliminar la última opción
      if (q.opciones.length <= 1) {
        alert("Debe existir al menos una opción en la pregunta.")
        return
      }

      q.opciones.splice(optIndex, 1)
    },

    // ==================================================
    // CAMBIO DE TIPO
    // ==================================================
    changeTipo(secIndex, pregIndex, tipo) {
      const q = this.secciones[secIndex].preguntas[pregIndex]
      if (!q) return

      q.tipo = tipo

      // Reinicializamos estructura acorde al tipo (esto evita inconsistencias)
      q.opciones = q.opciones ?? []
      q.filas = q.filas ?? []
      q.columnas = q.columnas ?? []
      q.escala_min = q.escala_min ?? 1
      q.escala_max = q.escala_max ?? 5

      // Acomodar valores según tipo
      if (["opcion_multiple", "casillas", "desplegable"].includes(tipo)) {
        if (!q.opciones.length) {
          q.opciones = [
            { id: uid("o-"), texto: "Opción 1", fila: null, columna: null },
            { id: uid("o-"), texto: "Opción 2", fila: null, columna: null },
          ]
        }
      } else {
        // si no es tipo de opciones, mantenemos array vacío
        q.opciones = q.opciones.length ? q.opciones : []
      }

      if (tipo === "escala_lineal") {
        q.escala_min = q.escala_min ?? 1
        q.escala_max = q.escala_max ?? 5
      }

      if (["cuadricula_opciones", "cuadricula_casillas"].includes(tipo)) {
        if (!q.filas.length) q.filas = [{ id: uid("f-"), texto: "Fila 1" }]
        if (!q.columnas.length) q.columnas = [{ id: uid("c-"), texto: "Columna 1" }]
      } else {
        q.filas = q.filas.length ? q.filas : []
        q.columnas = q.columnas.length ? q.columnas : []
      }

      // Forzamos re-render visual si es necesario
      if (typeof this.$nextTick === "function") {
        this.$nextTick(() => {
          // no-op: setting tipo ensures binding
          q.tipo = tipo
        })
      }

      // Títulos y textos nunca pueden ser obligatorios
      if (["titulo", "texto"].includes(tipo)) {
        q.obligatorio = false
        q.texto = q.texto ?? " " //Evita que textos sean null en la BD
      }
    },

    // ==================================================
    // FILAS / COLUMNAS (CUADRÍCULAS)
    // ==================================================
    addFila(secIndex, pregIndex) {
      const q = this.secciones[secIndex].preguntas[pregIndex]
      if (!q) return

      if (!Array.isArray(q.filas)) q.filas = []

      q.filas.push({
        id: uid("f-"),
        texto: `Fila ${q.filas.length + 1}`,
      })
    },

    removeFila(secIndex, pregIndex, filaIndex) {
      const q = this.secciones[secIndex].preguntas[pregIndex]
      if (!q || !Array.isArray(q.filas)) return

      if (q.filas.length <= 1) {
        alert("Debe existir al menos una fila en la pregunta.")
        return
      }

      q.filas.splice(filaIndex, 1)
    },

    addColumna(secIndex, pregIndex) {
      const q = this.secciones[secIndex].preguntas[pregIndex]
      if (!q) return

      if (!Array.isArray(q.columnas)) q.columnas = []

      q.columnas.push({
        id: uid("c-"),
        texto: `Columna ${q.columnas.length + 1}`,
      })
    },

    removeColumna(secIndex, pregIndex, colIndex) {
      const q = this.secciones[secIndex].preguntas[pregIndex]
      if (!q || !Array.isArray(q.columnas)) return

      if (q.columnas.length <= 1) {
        alert("Debe existir al menos una columna en la pregunta.")
        return
      }

      q.columnas.splice(colIndex, 1)
    },
    



    async guardar() {
      if (!this.formId) {
        alert("No hay formId definido.");
        return;
      }

      //  Obtener estructura final usando tu función ya corregida
      const estructura = this.getEstructura();

      console.log(" Enviando al backend:", JSON.stringify({
        estructura: estructura
        }, null, 2));


      try {

        const resp = await fetch(`/formularios/${this.formId}/estructura`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
          },

          //  IMPORTANTE: mandar solo { estructura }
          body: JSON.stringify({
            estructura: estructura
          }),
        });

        // Validar error
        if (!resp.ok) {
          const errorTxt = await resp.text();
          throw new Error(errorTxt);
        }

        alert("Guardado correctamente.");

      } catch (err) {
        console.error("Error al guardar estructura:", err);
        alert("Error al guardar: " + err.message);
      }
    },




    getEstructura() {
  return this.secciones.map((sec, si) => ({
    titulo: sec.titulo?.trim() || "Sección sin título",
    descripcion: sec.descripcion?.trim() || "",
    orden: si + 1,

    preguntas: sec.preguntas.map((p, pi) => {

      // ======================================================
      //  1) SINCRONIZAR CUADRÍCULA Y LOG DE ESTRUCTURA COMPLETA
      // ======================================================
      if (["cuadricula_opciones", "cuadricula_casillas"].includes(p.tipo)) {
          cuadriculaAux.ensureCuadriculaStructure(p);

          console.log(
            " Datos de cuadrícula después de ensureCuadriculaStructure:",
            JSON.parse(JSON.stringify(p))
          );
      }

      // ======================================================
      //  2) GENERAR OBJETO FINAL DE LA PREGUNTA
      // ======================================================
      const preguntaFinal = {
        tipo: p.tipo,
        texto: (p.texto?.trim() || "Pregunta sin título"),
        obligatorio: ["titulo", "texto"].includes(p.tipo) ? 0 : p.obligatorio ? 1 : 0,
        orden: pi + 1,

        escala_min: p.tipo === "escala_lineal" ? (p.escala_min ?? 1) : null,
        escala_max: p.tipo === "escala_lineal" ? (p.escala_max ?? 5) : null,

        filas: ["cuadricula_opciones", "cuadricula_casillas"].includes(p.tipo)
          ? p.filas.map((f, fi) => ({
              texto: f.texto?.trim() || `Fila ${fi + 1}`,
              fila: fi + 1
            }))
          : [],

        columnas: ["cuadricula_opciones", "cuadricula_casillas"].includes(p.tipo)
          ? p.columnas.map((c, ci) => ({
              texto: c.texto?.trim() || `Columna ${ci + 1}`,
              columna: ci + 1
            }))
          : [],

        opciones: ["opcion_multiple", "casillas", "desplegable"].includes(p.tipo)
          ? p.opciones.map((o, oi) => ({
              texto: o.texto?.trim() || `Opción ${oi + 1}`,
              orden: oi + 1,
              fila: null,
              columna: null
            }))
          : [],

        opciones_cuadricula: ["cuadricula_opciones", "cuadricula_casillas"].includes(p.tipo)
          ? cuadriculaAux.updateCuadriculaOpciones(p)
          : []
      };

      // ======================================================
      //  3) LOG DE LA PREGUNTA FINAL QUE SE ENVÍA AL BACKEND
      // ======================================================
      console.log(
        " Pregunta final generada:",
        JSON.parse(JSON.stringify(preguntaFinal))

      
      
      );

      return preguntaFinal;
    })
  }));
},



    generarOpcionesCuadricula(p) {
      const opciones = []
      ;(p.filas ?? []).forEach((f, fi) => {
        ;(p.columnas ?? []).forEach((c, ci) => {
          opciones.push({
            texto: `${f.texto} - ${c.texto}`,
            orden: opciones.length + 1,
            fila: fi + 1,
            columna: ci + 1,
          })
        })
      })

      return opciones
    },
  }
}

window.formBuilder = formBuilder
