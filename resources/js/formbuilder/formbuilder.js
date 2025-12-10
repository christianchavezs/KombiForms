// resources/js/formbuilder/formbuilder.js
// =====================================
// Form builder autónomo (Google Forms style)
// Evita dependencias frágiles de imports externos.
// =====================================

function uid(prefix = '') {
    return prefix + Date.now().toString(36) + '-' + Math.random().toString(36).substr(2,5);
}

// ---------- helpers para crear estructura inicial ----------
function createQuestion(text = 'Nueva pregunta') {
    return {
        id: uid('q-'),
        tipo: 'texto_corto', // valores usados en tus vistas: texto_corto, parrafo, opcion_unica, varias_opciones, desplegable, escala_lineal, cuadricula_unica, cuadricula_multiple, fecha, hora
        texto: text,
        obligatoria: false,
        escala_min: 1,
        escala_max: 5,
        opciones: [],   // { id, texto, fila?, columna? }
        filas: [],
        columnas: []
    };
}

function createSection(title = 'Nueva sección') {
    return {
        id: uid('s-'),
        titulo: title,
        descripcion: '',
        preguntas: [ createQuestion('Pregunta 1') ]
    };
}

// ---------- lista simple de tipos para usar en selects (puede ampliarse) ----------
const questionTypes = [
    { value: 'texto_corto', label: 'Respuesta corta' },
    { value: 'parrafo', label: 'Párrafo' },
    { value: 'opcion_unica', label: 'Opción múltiple' },
    { value: 'varias_opciones', label: 'Casillas (checkbox)' },
    { value: 'desplegable', label: 'Desplegable' },
    { value: 'escala_lineal', label: 'Escala lineal' },
    { value: 'cuadricula_unica', label: 'Cuadrícula de opciones' },
    { value: 'cuadricula_multiple', label: 'Cuadrícula (casillas)' },
    { value: 'fecha', label: 'Fecha' },
    { value: 'hora', label: 'Hora' }
];

