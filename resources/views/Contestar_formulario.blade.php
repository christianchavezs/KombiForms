@extends('layouts.app')

@section('content')
<div class="form-container">
    {{-- BARRA DE PROGRESO DECORATIVA --}}
    <div class="form-progress-bar">
        <div class="form-progress-fill"></div>
    </div>

    {{-- ENCABEZADO DEL FORMULARIO --}}
    <div class="form-header">
        <div class="form-header-accent"></div>
        <div class="form-header-content">
            <h1 class="form-title">{{ $formulario->titulo }}</h1>
            <p class="form-description">{{ $formulario->descripcion }}</p>
        </div>
    </div>

    <form method="POST" action="{{ url('/formularios/'.$formulario->id.'/responder') }}" class="form-main">
        @csrf
        @foreach($formulario->secciones as $seccion)
            <div class="form-section">
                <div class="form-section-header">
                    <h3 class="form-section-title">{{ $seccion->titulo }}</h3>
                    <p class="form-section-description">{{ $seccion->descripcion }}</p>
                </div>

                <div class="form-questions">
                    @foreach($seccion->preguntas as $pregunta)
                        <div class="form-question">
                            <label class="form-label">
                                {{ $pregunta->texto }}
                                @if($pregunta->obligatorio)
                                    <span class="form-required">*</span>
                                @endif
                            </label>

                            {{-- TEXTO CORTO --}}
                            @if($pregunta->tipo === 'texto_corto')
                                <div class="form-input-wrapper">
                                    <input 
                                        type="text" 
                                        name="respuestas[{{ $pregunta->id }}]" 
                                        class="form-input" 
                                        placeholder="Tu respuesta"
                                        {{ $pregunta->obligatorio ? 'required' : '' }}
                                    >
                                    <div class="form-input-focus"></div>
                                </div>

                            {{-- OPCIÓN MÚLTIPLE --}}
                            @elseif($pregunta->tipo === 'opcion_multiple')
                                <div class="form-options">
                                    @foreach($pregunta->opciones as $opcion)
                                        <label class="form-option form-option-radio">
                                            <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="{{ $opcion->id }}">
                                            <span class="form-option-indicator"></span>
                                            <span class="form-option-text">{{ $opcion->texto }}</span>
                                        </label>
                                    @endforeach
                                </div>

                            {{-- ESCALA LINEAL --}}
                            @elseif($pregunta->tipo === 'escala_lineal')
                                <div class="form-scale">
                                    <div class="form-scale-labels">
                                        <span class="form-scale-label">{{ $pregunta->etiqueta_inicial }}</span>
                                        <span class="form-scale-label">{{ $pregunta->etiqueta_final }}</span>
                                    </div>
                                    <div class="form-scale-options">
                                        @for($i = $pregunta->escala_min; $i <= $pregunta->escala_max; $i++)
                                            <label class="form-scale-option">
                                                <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="{{ $i }}">
                                                <span class="form-scale-number">{{ $i }}</span>
                                            </label>
                                        @endfor
                                    </div>
                                </div>

                            {{-- CASILLA MÚLTIPLE (CUADRÍCULA) --}}
                            @elseif($pregunta->tipo === 'cuadricula_casillas')
                                <div class="form-grid-wrapper">
                                    <table class="form-grid">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                @foreach($pregunta->columnas as $columna)
                                                    <th>{{ $columna->texto }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pregunta->filas as $fila)
                                                <tr>
                                                    <td class="form-grid-row-label">{{ $fila->texto }}</td>
                                                    @foreach($pregunta->columnas as $columna)
                                                        <td class="form-grid-cell">
                                                            <label class="form-grid-checkbox">
                                                                <input type="checkbox" name="respuestas[{{ $pregunta->id }}][{{ $fila->id }}][]" value="{{ $columna->id }}">
                                                                <span class="form-grid-checkbox-indicator"></span>
                                                            </label>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            {{-- PÁRRAFO --}}
                            @elseif($pregunta->tipo === 'parrafo')
                                <div class="form-textarea-wrapper">
                                    <textarea 
                                        name="respuestas[{{ $pregunta->id }}]" 
                                        class="form-textarea" 
                                        placeholder="Tu respuesta"
                                        {{ $pregunta->obligatorio ? 'required' : '' }}
                                    ></textarea>
                                    <div class="form-input-focus"></div>
                                </div>

                            {{-- LISTA DE CASILLAS --}}
                            @elseif($pregunta->tipo === 'casillas')
                                <div class="form-options">
                                    @foreach($pregunta->opciones as $opcion)
                                        <label class="form-option form-option-checkbox">
                                            <input type="checkbox" name="respuestas[{{ $pregunta->id }}][]" value="{{ $opcion->id }}">
                                            <span class="form-option-indicator"></span>
                                            <span class="form-option-text">{{ $opcion->texto }}</span>
                                        </label>
                                    @endforeach
                                </div>

                            {{-- CUADRÍCULA OPCIÓN ÚNICA --}}
                            @elseif($pregunta->tipo === 'cuadricula_opciones')
                                <div class="form-grid-wrapper">
                                    <table class="form-grid">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                @foreach($pregunta->columnas as $columna)
                                                    <th>{{ $columna->texto }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pregunta->filas as $fila)
                                                <tr>
                                                    <td class="form-grid-row-label">{{ $fila->texto }}</td>
                                                    @foreach($pregunta->columnas as $columna)
                                                        <td class="form-grid-cell">
                                                            <label class="form-grid-radio">
                                                                <input type="radio" name="respuestas[{{ $pregunta->id }}][{{ $fila->id }}]" value="{{ $columna->id }}">
                                                                <span class="form-grid-radio-indicator"></span>
                                                            </label>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- BOTÓN DE ENVÍO --}}
        <div class="form-footer">
            <button type="submit" class="form-submit-btn">
                <span>Enviar formulario</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </button>
            <p class="form-footer-note">Los campos marcados con <span class="form-required">*</span> son obligatorios</p>
        </div>
    </form>
