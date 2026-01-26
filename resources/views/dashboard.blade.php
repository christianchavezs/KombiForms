@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="p-6 dashboard-wrapper">

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="toast-ios toast-success show">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="toast-ios toast-error show">
            <i class="bi bi-x-circle-fill"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif


    
    {{-- Encabezado y botón --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-[#025742] drop-shadow">
            📊 Dashboard
        </h1>

        {{-- Botón Crear Nuevo Formulario --}}
        <a href="{{ route('formularios.crear', ['from' => 'dashboard']) }}"
            class="inline-flex items-center gap-2 bg-[#025742] hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition-all duration-200 transform hover:scale-105">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Formulario
        </a>
    </div>

    

    {{-- Tarjetas superiores --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card-stat">
            <div class="card-icon bg-green-100 text-green-600">
                <i class="bi bi-file-earmark-text"></i>
            </div>
            <div>
                <h2 class="card-label">Formularios</h2>
                <p class="card-value">{{ $totalFormularios }}</p>
            </div>
        </div>

        <div class="card-stat">
            <div class="card-icon bg-blue-100 text-blue-600">
                <i class="bi bi-chat-left-text"></i>
            </div>
            <div>
                <h2 class="card-label">Respuestas Recibidas</h2>
                <p class="card-value">{{ $totalRespuestas }}</p>
            </div>
        </div>

        <div class="card-stat">
            <div class="card-icon bg-yellow-100 text-yellow-600">
                <i class="bi bi-question-circle"></i>
            </div>
            <div>
                <h2 class="card-label">Preguntas Totales</h2>
                <p class="card-value">{{ $totalPreguntas }}</p>
            </div>
        </div>

        <div class="card-stat">
            <div class="card-icon bg-purple-100 text-purple-600">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div>
                <h2 class="card-label">Formularios Activos</h2>
                <p class="card-value">{{ $formulariosActivos }}</p>
            </div>
        </div>
    </div>

    {{-- Gráfica --}}
    <div class="card-graph mb-8">
        <h2 class="graph-title">Respuestas por Formulario</h2>
        <canvas id="respuestasChart" class="graph-canvas"></canvas>
    </div>
    

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Últimos formularios --}}
        <div class="card-list">
            <h2 class="list-title">Últimos Formularios</h2>
            <ul class="divide-y divide-gray-100">
                @forelse ($ultimosFormularios as $form)
                <li class="py-4 flex items-center justify-between hover:bg-gray-50 transition">
                    <div>
                        <p class="text-gray-900 font-medium">{{ $form->titulo }}</p>
                        <p class="text-gray-500 text-sm">Respuestas: {{ $form->respuestas_count }}</p>
                    </div>
                    @php
                        $hoy = now();
                        $activo = (!$form->fecha_inicio || $form->fecha_inicio <= $hoy) &&
                                  (!$form->fecha_fin || $form->fecha_fin >= $hoy);
                    @endphp
                    <span class="status-pill {{ $activo ? 'activo' : 'inactivo' }}">
                        {{ $activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </li>
                @empty
                <p class="text-gray-500 text-sm">No hay formularios recientes.</p>
                @endforelse
            </ul>
        </div>

        {{-- Últimas respuestas --}}
        <div class="card-list">
            <h2 class="list-title">Últimas Respuestas</h2>
            <ul class="divide-y divide-gray-100">
                @forelse ($ultimasRespuestas as $respuesta)
                <li class="py-4 hover:bg-gray-50 transition">
                    <p class="text-gray-800 font-medium">{{ $respuesta->formulario->titulo }}</p>
                    <p class="text-gray-500 text-sm">
                        Recibida: {{ \Carbon\Carbon::parse($respuesta->enviado_en)->format('d/m/Y H:i') }}
                    </p>
                </li>
                @empty
                <p class="text-gray-500 text-sm">No hay respuestas recientes.</p>
                @endforelse
            </ul>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const formularios = @json($graficaData);

const labels = formularios.map(f => f.titulo);
const dataRespuestas = formularios.map(f => f.respuestas);
const estados = formularios.map(f => f.estatus);

// Colores según estado
const colores = estados.map(e => e === 'Activo' ? 'rgba(25,135,84,0.7)' : 'rgba(220,53,69,0.7)');

const ctx = document.getElementById('respuestasChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Respuestas',
            data: dataRespuestas,
            backgroundColor: colores,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const index = context.dataIndex;
                        return `${labels[index]}: ${dataRespuestas[index]} respuestas (${estados[index]})`;
                    }
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    callback: function(value, index) {
                        return labels[index] + " (" + estados[index] + ")";
                    }
                }
            },
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});
</script>

<style>
.dashboard-wrapper { animation: fadeIn 0.6s ease-in-out; }
@keyframes fadeIn { from {opacity:0; transform:translateY(10px);} to {opacity:1; transform:translateY(0);} }

.card-stat {
    display: flex; align-items: center; gap: 1rem;
    background: #fff; border-radius: 1rem; padding: 1.2rem;
    box-shadow: 0 6px 18px rgba(0,0,0,.06);
    transition: transform .3s ease;
}
.card-stat:hover { transform: translateY(-4px); }
.card-icon {
    display: flex; align-items: center; justify-content: center;
    width: 48px; height: 48px; border-radius: 50%;
    font-size: 1.4rem;
}
.card-label { font-size: .9rem; color: #6c757d; }
.card-value { font-size: 1.8rem; font-weight: 700; color: #333; }

.card-graph {
    background: #fff; border-radius: 1rem; padding: 1.5rem;
    box-shadow: 0 6px 18px rgba(0,0,0,.06);
}
.graph-title { font-size: 1.2rem; font-weight: 600; margin-bottom: 1rem; }
.graph-canvas { width: 100%; height: 300px; }

.card-list {
    background: #fff; border-radius: 1rem; padding: 1.5rem;
    box-shadow: 0 6px 18px rgba(0,0,0,.06);
}
.list-title { font-size: 1.2rem; font-weight: 600; margin-bottom: 1rem; }

.status-pill {
    padding: .3rem .8rem; border-radius: 999px; font-size: .75rem; font-weight: 600;
}
.status-pill.activo { background: rgba(25,135,84,.15); color: #198754; }
.status-pill.inactivo { background: rgba(220,53,69,.15); color: #dc3545; }

/* Toast estilo iOS */
.toast-ios {
    position: fixed; top: 20px; right: 20px;
    padding: .8rem 1.2rem; border-radius: .75rem;
    display: flex; align-items: center; gap: .5rem;
    font-weight: 600; color: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,.2);
    z-index: 9999; opacity: .95; transition: all .4s ease;
}
.toast-success { background: #198754; }
.toast-error { background: #dc3545; }
.toast-ios.hide { opacity: 0; transform: translateY(-20px); }
</style>
@endsection