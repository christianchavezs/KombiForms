@extends('layouts.app')

@section('content')

<div class="p-6 max-w-5xl mx-auto">

    <!-- HEADER -->
    <div class="mb-6 flex items-center justify-between">

        <div>
            <h1 class="text-3xl font-extrabold text-gray-800">
                Evaluar Cuestionario
            </h1>

            <p class="text-gray-500 mt-1">
                {{ $formulario->titulo }}
            </p>
        </div>

        <!-- ESTADO GENERAL -->
        <div>
            @if($respuesta->estado === 'evaluado')
                <span class="px-4 py-2 rounded-full bg-green-100 text-green-700 text-sm font-bold">
                    Evaluado
                </span>
            @else
                <span class="px-4 py-2 rounded-full bg-yellow-100 text-yellow-700 text-sm font-bold">
                    Pendiente
                </span>
            @endif
        </div>

    </div>

    <!-- INFO RESPUESTA -->
    <div class="bg-white shadow-xl rounded-xl p-5 mb-6 border border-gray-200">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <!-- USUARIO -->
            <div>
                <p class="text-xs uppercase text-gray-400 font-bold">Usuario</p>
                <p class="text-gray-800 font-semibold mt-1">
                    {{ $respuesta->usuario->name ?? 'Anónimo' }}
                </p>
            </div>

            <!-- CORREO -->
            <div>
                <p class="text-xs uppercase text-gray-400 font-bold">Correo</p>
                <p class="text-gray-800 font-semibold mt-1">
                    {{ $respuesta->correo_respondedor ?? 'N/A' }}
                </p>
            </div>

            <!-- FECHA -->
            <div>
                <p class="text-xs uppercase text-gray-400 font-bold">Fecha de envío</p>
                <p class="text-gray-800 font-semibold mt-1">
                    {{ $respuesta->enviado_en }}
                </p>
            </div>

            <!-- 🆕 PUNTAJE TOTAL -->
            <div>
                <p class="text-xs uppercase text-gray-400 font-bold">Puntaje total</p>
                <p class="text-gray-900 font-extrabold mt-1 text-lg">
                    {{ $respuesta->puntaje_total ?? 0 }}
                </p>
            </div>

        </div>

    </div>

 

<!-- LISTADO DE RESPUESTAS -->
<div class="space-y-6">

    @forelse($respuesta->respuestasIndividuales as $ri)

        @php
            $pregunta = $ri->pregunta;
            $puntajeCalculado = null;
            $estadoCalculado = null;

            // Opción múltiple
            if ($pregunta && $pregunta->tipo === 'opcion_multiple' && $ri->opcion) {
                if ($ri->opcion->es_correcta) {
                    $puntajeCalculado = $pregunta->ponderacion ?? 1;
                    $estadoCalculado = 'correcta';
                } else {
                    $puntajeCalculado = 0;
                    $estadoCalculado = 'incorrecta';
                }
            }

            // Casillas
            if ($pregunta && $pregunta->tipo === 'casillas' && $ri->opcion) {
                $totalCorrectas = $pregunta->opciones->where('es_correcta', 1)->count();
                if ($ri->opcion->es_correcta && $totalCorrectas > 0) {
                    $puntajeCalculado = ($pregunta->ponderacion ?? 1) / $totalCorrectas;
                    $estadoCalculado = 'correcta';
                } else {
                    $puntajeCalculado = 0;
                    $estadoCalculado = 'incorrecta';
                }
            }

            // Texto corto / párrafo que requieren evaluación manual
            if ($pregunta && in_array($pregunta->tipo, ['texto_corto','parrafo']) && $pregunta->requiere_evaluador) {
                $puntajeCalculado = $ri->puntaje ?? 0;
                $estadoCalculado = $ri->estado ?? 'pendiente';
            }
        @endphp

        <div id="respuesta-{{ $ri->id }}" class="bg-white rounded-2xl shadow border border-gray-200 overflow-hidden">

            <!-- HEADER -->
            <div class="bg-gray-50 border-b px-6 py-4 flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">
                        {{ $pregunta?->texto ?? 'Pregunta no disponible' }}
                    </h3>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold">
                            {{ $pregunta?->tipo ?? 'N/A' }}
                        </span>
                        <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-bold">
                            Ponderación: {{ $pregunta?->ponderacion ?? 1 }}
                        </span>
                    </div>
                </div>
                <div class="estado">
                    @if($estadoCalculado === 'correcta')
                        <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">Correcta</span>
                    @elseif($estadoCalculado === 'incorrecta')
                        <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">Incorrecta</span>
                    @elseif($estadoCalculado === 'N/A')
                        <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-bold">N/A</span>
                    @else
                        <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-bold">Pendiente</span>
                    @endif
                </div>
            </div>

            <!-- BODY -->