</div>

<style>
/* ========================================
   VARIABLES Y CONFIGURACIÓN BASE
   ======================================== */
:root {
    --form-primary: #6366f1;
    --form-primary-hover: #4f46e5;
    --form-primary-light: #eef2ff;
    --form-primary-subtle: #c7d2fe;
    
    --form-bg: #f8fafc;
    --form-card-bg: #ffffff;
    --form-border: #e2e8f0;
    --form-border-focus: #6366f1;
    
    --form-text: #1e293b;
    --form-text-secondary: #64748b;
    --form-text-muted: #94a3b8;
    
    --form-error: #ef4444;
    --form-success: #22c55e;
    
    --form-radius: 12px;
    --form-radius-sm: 8px;
    --form-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1);
    --form-shadow-lg: 0 10px 40px rgba(0, 0, 0, 0.08);
    
    --form-transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ========================================
   CONTENEDOR PRINCIPAL
   ======================================== */
.form-container {
    max-width: 720px;
    margin: 0 auto;
    padding: 24px 16px 60px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: var(--form-bg);
    min-height: 100vh;
}

@media (min-width: 640px) {
    .form-container {
        padding: 40px 24px 80px;
    }
}

/* ========================================
   BARRA DE PROGRESO
   ======================================== */
.form-progress-bar {
    height: 4px;
    background: var(--form-border);
    border-radius: 2px;
    margin-bottom: 32px;
    overflow: hidden;
}

.form-progress-fill {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, var(--form-primary), var(--form-primary-hover));
    border-radius: 2px;
    transition: width 0.3s ease;
}

/* ========================================
   ENCABEZADO DEL FORMULARIO
   ======================================== */
.form-header {
    background: var(--form-card-bg);
    border-radius: var(--form-radius);
    box-shadow: var(--form-shadow);
    overflow: hidden;
    margin-bottom: 24px;
    border: 1px solid var(--form-border);
}