// ---------- export: fábrica que Alpine usará con x-data="formBuilder(initial, formId)" ----------
export function formBuilder(initialSections = [], formId = null) {
    // normaliza incoming initialSections (evita referencias inesperadas)
    const normalized = Array.isArray(initialSections) && initialSections.length
        ? JSON.parse(JSON.stringify(initialSections))
        : [ createSection('Sección 1') ];

    return {
        formId: formId || null,
        secciones: normalized,
        seleccionado: { seccion: null, pregunta: null },

        // getter para tipos (utilizar en template)
        get tipos() {
            return questionTypes;
        },

        // ---------------- sections ----------------
        addSection() {
            const n = this.secciones.length + 1;
            this.secciones.push(createSection(`Sección ${n}`));
        },

        removeSection(index) {
            if (!Number.isInteger(index) || index < 0 || index >= this.secciones.length) return;
            if (this.secciones.length === 1) {
                alert('Debe haber al menos una sección');
                return;
            }
            this.secciones.splice(index, 1);
            // actualizar selección si corresponde
            if (this.seleccionado.seccion === index) {
                this.seleccionado.seccion = null;
                this.seleccionado.pregunta = null;
            }
        },

        selectSection(index) {
            if (!Number.isInteger(index)) return;
            this.seleccionado.seccion = index;
            this.seleccionado.pregunta = null;
        },

        // ---------------- preguntas ----------------
        addPregunta(secIndex) {
            if (!Number.isInteger(secIndex) || !this.secciones[secIndex]) {
                alert('Selecciona una sección primero');
                return;
            }
            const q = createQuestion(`Pregunta ${this.secciones[secIndex].preguntas.length + 1}`);
            this.secciones[secIndex].preguntas.push(q);
            this.seleccionado.pregunta = this.secciones[secIndex].preguntas.length - 1;
            this.seleccionado.seccion = secIndex;
        },

        addPreguntaToCurrent() {
            if (this.seleccionado.seccion === null) {
                alert('Selecciona una sección primero');
                return;
            }
            this.addPregunta(this.seleccionado.seccion);
        },

        selectPregunta(secIndex, pregIndex) {
            if (!this.secciones[secIndex] || !this.secciones[secIndex].preguntas[pregIndex]) return;
            this.seleccionado.seccion = secIndex;
            this.seleccionado.pregunta = pregIndex;
        },

        duplicatePregunta(secIndex, pregIndex) {
            if (!this.seleccionado || this.seleccionado.seccion === null || this.seleccionado.pregunta === null) {
                alert('Selecciona una pregunta a duplicar');
                return;
            }
            const original = this.secciones[secIndex].preguntas[pregIndex];
            const copy = JSON.parse(JSON.stringify(original));
            copy.id = uid('q-');
            // también regenerar ids de opciones
            copy.opciones = (copy.opciones || []).map(o => ({ ...o, id: uid('o-') }));
            this.secciones[secIndex].preguntas.splice(pregIndex + 1, 0, copy);
        },

        removePregunta(secIndex, pregIndex) {
            if (!this.secciones[secIndex] || !this.secciones[secIndex].preguntas[pregIndex]) return;
            this.secciones[secIndex].preguntas.splice(pregIndex, 1);
            // ajustar seleccionado
            if (this.seleccionado.pregunta === pregIndex) {
                this.seleccionado.pregunta = null;
            }
        },

        // ---------------- opciones ----------------
        isChoice(pregunta) {
            return ['opcion_unica','varias_opciones','desplegable'].includes(pregunta.tipo);
        },

        addOption(secIndex, pregIndex) {
            const q = this.secciones[secIndex]?.preguntas?.[pregIndex];
            if (!q) return;
            if (!Array.isArray(q.opciones)) q.opciones = [];
            q.opciones.push({ id: uid('o-'), texto: `Opción ${q.opciones.length + 1}` });
        },

        removeOption(secIndex, pregIndex, optIndex) {
            const q = this.secciones[secIndex]?.preguntas?.[pregIndex];
            if (!q || !Array.isArray(q.opciones)) return;
            q.opciones.splice(optIndex, 1);
        },

        changeTipo(secIndex, pregIndex, tipo) {
            const q = this.secciones[secIndex]?.preguntas?.[pregIndex];
            if (!q) return;
            q.tipo = tipo;

            if (['opcion_unica','varias_opciones','desplegable'].includes(tipo)) {
                if (!Array.isArray(q.opciones) || q.opciones.length === 0) {
                    q.opciones = [
                        { id: uid('o-'), texto: 'Opción 1' },
                        { id: uid('o-'), texto: 'Opción 2' }
                    ];
                }
            } else {
                // limpiar opciones si el tipo no las necesita
                q.opciones = [];
            }

            if (tipo === 'escala_lineal') {
                q.escala_min = q.escala_min ?? 1;
                q.escala_max = q.escala_max ?? 5;
            }
            if (tipo === 'cuadricula_unica' || tipo === 'cuadricula_multiple') {
                q.filas = q.filas && q.filas.length ? q.filas : ['Fila 1'];
                q.columnas = q.columnas && q.columnas.length ? q.columnas : ['Columna 1'];
            }
        },

        // ---------------- preview (usa la estructura básica) ----------------
        preview(pregunta) {
            // simple preview fallback (puedes conectarlo luego a tu render.js)
            if (!pregunta) return '';
            switch (pregunta.tipo) {
                case 'texto_corto': return `<input type="text" class="w-full border p-2" disabled>`;
                case 'parrafo': return `<textarea class="w-full border p-2" rows="3" disabled></textarea>`;
                case 'opcion_unica':
                    return (pregunta.opciones || []).map(o => `<label class="flex items-center gap-2"><input type="radio" disabled><span>${o.texto}</span></label>`).join('');
                case 'varias_opciones':
                    return (pregunta.opciones || []).map(o => `<label class="flex items-center gap-2"><input type="checkbox" disabled><span>${o.texto}</span></label>`).join('');
                case 'desplegable':
                    return `<select class="border p-2 w-full" disabled>${(pregunta.opciones || []).map(o => `<option>${o.texto}</option>`).join('')}</select>`;
                case 'escala_lineal':
                    let items = '';
                    for (let i = (pregunta.escala_min || 1); i <= (pregunta.escala_max || 5); i++) {
                        items += `<label class="flex items-center gap-1"><input type="radio" disabled>${i}</label>`;
                    }
                    return `<div class="flex gap-2">${items}</div>`;
                default:
                    return `<em>Vista previa</em>`;
            }
        },

        // ---------------- guardar (envío estructurado) ----------------
        async guardar() {
            // si formId no está seteado, intenta buscarlo en window (opcional)
            const fid = this.formId || (window && window.currentFormId) || null;
            if (!fid) {
                alert('formId no especificado. No se guardará.');
                return;
            }

            const payload = { estructura: this.secciones };

            try {
                const response = await fetch(`/formularios/${fid}/estructura`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    const txt = await response.text().catch(()=>null);
                    throw new Error(`${response.status} ${txt || response.statusText}`);
                }

                const data = await response.json();
                alert(data.message || 'Guardado correctamente');
            } catch (err) {
                console.error(err);
                alert('Error al guardar: ' + (err.message || 'desconocido'));
            }
        }
    };
}
