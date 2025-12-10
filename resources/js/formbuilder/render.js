// resources/js/formbuilder/render.js

export function renderQuestion(question) {
    return {
        get isChoice() {
            return ['opcion_multiple', 'casillas', 'desplegable'].includes(question.tipo);
        },

        get isScale() {
            return question.tipo === 'escala';
        },

        get isGrid() {
            return ['cuadricula_opciones', 'cuadricula_casillas'].includes(question.tipo);
        },

        get isShortText() {
            return question.tipo === 'texto_corto';
        },

        get isLongText() {
            return question.tipo === 'texto_largo';
        },

        get isDate() {
            return question.tipo === 'fecha';
        },

        get isTime() {
            return question.tipo === 'hora';
        },

        // Render dinámico del input de vista previa — tipo Google Forms
        previewInput() {
            switch (question.tipo) {
                case 'texto_corto':
                    return `<input type="text" class="w-full border-b p-1 text-gray-600" placeholder="Respuesta corta" disabled>`;
                case 'texto_largo':
                    return `<textarea class="w-full border p-2 h-24 text-gray-600" disabled placeholder="Respuesta larga"></textarea>`;
                case 'opcion_multiple':
                    return this.renderOptions('radio');
                case 'casillas':
                    return this.renderOptions('checkbox');
                case 'desplegable':
                    return this.renderSelect();
                case 'escala':
                    return this.renderScale();
                case 'cuadricula_opciones':
                    return this.renderGrid('radio');
                case 'cuadricula_casillas':
                    return this.renderGrid('checkbox');
                case 'fecha':
                    return `<input type="date" class="border p-2" disabled>`;
                case 'hora':
                    return `<input type="time" class="border p-2" disabled>`;
                default:
                    return '';
            }
        },

        renderOptions(type) {
            return question.opciones
                .map(o => `
                    <label class="flex items-center space-x-2 text-gray-700">
                        <input type="${type}" disabled>
                        <span>${o.texto || 'Opción'}</span>
                    </label>
                `).join('');
        },

        renderSelect() {
            return `
                <select class="border p-2 rounded w-full" disabled>
                    ${question.opciones.map(o => `<option>${o.texto}</option>`).join('')}
                </select>`;
        },

        renderScale() {
            let items = '';
            for (let i = question.escala_min; i <= question.escala_max; i++) {
                items += `
                    <label class="flex flex-col items-center text-gray-700">
                        <input type="radio" disabled>
                        <span>${i}</span>
                    </label>
                `;
            }
            return `<div class="flex space-x-4">${items}</div>`;
        },

        renderGrid(type) {
            const filas = [...new Set(question.opciones.map(o => o.fila))];
            const columnas = [...new Set(question.opciones.map(o => o.columna))];

            let html = `<table class="w-full border-collapse text-center">
                <tr>
                    <th></th>
                    ${columnas.map(c => `<th>${c}</th>`).join('')}
                </tr>`;

            filas.forEach(fila => {
                html += `<tr><th class="text-left">${fila}</th>`;
                columnas.forEach(() => {
                    html += `<td><input type="${type}" disabled></td>`;
                });
                html += `</tr>`;
            });

            html += `</table>`;
            return html;
        }
    };
}