.form-header-accent {
    height: 10px;
    background: linear-gradient(90deg, var(--form-primary), #8b5cf6);
}

.form-header-content {
    padding: 32px 28px;
}

.form-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--form-text);
    margin: 0 0 12px 0;
    line-height: 1.2;
    letter-spacing: -0.02em;
}

.form-description {
    font-size: 16px;
    color: var(--form-text-secondary);
    margin: 0;
    line-height: 1.6;
}

@media (min-width: 640px) {
    .form-title {
        font-size: 32px;
    }
    
    .form-header-content {
        padding: 36px 32px;
    }
}

/* ========================================
   SECCIONES
   ======================================== */
.form-section {
    background: var(--form-card-bg);
    border-radius: var(--form-radius);
    box-shadow: var(--form-shadow);
    margin-bottom: 24px;
    border: 1px solid var(--form-border);
    overflow: hidden;
}

.form-section-header {
    padding: 24px 28px 20px;
    border-bottom: 1px solid var(--form-border);
    background: var(--form-primary-light);
}

.form-section-title {
    font-size: 20px;
    font-weight: 600;
    color: var(--form-text);
    margin: 0 0 6px 0;
}

.form-section-description {
    font-size: 14px;
    color: var(--form-text-secondary);
    margin: 0;
    line-height: 1.5;
}

.form-questions {
    padding: 8px 0;
}

/* ========================================
   PREGUNTAS
   ======================================== */
.form-question {
    padding: 24px 28px;
    border-bottom: 1px solid var(--form-border);
    transition: var(--form-transition);
}

.form-question:last-child {
    border-bottom: none;
}

.form-question:hover {
    background: rgba(99, 102, 241, 0.02);
}

.form-label {
    display: block;
    font-size: 15px;
    font-weight: 500;
    color: var(--form-text);
    margin-bottom: 16px;
    line-height: 1.5;
}

.form-required {
    color: var(--form-error);
    margin-left: 2px;
}

/* ========================================
   INPUTS DE TEXTO
   ======================================== */
.form-input-wrapper,
.form-textarea-wrapper {
    position: relative;
}

.form-input,
.form-textarea {
    width: 100%;
    padding: 14px 16px;
    font-size: 15px;
    color: var(--form-text);
    background: var(--form-bg);
    border: 2px solid var(--form-border);
    border-radius: var(--form-radius-sm);
    transition: var(--form-transition);
    outline: none;
    font-family: inherit;
}

.form-input::placeholder,
.form-textarea::placeholder {
    color: var(--form-text-muted);
}

.form-input:hover,
.form-textarea:hover {
    border-color: var(--form-primary-subtle);
}

.form-input:focus,
.form-textarea:focus {
    border-color: var(--form-primary);
    background: var(--form-card-bg);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

.form-textarea {
    min-height: 140px;
    resize: vertical;
    line-height: 1.6;
}

/* ========================================
   OPCIONES (RADIO Y CHECKBOX)
   ======================================== */
.form-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.form-option {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 16px;
    background: var(--form-bg);
    border: 2px solid transparent;
    border-radius: var(--form-radius-sm);
    cursor: pointer;
    transition: var(--form-transition);
}

.form-option:hover {
    background: var(--form-primary-light);
    border-color: var(--form-primary-subtle);
}

.form-option input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.form-option-indicator {
    width: 22px;
    height: 22px;
    border: 2px solid var(--form-border);
    background: var(--form-card-bg);
    transition: var(--form-transition);
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.form-option-radio .form-option-indicator {
    border-radius: 50%;
}

.form-option-checkbox .form-option-indicator {
    border-radius: 6px;
}

.form-option-radio .form-option-indicator::after {
    content: '';
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--form-primary);
    transform: scale(0);
    transition: var(--form-transition);
}

.form-option-checkbox .form-option-indicator::after {
    content: '';
    width: 6px;
    height: 10px;
    border: solid var(--form-card-bg);
    border-width: 0 2.5px 2.5px 0;
    transform: rotate(45deg) scale(0);
    transition: var(--form-transition);
    margin-bottom: 2px;
}

.form-option input:checked + .form-option-indicator {
    border-color: var(--form-primary);
    background: var(--form-primary);
}

.form-option-radio input:checked + .form-option-indicator {
    background: var(--form-card-bg);
}

.form-option-radio input:checked + .form-option-indicator::after {
    transform: scale(1);
}

.form-option-checkbox input:checked + .form-option-indicator::after {
    transform: rotate(45deg) scale(1);
}

.form-option-text {
    font-size: 15px;
    color: var(--form-text);
    line-height: 1.4;
}

/* ========================================
   ESCALA LINEAL
   ======================================== */
.form-scale {
    padding: 8px 0;
}

.form-scale-labels {
    display: flex;
    justify-content: space-between;
    margin-bottom: 16px;
    padding: 0 8px;
}

.form-scale-label {
    font-size: 13px;
    color: var(--form-text-secondary);
    font-weight: 500;
}

.form-scale-options {
    display: flex;
    gap: 8px;
    justify-content: center;
    flex-wrap: wrap;
}

.form-scale-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    transition: var(--form-transition);
}

