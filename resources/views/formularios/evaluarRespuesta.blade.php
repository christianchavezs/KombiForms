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
        @endphp

        <div class="bg-white rounded-2xl shadow border border-gray-200 overflow-hidden">

            <!-- HEADER -->
            <div class="bg-gray-50 border-b px-6 py-4">

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <h3 class="text-lg font-bold text-gray-800">
                            {{ $pregunta?->texto ?? 'Pregunta no disponible' }}
                        </h3>

                        <div class="flex flex-wrap gap-2 mt-3">

                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold">
                                {{ $pregunta?->tipo ?? 'N/A' }}
                            </span>

                            <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-bold">
                                Ponderación:
                                {{ $pregunta?->ponderacion ?? 1 }}
                            </span>

                        </div>

                    </div>

                    <!-- ESTADO -->
                    <div>

                        @if($ri->estado === 'correcta')

                            <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">
                                Correcta
                            </span>

                        @elseif($ri->estado === 'incorrecta')

                            <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">
                                Incorrecta
                            </span>

                        @elseif($ri->estado === 'N/A')

                            <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-bold">
                                N/A
                            </span>

                        @else

                            <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-bold">
                                Pendiente
                            </span>

                        @endif

                    </div>

                </div>

            </div>

            <!-- BODY -->
            <div class="p-6">

                <p class="text-xs uppercase tracking-wide text-gray-400 font-bold mb-2">
                    Respuesta del usuario
                </p>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-gray-800 whitespace-pre-line">

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

            </div>

            <!-- FOOTER -->
            <div class="mt-5 flex items-center justify-between border-t pt-4 px-6 pb-6">

                <div>

                    <p class="text-xs uppercase text-gray-400 font-bold">
                        Puntaje
                    </p>

                    <p class="text-lg font-bold text-indigo-700">
                        {{ $ri->puntaje ?? 0 }}
                    </p>

                </div>

                <div>

                    <p class="text-xs uppercase text-gray-400 font-bold">
                        Estado actual
                    </p>

                    <p class="font-semibold text-gray-700">
                        {{ $ri->estado }}
                    </p>

                </div>

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