@extends('layouts.app')

@section('content')
<div class="p-6">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-[#025742] drop-shadow">
            📋 Lista de Formularios
        </h1>

        {{-- Botón Crear Nuevo Formulario --}}
        <a href="{{ route('formularios.crear') }}"
           class="inline-flex items-center gap-2 bg-[#025742] hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition-all duration-200 transform hover:scale-105">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Formulario
        </a>
    </div>

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-[#025742] text-white">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Título</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Respuestas</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse ($formularios as $form)
                <tr class="transition transform hover:scale-[1.02] hover:bg-green-50">
                    <td class="px-4 py-4 text-gray-800 font-medium">
                        {{ $form->titulo }}
                    </td>

                    {{-- Estado: mostrar Activo/Inactivo según BD --}}
                    <td class="px-4 py-4 text-gray-700 font-semibold">
                        {{ $form->activo == 1 ? 'Activo' : 'Inactivo' }}
                    </td>

                    <td class="px-4 py-4 text-gray-600">
                        {{ $form->respuestas_count }}
                    </td>

                    <td class="px-4 py-4 text-right space-x-2">
                        {{-- Botones de acción --}}
                        <a href="{{ route('formularios.editar', $form->id) }}" 
                           class="inline-block bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded shadow transition">
                           Editar
                        </a>

                        <a href="#"
                           onclick="copiarEnlace('{{ route('formularios.acceder', $form->token) }}')"
                           class="inline-block bg-indigo-500 hover:bg-indigo-600 text-white text-xs px-3 py-1 rounded shadow transition">
                           Ver enlace
                        </a>

                        <a href="{{ route('formularios.concentrado', $form->id) }}" 
                           class="inline-block bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1 rounded shadow transition">
                           Respuestas
                        </a>

                        <form action="#" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded shadow transition">
                                Eliminar
                            </button>
                        </form>

                        {{-- Configuración --}}
                        <a href="{{ route('formularios.configuracion', ['id' => $form->id, 'from' => 'index']) }}"
                           class="inline-flex items-center justify-center w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-full shadow transition"
                           title="Configuración del formulario">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.75 3a.75.75 0 01.75-.75h3a.75.75 0 01.75.75v1.5a6.75 6.75 0 012.25 1.3l1.06-.61a.75.75 0 011.02.27l1.5 2.6a.75.75 0 01-.27 1.02l-1.06.61c.17.46.3.95.37 1.46h1.5a.75.75 0 01.75.75v3a.75.75 0 01-.75.75h-1.5a6.75 6.75 0 01-.37 1.46l1.06.61a.75.75 0 01.27 1.02l-1.5 2.6a.75.75 0 01-1.02.27l-1.06-.61a6.75 6.75 0 01-2.25 1.3v1.5a.75.75 0 01-.75.75h-3a.75.75 0 01-.75-.75v-1.5a6.75 6.75 0 01-2.25-1.3l-1.06.61a.75.75 0 01-1.02-.27l-1.5-2.6a.75.75 0 01.27-1.02l1.06-.61a6.75 6.75 0 01-.37-1.46H3.75a.75.75 0 01-.75-.75v-3a.75.75 0 01.75-.75h1.5c.07-.51.2-1 .37-1.46l-1.06-.61a.75.75 0 01-.27-1.02l1.5-2.6a.75.75 0 011.02-.27l1.06.61A6.75 6.75 0 018.25 4.5V3z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                        No hay formularios creados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function copiarEnlace(url) {
        navigator.clipboard.writeText(url).then(function() {
            alert("Enlace copiado: " + url);
        }, function(err) {
            console.error("Error al copiar enlace: ", err);
        });
    }
</script>
@endsection