.form-scale-option input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.form-scale-number {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 600;
    color: var(--form-text-secondary);
    background: var(--form-bg);
    border: 2px solid var(--form-border);
    border-radius: 50%;
    transition: var(--form-transition);
}

.form-scale-option:hover .form-scale-number {
    border-color: var(--form-primary-subtle);
    background: var(--form-primary-light);
    color: var(--form-primary);
}

.form-scale-option input:checked + .form-scale-number {
    background: var(--form-primary);
    border-color: var(--form-primary);
    color: white;
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

/* ========================================
   CUADRÍCULAS (TABLAS)
   ======================================== */
.form-grid-wrapper {
    overflow-x: auto;
    margin: 8px -12px;
    padding: 0 12px;
}

.form-grid {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: var(--form-card-bg);
    border-radius: var(--form-radius-sm);
    overflow: hidden;
    border: 1px solid var(--form-border);
}

.form-grid thead th {
    background: var(--form-primary-light);
    color: var(--form-text);
    font-size: 13px;
    font-weight: 600;
    padding: 16px 12px;
    text-align: center;
    border-bottom: 1px solid var(--form-border);
    white-space: nowrap;
}

.form-grid thead th:first-child {
    text-align: left;
    min-width: 140px;
}

.form-grid tbody tr {
    transition: var(--form-transition);
}

.form-grid tbody tr:hover {
    background: var(--form-primary-light);
}

.form-grid tbody tr:not(:last-child) td {
    border-bottom: 1px solid var(--form-border);
}

.form-grid-row-label {
    padding: 16px;
    font-size: 14px;
    font-weight: 500;
    color: var(--form-text);
    text-align: left;
    background: var(--form-bg);
}

.form-grid-cell {
    padding: 16px 12px;
    text-align: center;
}

/* Grid Radio */
.form-grid-radio,
.form-grid-checkbox {
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.form-grid-radio input,
.form-grid-checkbox input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.form-grid-radio-indicator,
.form-grid-checkbox-indicator {
    width: 22px;
    height: 22px;
    border: 2px solid var(--form-border);
    background: var(--form-card-bg);
    transition: var(--form-transition);
    display: flex;
    align-items: center;
    justify-content: center;
}

.form-grid-radio-indicator {
    border-radius: 50%;
}

.form-grid-checkbox-indicator {
    border-radius: 6px;
}

.form-grid-radio-indicator::after {
    content: '';
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--form-primary);
    transform: scale(0);
    transition: var(--form-transition);
}

.form-grid-checkbox-indicator::after {
    content: '';
    width: 6px;
    height: 10px;
    border: solid var(--form-card-bg);
    border-width: 0 2.5px 2.5px 0;
    transform: rotate(45deg) scale(0);
    transition: var(--form-transition);
    margin-bottom: 2px;
}

.form-grid-radio input:checked + .form-grid-radio-indicator,
.form-grid-checkbox input:checked + .form-grid-checkbox-indicator {
    border-color: var(--form-primary);
    background: var(--form-primary);
}

.form-grid-radio input:checked + .form-grid-radio-indicator {
    background: var(--form-card-bg);
}

.form-grid-radio input:checked + .form-grid-radio-indicator::after {
    transform: scale(1);
}

.form-grid-checkbox input:checked + .form-grid-checkbox-indicator::after {
    transform: rotate(45deg) scale(1);
}

.form-grid-radio:hover .form-grid-radio-indicator,
.form-grid-checkbox:hover .form-grid-checkbox-indicator {
    border-color: var(--form-primary-subtle);
}

/* ========================================
   FOOTER Y BOTÓN DE ENVÍO
   ======================================== */
.form-footer {
    padding: 32px 0 16px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
}

@media (min-width: 640px) {
    .form-footer {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
}

.form-submit-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 16px 32px;
    font-size: 16px;
    font-weight: 600;
    color: white;
    background: linear-gradient(135deg, var(--form-primary), var(--form-primary-hover));
    border: none;
    border-radius: var(--form-radius-sm);
    cursor: pointer;
    transition: var(--form-transition);
    font-family: inherit;
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.25);
}

