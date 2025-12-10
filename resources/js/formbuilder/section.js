// resources/js/formbuilder/section.js

import { createEmptyQuestion } from './question';

export function createSection(title = "Nueva sección") {
    return {
        id: Date.now() + "-" + Math.random().toString(36).substr(2, 5),
        titulo: title,
        descripcion: "",
        preguntas: [],

        addPregunta() {
            this.preguntas.push(createEmptyQuestion());
        },

        deletePregunta(preguntaId) {
            this.preguntas = this.preguntas.filter(q => q.id !== preguntaId);
        },

        duplicatePregunta(pregunta) {
            const clone = JSON.parse(JSON.stringify(pregunta));
            clone.id = Date.now() + "-" + Math.random().toString(36).substr(2, 5);
            this.preguntas.push(clone);
        },

        movePreguntaUp(index) {
            if (index === 0) return;
            const temp = this.preguntas[index];
            this.preguntas[index] = this.preguntas[index - 1];
            this.preguntas[index - 1] = temp;
        },

        movePreguntaDown(index) {
            if (index === this.preguntas.length - 1) return;
            const temp = this.preguntas[index];
            this.preguntas[index] = this.preguntas[index + 1];
            this.preguntas[index + 1] = temp;
        }
    };
}
