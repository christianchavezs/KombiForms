// resources/js/formbuilder/types/seleccion.js

export function createSeleccionQuestion(texto = "Nueva pregunta") {
    return {
        tipo: "opcion_unica",
        texto: texto,
        obligatorio: false,
        orden: 0,
        opciones: [
            { texto: "Opción 1" },
            { texto: "Opción 2" }
        ],
        filas: [],
        columnas: [],
        escala_min: null,
        escala_max: null,
    };
}

// ============================
// RENDER DE LAS OPCIONES
// ============================
export function renderOptions(pregunta, container) {
    container.innerHTML = "";

    const wrapper = document.createElement("div");

    pregunta.opciones.forEach((op, i) => {
        const row = document.createElement("div");
        row.className = "flex items-center gap-2 mb-2";

        // Radio visual (deshabilitado)
        const radio = document.createElement("input");
        radio.type = "radio";
        radio.disabled = true;

        // Input editable del texto de la opción
        const input = document.createElement("input");
        input.type = "text";
        input.className = "border p-1 rounded w-full";
        input.value = op.texto;

        input.addEventListener("input", () => {
            pregunta.opciones[i].texto = input.value;
        });

        // Botón eliminar
        const btn = document.createElement("button");
        btn.textContent = "✕";
        btn.className = "text-red-600";

        btn.addEventListener("click", () => {
            pregunta.opciones.splice(i, 1);
            renderOptions(pregunta, container);
        });

        row.appendChild(radio);
        row.appendChild(input);
        row.appendChild(btn);

        wrapper.appendChild(row);
    });

    // Botón añadir opción
    const addBtn = document.createElement("button");
    addBtn.textContent = "+ Añadir opción";
    addBtn.className = "text-blue-600 mt-2";

    addBtn.addEventListener("click", () => {
        pregunta.opciones.push({ texto: "Nueva opción" });
        renderOptions(pregunta, container);
    });

    wrapper.appendChild(addBtn);
    container.appendChild(wrapper);
}
