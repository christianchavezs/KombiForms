@extends('layouts.app')

@section('content')
<div class="p-6">

    {{-- Título --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-semibold text-gray-800">Formularios</h1>

        <a href="{{ route('formularios.create') }}"
           class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow">
            + Nuevo Formulario
        </a>
    </div>

    {{-- Tabla --}}
    <div class="bg-white shadow rounded-2xl border border-gray-100 p-6">

        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Título</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Respuestas</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

                @forelse ($formularios as $form)

                    @php
                        $hoy = now();
                        $estado = '';

                        if ($form->fecha_inicio && $form->fecha_inicio > $hoy) {
                            $estado = 'Programado';
                        } elseif ($form->fecha_fin && $form->fecha_fin < $hoy) {
                            $estado = 'Cerrado';
                        } else {
                            $estado = 'Activo';
                        }

                        $color = [
                            'Activo' => 'bg-green-100 text-green-700',
                            'Cerrado' => 'bg-red-100 text-red-700',
                            'Programado' => 'bg-yellow-100 text-yellow-700'
                        ][$estado];
                    @endphp

                    <tr>
                        {{-- Título --}}
                        <td class="px-4 py-4 text-gray-800 font-medium">
                            {{ $form->titulo }}
                        </td>

                        {{-- Estado --}}
                        <td class="px-4 py-4">
                            <span class="px-3 py-1 text-xs rounded-full {{ $color }}">
                                {{ $estado }}
                            </span>
                        </td>

                        {{-- Respuestas --}}
                        <td class="px-4 py-4 text-gray-700">
                            {{ $form->respuestas_count }}
                        </td>

                        {{-- Acciones --}}
                        <td class="px-4 py-4 text-right space-x-2">

                            {{-- Editar --}}
                            <a href="{{ route('formularios.edit', $form->id) }}"
                                class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-sm">
                                Editar
                            </a>

                            {{-- Enlaces --}}
                            <a href="{{ route('formularios.links', $form->id) }}"
                                class="px-3 py-1 bg-indigo-500 hover:bg-indigo-600 text-white rounded-md text-sm">
                                Enlaces
                            </a>

                            {{-- Ver Respuestas --}}
                            <a href="{{ route('formularios.respuestas', $form->id) }}"
                                class="px-3 py-1 bg-gray-700 hover:bg-gray-800 text-white rounded-md text-sm">
                                Respuestas
                            </a>

                            {{-- Eliminar --}}
                            <form action="{{ route('formularios.destroy', $form->id) }}"
                                  method="POST" class="inline">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    onclick="return confirm('¿Seguro que deseas eliminar este formulario?')"
                                    class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded-md text-sm">
                                    Eliminar
                                </button>
                            </form>

                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="4" class="text-center py-6 text-gray-500">
                            No hay formularios creados.
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>

    </div>
</div>
@endsection
