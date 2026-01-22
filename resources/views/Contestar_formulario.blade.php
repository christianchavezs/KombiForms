@extends('layouts.app')

@section('content')
<div class="container py-5 empresa-form">

    {{-- ENCABEZADO --}}
    <div class="mb-5">
        <h1 class="empresa-titulo">{{ $formulario->titulo }}</h1>
        <p class="empresa-descripcion">{{ $formulario->descripcion }}</p>
    </div>

    <form method="POST" action="{{ url('/formularios/'.$formulario->id.'/responder') }}">
        @csrf
        @foreach($formulario->secciones as $seccion)
            <div class="empresa-card">
                <div class="empresa-seccion">
                    <h3>{{ $seccion->titulo }}</h3>
                    <p>{{ $seccion->descripcion }}</p>
                </div>

                @foreach($seccion->preguntas as $pregunta)
                    <div class="empresa-pregunta">
                        <label class="empresa-label">
                            {{ $pregunta->texto }}
                            @if($pregunta->obligatorio)
                                <span class="empresa-required">*</span>
                            @endif
                        </label>

                        {{-- TEXTO CORTO--}}
                        @if($pregunta->tipo === 'texto_corto')
                            <input type="text" name="respuestas[{{ $pregunta->id }}]" class="empresa-input" {{ $pregunta->obligatorio ? 'required' : '' }} >

                        {{-- OPCIÓN MÚLTIPLE --}}
                        @elseif($pregunta->tipo === 'opcion_multiple')
                            @foreach($pregunta->opciones as $opcion)
                                <div class="empresa-option">
                                    <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="{{ $opcion->id }}">
                                    <span>{{ $opcion->texto }}</span>
                                </div>
                            @endforeach
                        

                        {{-- ESCALA LINEAL --}}
                        @elseif($pregunta->tipo === 'escala_lineal')
                            <div class="empresa-scale space-y-2">

                                {{-- Fila de etiquetas (arriba, separadas y más grandes) --}}
                                <div class="flex justify-between w-full mb-2">
                                    <div class="text-base font-semibold text-gray-700">{{ $pregunta->etiqueta_inicial }}</div>
                                    <div class="text-base font-semibold text-gray-700">{{ $pregunta->etiqueta_final }}</div>
                                </div>

                                {{-- Fila de radios con números (abajo) --}}
                                <div class="flex gap-2 w-full">
                                    @for($i = $pregunta->escala_min; $i <= $pregunta->escala_max; $i++)
                                        <label class="empresa-scale-option flex-1 text-center">
                                            <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="{{ $i }}">
                                            <span>{{ $i }}</span>
                                        </label>
                                    @endfor
                                </div>
                            </div>





                        {{-- CASILLA MÚLTIPLE --}}
                        @elseif($pregunta->tipo === 'cuadricula_casillas') 
                            <table class="empresa-grid">
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
                                            <td class="empresa-grid-row">
                                                {{ $fila->texto }}
                                            </td>

                                            @foreach($pregunta->columnas as $columna)
                                                <td class="empresa-grid-cell">
                                                    <input type="checkbox" name="respuestas[{{ $pregunta->id }}][{{ $fila->id }}][]" value="{{ $columna->id }}"  >
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        {{-- PÁRRAFO --}}
                        @elseif($pregunta->tipo === 'parrafo')
                            <textarea name="respuestas[{{ $pregunta->id }}]" class="empresa-textarea" {{ $pregunta->obligatorio ? 'required' : '' }}></textarea>

                        {{-- LISTA DE CASILLAS --}}
                        @elseif($pregunta->tipo === 'casillas')
                            @foreach($pregunta->opciones as $opcion)
                                <div class="empresa-option">
                                    <input type="checkbox" name="respuestas[{{ $pregunta->id }}][]" value="{{ $opcion->id }}" >
                                    <span>{{ $opcion->texto }}</span>
                                </div>
                            @endforeach
                        
                        {{-- CUADRÍCULA OPCIÓN ÚNICA --}}
                        @elseif($pregunta->tipo === 'cuadricula_opciones')
                            <table class="empresa-grid">
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
                                            <td class="empresa-grid-row">
                                                {{ $fila->texto }}
                                            </td>

                                            @foreach($pregunta->columnas as $columna)
                                                <td class="empresa-grid-cell">
                                                    <input type="radio" name="respuestas[{{ $pregunta->id }}][{{ $fila->id }}]" value="{{ $columna->id }}"  >
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach

        {{-- BOTÓN --}}
        <div class="empresa-footer">
            <button type="submit" class="empresa-btn">
                Enviar formulario
            </button>
        </div>
    </form>