<div class="p-6">

    <p class="text-xs uppercase tracking-wide text-gray-400 font-bold mb-2">
        Respuesta del usuario
    </p>

    <div class="respuesta-box border rounded-xl p-4 whitespace-pre-line
        @if($estadoCalculado === 'correcta') bg-green-50 text-green-700 border-green-200
        @elseif($estadoCalculado === 'incorrecta') bg-red-50 text-red-700 border-red-200
        @else bg-gray-50 text-gray-800 border-gray-200
        @endif">

        @if(!empty($ri->texto_respuesta))
            {{ $ri->texto_respuesta }}
        @elseif(!is_null($ri->valor_numerico))
            {{ $ri->valor_numerico }}
        @elseif(!empty($ri->opcion))
            {{ $ri->opcion->texto ?? 'Sin opción' }}
        @elseif(!empty($ri->valor_fecha))
            {{ $ri->valor_fecha }}
        @elseif(!empty($ri->valor_hora))
            {{ $ri->valor_hora }}
        @else
            Sin respuesta
        @endif
    </div>

    @if(!is_null($puntajeCalculado))
        <!-- Puntaje calculado -->
        <div class="mt-3">
            <p class="text-xs uppercase text-gray-400 font-bold">Puntaje calculado</p>
            <p class="puntaje text-lg font-bold text-indigo-700">
                {{ number_format($puntajeCalculado, 2) }}
            </p>
        </div>

        <!-- Estado -->
        <div class="mt-1">
            <p class="text-xs uppercase text-gray-400 font-bold">Estado</p>
            <p class="font-semibold 
                @if($estadoCalculado === 'correcta') text-green-700 
                @elseif($estadoCalculado === 'incorrecta') text-red-700 
                @else text-yellow-700 
                @endif">
                {{ $estadoCalculado }}
            </p>
        </div>
    @endif

    {{-- Botón para mostrar opciones de evaluación manual --}}
    @if($pregunta && in_array($pregunta->tipo, ['texto_corto','parrafo']) && $pregunta->requiere_evaluador)
        <div class="mt-4">
            <button onclick="document.getElementById('eval-{{ $ri->id }}').classList.toggle('hidden')" 
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Evaluar
            </button>

            <div id="eval-{{ $ri->id }}" class="hidden mt-3 flex gap-3">
                <button 
                    onclick="evaluarRespuesta({{ $ri->id }}, 'correcta', {{ $pregunta->ponderacion ?? 1 }})"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Marcar Correcta
                </button>
                <button 
                    onclick="evaluarRespuesta({{ $ri->id }}, 'incorrecta', 0)"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Marcar Incorrecta
                </button>
            </div>
        </div>
    @endif

</div>

        </div>

    @empty
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-yellow-700">
            No hay respuestas para mostrar.
        </div>
    @endforelse

</div>




</div>

@endsection



<!-- Script AJAX -->
<script>
function evaluarRespuesta(id, estado, puntaje) {
    fetch(`/respuestas/${id}/evaluar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ estado: estado, puntaje: puntaje })
    })
    .then(response => response.json())
    .then(data => {
        const card = document.getElementById(`respuesta-${id}`);
        if(card){
            // Actualizar puntaje con formato 1.00 / 0.00
            const puntajeEl = card.querySelector('.puntaje');
            if(puntajeEl) puntajeEl.innerText = parseFloat(data.puntaje).toFixed(2);

            // Actualizar estado visual
            const estadoEl = card.querySelector('.estado');
            if(estadoEl){
                if(data.estado === 'correcta'){
                    estadoEl.innerHTML = '<span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">Correcta</span>';
                } else if(data.estado === 'incorrecta'){
                    estadoEl.innerHTML = '<span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">Incorrecta</span>';
                } else {
                    estadoEl.innerHTML = '<span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-bold">Pendiente</span>';
                }
            }

            // Cambiar color del bloque principal
            const respuestaBox = card.querySelector('.respuesta-box');
            if(respuestaBox){
                if(data.estado === 'correcta'){
                    respuestaBox.className = 'respuesta-box border rounded-xl p-4 whitespace-pre-line bg-green-50 text-green-700 border-green-200';
                } else if(data.estado === 'incorrecta'){
                    respuestaBox.className = 'respuesta-box border rounded-xl p-4 whitespace-pre-line bg-red-50 text-red-700 border-red-200';
                } else {
                    respuestaBox.className = 'respuesta-box border rounded-xl p-4 whitespace-pre-line bg-gray-50 text-gray-800 border-gray-200';
                }
            }
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
