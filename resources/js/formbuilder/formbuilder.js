// resources/js/formbuilder/formbuilder.js
// ======================================================
// Form Builder estable (versión Alpine) — 2025
// Incluye preview() y renderPregunta() para uso directo desde Blade
// ======================================================

// ------------------------ UID -------------------------
function uid(prefix = '') {
    return prefix + Date.now().toString(36) + '-' + Math.random().toString(36).substr(2, 6);
}

// ------------------------ TIPOS -----------------------
const QUESTION_TYPES = [
    { value: 'texto_corto',       label: 'Respuesta corta' },
    { value: 'parrafo',           label: 'Párrafo' },
    { value: 'opcion_multiple',   label: 'Opción múltiple' },
    { value: 'casillas',          label: 'Casillas (checkbox)' },
    { value: 'desplegable',       label: 'Desplegable' },
    { value: 'escala_lineal',     label: 'Escala lineal' },
    { value: 'cuadricula_opciones', label: 'Cuadrícula de opciones' },
    { value: 'cuadricula_casillas', label: 'Cuadrícula casillas' },
];

// ---------------------- CREAR PREGUNTA ----------------
function createQuestion(text = "Nueva pregunta") {
    return {
        id: uid("q-"),
        tipo: 'texto_corto',
        texto: text,
        obligatoria: false,

        // escala
        escala_min: 1,
        escala_max: 5,

        // opciones
        opciones: [],

        // cuadrícula
        filas: [],
        columnas: [],
    };
}

// ----------------------- CREAR SECCIÓN ----------------
function createSection(title = "Nueva sección") {
    return {
        id: uid("s-"),
        titulo: title,
        descripcion: "",
        preguntas: [createQuestion("Pregunta 1")]
    };
}

// ---------------------- DEEP CLONE ---------------------
function deepClone(obj) {
    return JSON.parse(JSON.stringify(obj));
}

// ======================================================
// EXPORT PRINCIPAL PARA ALPINE
// ======================================================

