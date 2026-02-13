@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-extrabold text-gray-800">
            Concentrado de Respuestas
            <span class="text-base font-medium text-gray-500">— {{ $formulario->titulo }}</span>
        </h1>

        <a href="{{ route('formularios.concentrarRespuestas', $formulario->id) }}"
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg shadow-md transition transform hover:-translate-y-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 4v16h16V4H4zm8 4v8m0 0l-4-4m4 4l4-4"/>
            </svg>
            Descargar Excel
        </a>
    </div>

    {{-- Estadísticas por sección y pregunta --}}
    @php $preguntaIndex = 1; @endphp
    <div class="space-y-6">
        @foreach ($formulario->secciones as $seccion)
            <section class="bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-green-700">{{ $seccion->titulo }}</h2>
                </div>

                <div class="p-6 space-y-6">
                    @foreach ($seccion->preguntas as $pregunta)
                        @php
                            // Tipo legible
                            $tipo = $pregunta->tipo ?? $pregunta->tipo_pregunta ?? 'desconocido';
                            switch ($tipo) {
                                case 'opcion_simple': $tipoLabel = 'Opción simple'; break;
                                case 'opcion_multiple': $tipoLabel = 'Opción múltiple'; break;
                                case 'casillas': $tipoLabel = 'Casillas (checkbox)'; break;
                                case 'texto': $tipoLabel = 'Respuesta abierta (texto)'; break;
                                case 'fecha': $tipoLabel = 'Fecha'; break;
                                default: $tipoLabel = ucfirst(str_replace('_',' ',$tipo)); break;
                            }
                        @endphp

                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-9 h-9 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-semibold">
                                            {{ $preguntaIndex }}
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-gray-800 font-semibold">{{ $pregunta->texto }}</p>
                                        <p class="text-sm text-gray-500 mt-1">Tipo: <span class="font-medium text-gray-600">{{ $tipoLabel }}</span></p>
                                    </div>
                                </div>

                                {{-- Total respuestas (si aplica) --}}
                                <div class="text-right">
                                    @php
                                        // Intentamos obtener total desde $estadisticas si existe
                                        $totalOpciones = 0;
                                        if (!empty($estadisticas[$pregunta->id]) && is_array($estadisticas[$pregunta->id])) {
                                            foreach ($estadisticas[$pregunta->id] as $d) {
                                                $totalOpciones += isset($d['conteo']) ? (int)$d['conteo'] : 0;
                                            }
                                        } else {
                                            // calcular total recorriendo respuestas (solo si no hay estadisticas)
                                            foreach ($formulario->respuestas as $r) {
                                                $ri = $r->respuestasIndividuales ?? collect();
                                                if (!is_a($ri, 'Illuminate\\Support\\Collection')) $ri = collect($ri);
                                                $riFor = $ri->where('pregunta_id', $pregunta->id);
                                                foreach ($riFor as $it) {
                                                    if (!empty($it->opcion_id) || (!empty($it->opcion) && !empty($it->opcion->texto)) || !empty($it->texto)) {
                                                        $totalOpciones++;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    <div class="text-sm text-gray-500">Total respuestas: <span class="font-semibold text-gray-700">{{ $totalOpciones }}</span></div>
                                </div>
                            </div>

                            <div class="mt-4">
                                {{-- Preguntas con opciones (estadísticas pre-calculadas) --}}
                                @if (!empty($estadisticas[$pregunta->id]) && is_array($estadisticas[$pregunta->id]) && count($estadisticas[$pregunta->id]) > 0)
                                    @php
                                        $total = 0;
                                        foreach ($estadisticas[$pregunta->id] as $d) { $total += isset($d['conteo']) ? (int)$d['conteo'] : 0; }
                                    @endphp

                                    <ul class="space-y-3">
                                        @foreach ($estadisticas[$pregunta->id] as $dato)
                                            @php
                                                $label = $dato['opcion'] ?? 'Opción';
                                                $count = isset($dato['conteo']) ? (int)$dato['conteo'] : 0;
                                                $pct = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                                            @endphp
                                            <li class="flex items-center justify-between gap-4">
                                                <div class="w-3/5">
                                                    <div class="text-sm text-gray-700 font-medium">{{ $label }}</div>
                                                    <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                        <div class="h-2 bg-green-500 rounded-full" style="width: {{ $pct }}%"></div>
                                                    </div>
                                                </div>
                                                <div class="w-2/5 text-right">
                                                    <div class="text-sm text-gray-600">{{ $count }} respuestas</div>
                                                    <div class="text-xs text-gray-400">{{ $pct }}%</div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>

                                {{-- Preguntas tipo opción pero sin estadisticas pre-calculadas: calcular conteos --}}
                                @elseif(in_array($tipo, ['opcion_simple','opcion_multiple','casillas']))
                                    @php
                                        $conteos = [];
                                        $totalCalc = 0;
                                        foreach ($formulario->respuestas as $r) {
                                            $ri = $r->respuestasIndividuales ?? collect();
                                            if (!is_a($ri, 'Illuminate\\Support\\Collection')) $ri = collect($ri);
                                            $riFor = $ri->where('pregunta_id', $pregunta->id);
                                            foreach ($riFor as $it) {
                                                if (!empty($it->opcion_id)) {
                                                    $key = 'id_'.$it->opcion_id;
                                                    $label = $it->opcion->texto ?? ('Opción '.$it->opcion_id);
                                                    $conteos[$key]['label'] = $label;
                                                    $conteos[$key]['count'] = ($conteos[$key]['count'] ?? 0) + 1;
                                                    $totalCalc++;
                                                } elseif (!empty($it->opcion) && !empty($it->opcion->texto)) {
                                                    $label = $it->opcion->texto;
                                                    $key = 'txt_'.md5($label);
                                                    $conteos[$key]['label'] = $label;
                                                    $conteos[$key]['count'] = ($conteos[$key]['count'] ?? 0) + 1;
                                                    $totalCalc++;
                                                } elseif (!empty($it->texto)) {
                                                    $label = $it->texto;
                                                    $key = 'txt_'.md5($label);
                                                    $conteos[$key]['label'] = $label;
                                                    $conteos[$key]['count'] = ($conteos[$key]['count'] ?? 0) + 1;
                                                    $totalCalc++;
                                                }
                                            }
                                        }
                                    @endphp

                                    @if(count($conteos) > 0)
                                        <ul class="space-y-3">
                                            @foreach ($conteos as $c)
                                                @php $pct = $totalCalc > 0 ? round(($c['count'] / $totalCalc) * 100, 1) : 0; @endphp
                                                <li class="flex items-center justify-between gap-4">
                                                    <div class="w-3/5">
                                                        <div class="text-sm text-gray-700 font-medium">{{ $c['label'] }}</div>
                                                        <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                            <div class="h-2 bg-green-500 rounded-full" style="width: {{ $pct }}%"></div>
                                                        </div>
                                                    </div>
                                                    <div class="w-2/5 text-right">
                                                        <div class="text-sm text-gray-600">{{ $c['count'] }} respuestas</div>
                                                        <div class="text-xs text-gray-400">{{ $pct }}%</div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="text-sm text-gray-500 ml-8">No hay respuestas registradas para esta pregunta.</div>
                                    @endif

                                {{-- Preguntas abiertas (texto) --}}
                                @else
                                    @php
                                        $countOpen = 0;
                                        $examples = [];
                                        foreach ($formulario->respuestas as $r) {
                                            $ri = $r->respuestasIndividuales ?? collect();
                                            if (!is_a($ri, 'Illuminate\\Support\\Collection')) $ri = collect($ri);
                                            $riFor = $ri->where('pregunta_id', $pregunta->id);
                                            foreach ($riFor as $it) {
                                                if (!empty($it->texto) && trim($it->texto) !== '') {
                                                    $countOpen++;
                                                    if (count($examples) < 5) $examples[] = trim($it->texto);
                                                }
                                            }
                                        }
                                    @endphp

                                    <div class="ml-6 text-gray-700">
                                        <div class="text-sm">Respuestas recibidas: <span class="font-semibold text-gray-800">{{ $countOpen }}</span></div>
                                        @if(count($examples) > 0)
                                            <div class="mt-3">
                                                <div class="text-sm text-gray-600 font-medium">Ejemplos</div>
                                                <ul class="list-disc ml-5 mt-2 text-sm text-gray-600 space-y-1">
                                                    @foreach($examples as $ex) <li>{{ $ex }}</li> @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        @php $preguntaIndex++; @endphp
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>

    {{-- Respuestas individuales (tabla compacta) --}}
    <div class="mt-8">
        <h3 class="text-lg font-semibold mb-4">Respuestas individuales</h3>
        <div class="overflow-x-auto bg-white rounded-lg border border-gray-100 shadow-sm">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-gray-600">ID</th>
                        <th class="px-3 py-2 text-left text-gray-600">Usuario</th>
                        <th class="px-3 py-2 text-left text-gray-600">Correo</th>
                        <th class="px-3 py-2 text-left text-gray-600">Fecha</th>
                        @foreach ($formulario->secciones as $s)
                            @foreach ($s->preguntas as $p)
                                <th class="px-3 py-2 text-left text-gray-600">{{ Str::limit($p->texto, 30) }}</th>
                            @endforeach
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @php $anon = 1; @endphp
                    @foreach ($formulario->respuestas as $resp)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $resp->id }}</td>
                            @if($formulario->permitir_anonimo)
                                <td class="px-3 py-2">Persona {{ $anon++ }}</td>
                                <td class="px-3 py-2">N/A</td>
                            @else
                                <td class="px-3 py-2">{{ optional($resp->usuario)->name ?? 'Sin nombre' }}</td>
                                <td class="px-3 py-2">{{ optional($resp->usuario)->email ?? 'N/A' }}</td>
                            @endif
                            <td class="px-3 py-2">
                                {{ $resp->enviado_en ? \Carbon\Carbon::parse($resp->enviado_en)->format('d/m/Y H:i') : 'N/A' }}
                            </td>

                            @foreach ($formulario->secciones as $s)
                                @foreach ($s->preguntas as $p)
                                    @php
                                        $ri = $resp->respuestasIndividuales ?? collect();
                                        if (!is_a($ri, 'Illuminate\\Support\\Collection')) $ri = collect($ri);
                                        $riFor = $ri->where('pregunta_id', $p->id);

                                        $vals = [];
                                        foreach ($riFor as $it) {
                                            // Pregunta abierta
                                            if (!empty($it->texto_respuesta)) {
                                                $vals[] = $it->texto_respuesta;
                                                continue;
                                            }

                                            // Escala lineal
                                            if (!empty($it->valor_numerico)) {
                                                $vals[] = $it->valor_numerico;
                                                continue;
                                            }

                                            // Opción seleccionada
                                            if (!empty($it->opcion) && !empty($it->opcion->texto)) {
                                                $vals[] = $it->opcion->texto;
                                                continue;
                                            }

                                            // Fallback: opcion_id
                                            if (!empty($it->opcion_id)) {
                                                $vals[] = 'Opción #' . $it->opcion_id;
                                                continue;
                                            }

                                            // Fechas/horas si aplica
                                            if (!empty($it->valor_fecha)) {
                                                $vals[] = $it->valor_fecha;
                                                continue;
                                            }
                                            if (!empty($it->valor_hora)) {
                                                $vals[] = $it->valor_hora;
                                                continue;
                                            }
                                        }

                                        $display = count($vals) ? implode('; ', $vals) : 'Sin respuesta';
                                    @endphp
                                    <td class="px-3 py-2">{{ Str::limit($display, 80) }}</td>
                                @endforeach
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


</div>
@endsection