.form-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35);
}

.form-submit-btn:active {
    transform: translateY(0);
}

.form-submit-btn svg {
    transition: transform 0.2s ease;
}

.form-submit-btn:hover svg {
    transform: translateX(4px);
}

.form-footer-note {
    font-size: 13px;
    color: var(--form-text-muted);
    margin: 0;
}

/* ========================================
   ANIMACIONES
   ======================================== */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-section {
    animation: fadeIn 0.4s ease forwards;
}

.form-section:nth-child(1) { animation-delay: 0.1s; }
.form-section:nth-child(2) { animation-delay: 0.2s; }
.form-section:nth-child(3) { animation-delay: 0.3s; }
.form-section:nth-child(4) { animation-delay: 0.4s; }

/* ========================================
   ESTADOS DE VALIDACIÓN
   ======================================== */
.form-input:invalid:not(:placeholder-shown),
.form-textarea:invalid:not(:placeholder-shown) {
    border-color: var(--form-error);
}

.form-input:valid:not(:placeholder-shown),
.form-textarea:valid:not(:placeholder-shown) {
    border-color: var(--form-success);
}

/* ========================================
   RESPONSIVE AJUSTES FINALES
   ======================================== */
@media (max-width: 480px) {
    .form-header-content,
    .form-section-header,
    .form-question {
        padding-left: 20px;
        padding-right: 20px;
    }
    
    .form-title {
        font-size: 24px;
    }
    
    .form-scale-number {
        width: 42px;
        height: 42px;
        font-size: 14px;
    }
    
    .form-scale-options {
        gap: 6px;
    }
}
</style>

<script>
// Actualizar barra de progreso mientras el usuario completa el formulario
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.form-main');
    const progressFill = document.querySelector('.form-progress-fill');
    
    if (form && progressFill) {
        const inputs = form.querySelectorAll('input, textarea');
        const requiredInputs = form.querySelectorAll('[required]');
        
        function updateProgress() {
            let filledRequired = 0;
            requiredInputs.forEach(input => {
                if (input.type === 'radio' || input.type === 'checkbox') {
                    const name = input.name;
                    const checked = form.querySelector(`input[name="${name}"]:checked`);
                    if (checked) filledRequired++;
                } else if (input.value.trim() !== '') {
                    filledRequired++;
                }
            });
            
            // Calcular porcentaje considerando campos únicos por nombre
            const uniqueNames = new Set();
            requiredInputs.forEach(input => uniqueNames.add(input.name));
            const total = uniqueNames.size || 1;
            
            const percentage = Math.min((filledRequired / total) * 100, 100);
            progressFill.style.width = percentage + '%';
        }
        
        inputs.forEach(input => {
            input.addEventListener('input', updateProgress);
            input.addEventListener('change', updateProgress);
        });
    }
});
</script>
@endsection