</div>

<style>

/* ESCALA LINEAL */
.empresa-scale {
    display: flex;
    gap: 18px;
    margin-top: 10px;
    flex-wrap: wrap;
}

.empresa-scale-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 14px;
    color: #374151;
    cursor: pointer;
}

.empresa-scale-option input {
    margin-bottom: 4px;
    accent-color: #2563eb;
    cursor: pointer;
}

/* CUADRÍCULA OPCIÓN ÚNICA */
.empresa-grid input[type="radio"] {
    width: 18px;
    height: 18px;
    accent-color: #2563eb;
    cursor: pointer;
}

/* Contenedor general */
.empresa-grid {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    margin-top: 12px;
}

/* Header */
.empresa-grid thead th {
    background: #f8fafc;
    color: #334155;
    font-size: 14px;
    font-weight: 600;
    padding: 14px;
    text-align: center;
    border-bottom: 1px solid #e2e8f0;
}

/* Primera columna (filas) */
.empresa-grid-row {
    background: #f9fafb;
    font-weight: 600;
    color: #1f2937;
    text-align: left;
    padding: 14px;
    border-right: 1px solid #e5e7eb;
    white-space: nowrap;
}

/* Celdas */
.empresa-grid-cell {
    text-align: center;
    padding: 14px;
    border-bottom: 1px solid #f1f5f9;
}

/* Hover elegante por fila */
.empresa-grid tbody tr:hover {
    background-color: #f8fafc;
}

/* Checkboxes modernos */
.empresa-grid input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #2563eb; /* Azul elegante */
    transition: transform 0.15s ease;
}

.empresa-grid input[type="checkbox"]:hover {
    transform: scale(1.15);
}

/* Responsive: scroll horizontal sin romper diseño */
@media (max-width: 768px) {
    .empresa-grid {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
}


.empresa-form {
    max-width: 900px;
    margin: auto;
    font-family: "Inter", "Segoe UI", Arial, sans-serif;
    color: #1f2933;
}

/* ENCABEZADO */
.empresa-titulo {
    font-size: 2.2rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.empresa-descripcion {
    font-size: 1.05rem;
    color: #6b7280;
}

/* TARJETAS */
.empresa-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 2rem;
    margin-bottom: 2rem;
}

/* SECCIÓN */
.empresa-seccion h3 {
    font-size: 1.4rem;
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.empresa-seccion p {
    color: #6b7280;
    margin-bottom: 1.5rem;
}

/* PREGUNTAS */
.empresa-pregunta {
    margin-bottom: 1.5rem;
}

.empresa-label {
    display: block;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.empresa-required {
    color: #b91c1c;
}

/* INPUTS */
.empresa-input,
.empresa-textarea {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    padding: 0.6rem 0.75rem;
    font-size: 0.95rem;
}

.empresa-textarea {
    min-height: 120px;
}

/* OPCIONES */
.empresa-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

/* BOTÓN */
.empresa-footer {
    text-align: right;
}

.empresa-btn {
    background-color: #1f2933;
    color: #ffffff;
    border: none;
    padding: 0.7rem 2.2rem;
    font-size: 0.95rem;
    border-radius: 4px;
    font-weight: 500;
}

.empresa-btn:hover {
    background-color: #111827;
}

</style>
@endsection