export function formBuilder(initialSections = [], formId = null) {

    const sections = Array.isArray(initialSections) && initialSections.length
        ? deepClone(initialSections)
        : [createSection("Sección 1")];

    return {
        formId: formId ?? null,
        secciones: sections,
        seleccionado: { seccion: null, pregunta: null },

        get tipos() {
            return QUESTION_TYPES;
        },

        // ------------------------------
        // Helper: normalize question structure after type change
        // ------------------------------
        _ensureStructureForTipo(q, tipo) {
            // initialize fields to safe defaults
            q.opciones = Array.isArray(q.opciones) ? q.opciones : [];
            q.filas = Array.isArray(q.filas) ? q.filas : [];
            q.columnas = Array.isArray(q.columnas) ? q.columnas : [];
            q.escala_min = q.escala_min ?? 1;
            q.escala_max = q.escala_max ?? 5;

            if (['opcion_multiple', 'casillas', 'desplegable'].includes(tipo)) {
                if (!q.opciones.length) {
                    q.opciones = [
                        { id: uid("o-"), texto: 'Opción 1' },
                        { id: uid("o-"), texto: 'Opción 2' }
                    ];
                }
            } else {
                // not choice-like -> keep opciones empty
                q.opciones = q.opciones.length ? q.opciones : [];
            }

            if (tipo === "escala_lineal") {
                q.escala_min = q.escala_min ?? 1;
                q.escala_max = q.escala_max ?? 5;
            }

            if (['cuadricula_opciones', 'cuadricula_casillas'].includes(tipo)) {
                if (!q.filas.length) q.filas = [{ id: uid("f-"), texto: "Fila 1" }];
                if (!q.columnas.length) q.columnas = [{ id: uid("c-"), texto: "Columna 1" }];
            } else {
                // if switched away from grid, keep filas/columnas if present or clear? Keep them for data safety.
                q.filas = q.filas.length ? q.filas : [];
                q.columnas = q.columnas.length ? q.columnas : [];
            }
        },

        // ------------------------------
        // PREVIEW: texto corto resumen (usado en Blade: x-html="preview(pregunta)")
        // ------------------------------
        preview(p) {
            if (!p || !p.tipo) return "";
            switch (p.tipo) {
                case "texto_corto":
                    return "📝 Respuesta corta";
                case "parrafo":
                    return "📄 Respuesta larga";
                case "opcion_multiple":
                    return `🔘 Opción múltiple (${p.opciones?.length ?? 0} opciones)`;
                case "casillas":
                    return `☑️ Casillas (${p.opciones?.length ?? 0} opciones)`;
                case "desplegable":
                    return `⬇️ Desplegable (${p.opciones?.length ?? 0} opciones)`;
                case "escala_lineal":
                    return `📊 Escala ${p.escala_min ?? 1} – ${p.escala_max ?? 5}`;
                case "cuadricula_opciones":
                    return `🧩 Cuadrícula (radios) ${p.filas?.length ?? 0}×${p.columnas?.length ?? 0}`;
                case "cuadricula_casillas":
                    return `🧩 Cuadrícula (checks) ${p.filas?.length ?? 0}×${p.columnas?.length ?? 0}`;
                default:
                    return "Tipo no reconocido";
            }
        },

        // ------------------------------
        // renderPregunta: HTML de vista previa por pregunta (usado en Blade con x-html si quieres)
        // ------------------------------
        renderPregunta(p) {
            if (!p || !p.tipo) return "";
            // Aseguramos estructura antes de render
            this._ensureStructureForTipo(p, p.tipo);

            switch (p.tipo) {
                case 'texto_corto':
                    return `<input type="text" class="w-full border p-2 rounded" placeholder="Respuesta corta" disabled>`;
                case 'parrafo':
                    return `<textarea class="w-full border p-2 rounded" rows="3" disabled placeholder="Respuesta larga"></textarea>`;
                case 'opcion_multiple':
                    return (p.opciones || []).map(o => `
                        <label class="flex items-center gap-2">
                            <input type="radio" disabled>
                            <span>${o.texto ?? 'Opción'}</span>
                        </label>
                    `).join('');
                case 'casillas':
                    return (p.opciones || []).map(o => `
                        <label class="flex items-center gap-2">
                            <input type="checkbox" disabled>
                            <span>${o.texto ?? 'Opción'}</span>
                        </label>
                    `).join('');
                case 'desplegable':
                    return `<select class="w-full border p-2 rounded" disabled>
                        ${(p.opciones || []).map(o => `<option>${o.texto ?? 'Opción'}</option>`).join('')}
                    </select>`;
                case 'escala_lineal': {
                    const min = Number(p.escala_min ?? 1);
                    const max = Number(p.escala_max ?? 5);
                    let items = '';
                    for (let i = min; i <= max; i++) {
                        items += `<label class="flex items-center gap-2"><input type="radio" disabled> <span>${i}</span></label>`;
                    }
                    return `<div class="flex gap-2">${items}</div>`;
                }
                case 'cuadricula_opciones':
                case 'cuadricula_casillas': {
                    const filas = p.filas || [];
                    const columnas = p.columnas || [];
                    if (!filas.length || !columnas.length) {
                        return `<div class="text-sm text-gray-500">Agrega filas/columnas para ver la cuadrícula</div>`;
                    }
                    const type = p.tipo === 'cuadricula_opciones' ? 'radio' : 'checkbox';
                    let html = `<table class="w-full border-collapse text-center"><tr><th></th>${columnas.map(c => `<th class="p-1 border">${c.texto ?? ''}</th>`).join('')}</tr>`;
                    filas.forEach(f => {
                        html += `<tr><th class="text-left p-1 border">${f.texto ?? ''}</th>`;
                        columnas.forEach(() => {
                            html += `<td class="p-1 border"><input type="${type}" disabled></td>`;
                        });
                        html += `</tr>`;
                    });
                    html += `</table>`;
                    return html;
                }
                default:
                    return `<em>Tipo no soportado: ${p.tipo}</em>`;
            }
        },

        // ==================================================
        // SECCIONES
        // ==================================================
        addSection() {
            const n = this.secciones.length + 1;
            this.secciones.push(createSection(`Sección ${n}`));
        },

        removeSection(index) {
            if (this.secciones.length === 1) {
                alert("Debe haber al menos una sección.");
                return;
            }
            this.secciones.splice(index, 1);
            this.seleccionado = { seccion: null, pregunta: null };
        },

        selectSection(index) {
            this.seleccionado = { seccion: index, pregunta: null };
        },

        // ==================================================
        // PREGUNTAS
        // ==================================================
        addPregunta(secIndex) {
            const sec = this.secciones[secIndex];
            const q = createQuestion(`Pregunta ${sec.preguntas.length + 1}`);
            sec.preguntas.push(q);
            this.selectPregunta(secIndex, sec.preguntas.length - 1);
        },

        selectPregunta(secIndex, pregIndex) {
            this.seleccionado = { seccion: secIndex, pregunta: pregIndex };
        },

        duplicatePregunta(secIndex, pregIndex) {
            const sec = this.secciones[secIndex];
            const original = sec.preguntas[pregIndex];
            if (!original) return;

            const clone = deepClone(original);
            clone.id = uid("q-");

            if (clone.opciones?.length) {
                clone.opciones = clone.opciones.map(o => ({ ...o, id: uid("o-") }));
            }
            if (clone.filas?.length) {
                clone.filas = clone.filas.map(f => ({ ...f, id: uid("f-") }));
            }
            if (clone.columnas?.length) {
                clone.columnas = clone.columnas.map(c => ({ ...c, id: uid("c-") }));
            }

            sec.preguntas.splice(pregIndex + 1, 0, clone);
            this.selectPregunta(secIndex, pregIndex + 1);

            // Forzar rebind visual del select en Alpine
            if (typeof this.$nextTick === 'function') {
                this.$nextTick(() => {
                    const q = sec.preguntas[pregIndex + 1];
                    q.tipo = q.tipo;
                });
            } else {
                setTimeout(() => {
                    const q = sec.preguntas[pregIndex + 1];
                    if (q) q.tipo = q.tipo;
                }, 0);
            }
        },

        removePregunta(secIndex, pregIndex) {
            this.secciones[secIndex].preguntas.splice(pregIndex, 1);
            this.seleccionado.pregunta = null;
        },

        // ==================================================
        // OPCIONES
        // ==================================================
        isChoice(q) {
            return ['opcion_multiple', 'casillas', 'desplegable'].includes(q.tipo);
        },

        addOption(secIndex, pregIndex) {
            const q = this.secciones[secIndex].preguntas[pregIndex];
            if (!q.opciones) q.opciones = [];
            q.opciones.push({ id: uid("o-"), texto: `Opción ${q.opciones.length + 1}` });
        },

        removeOption(secIndex, pregIndex, optIndex) {
            const q = this.secciones[secIndex].preguntas[pregIndex];
            if (!q.opciones) return;
            q.opciones.splice(optIndex, 1);
        },

        // ==================================================
        // CAMBIO DE TIPO
        // ==================================================
        changeTipo(secIndex, pregIndex, tipo) {
            const q = this.secciones[secIndex].preguntas[pregIndex];
            if (!q) return;

            q.tipo = tipo;

            // Reinicializamos estructura acorde al tipo (esto evita inconsistencias)
            q.opciones = q.opciones ?? [];
            q.filas = q.filas ?? [];
            q.columnas = q.columnas ?? [];
            q.escala_min = q.escala_min ?? 1;
            q.escala_max = q.escala_max ?? 5;

            // Acomodar valores según tipo
            if (['opcion_multiple', 'casillas', 'desplegable'].includes(tipo)) {
                if (!q.opciones.length) {
                    q.opciones = [
                        { id: uid("o-"), texto: 'Opción 1' },
                        { id: uid("o-"), texto: 'Opción 2' }
                    ];
                }
            } else {
                // si no es tipo de opciones, mantenemos array vacío
                q.opciones = q.opciones.length ? q.opciones : [];
            }

            if (tipo === "escala_lineal") {
                q.escala_min = q.escala_min ?? 1;
                q.escala_max = q.escala_max ?? 5;
            }

            if (['cuadricula_opciones', 'cuadricula_casillas'].includes(tipo)) {
                if (!q.filas.length) q.filas = [{ id: uid("f-"), texto: "Fila 1" }];
                if (!q.columnas.length) q.columnas = [{ id: uid("c-"), texto: "Columna 1" }];
            } else {
                q.filas = q.filas.length ? q.filas : [];
                q.columnas = q.columnas.length ? q.columnas : [];
            }

            // Forzamos re-render visual si es necesario
            if (typeof this.$nextTick === 'function') {
                this.$nextTick(() => {
                    // no-op: setting tipo ensures binding
                    q.tipo = tipo;
                });
            }
        },

        // ==================================================
        // GUARDAR
        // ==================================================
        async guardar() {
            if (!this.formId) return alert("No hay formId definido.");

            const estructura = this.secciones.map((sec, si) => ({
                titulo: sec.titulo,
                descripcion: sec.descripcion,
                orden: si + 1,
                preguntas: sec.preguntas.map((p, pi) => ({
                    tipo: p.tipo,
                    texto: p.texto,
                    obligatorio: p.obligatoria ? 1 : 0,
                    orden: pi + 1,
                    escala_min: p.escala_min,
                    escala_max: p.escala_max,
                    filas: p.filas ?? [],
                    columnas: p.columnas ?? [],
                    opciones: (p.opciones ?? []).map((o, oi) => ({
                        texto: o.texto,
                        orden: oi + 1
                    }))
                }))
            }));

            try {
                const resp = await fetch(`/formularios/${this.formId}/estructura`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ estructura })
                });

                if (!resp.ok) throw new Error(await resp.text());
                alert("Guardado correctamente.");
            } catch (err) {
                alert("Error al guardar: " + err.message);
            }
        }
    };
}
