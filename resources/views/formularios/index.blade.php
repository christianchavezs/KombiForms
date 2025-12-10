@extends('layouts.app')

@section('content')
<div class="p-6">

    <h1 class="text-3xl font-semibold text-gray-800 mb-6">Lista de Formularios</h1>

    <div class="bg-white shadow rounded-2xl p-6 border border-gray-100">

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Título</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Respuestas</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse ($formularios as $form)
                <tr>
                    <td class="px-4 py-4 text-gray-800 font-medium">
                        {{ $form->titulo }}
                    </td>

                    {{-- Cálculo de estado --}}
                    @php
                        $hoy = now();
                        if ($form->fecha_inicio && $form->fecha_inicio > $hoy) {
                            $estado = 'Programado';
                            $color = 'bg-blue-100 text-blue-700';
                        } elseif ($form->fecha_fin && $form->fecha_fin < $hoy) {
                            $estado = 'Cerrado';
                            $color = 'bg-red-100 text-red-700';
                        } else {
                            $estado = 'Activo';
                            $color = 'bg-green-100 text-green-700';
                        }
                    @endphp

                    <td class="px-4 py-4">
                        <span class="px-3 py-1 rounded-full text-xs {{ $color }}">
                            {{ $estado }}
                        </span>
                    </td>

                    <td class="px-4 py-4 text-gray-600">
                        {{ $form->respuestas_count }}
                    </td>

                    <td class="px-4 py-4 text-right space-x-2">

                        <a href="{{ route('formularios.editar', $form->id) }}" 
                        class="text-blue-600 hover:underline text-sm">
                        Editar
                        </a>
                        <a href="#" class="text-indigo-600 hover:underline text-sm">Ver enlaces</a>
                        <a href="#" class="text-green-600 hover:underline text-sm">Respuestas</a>

                        <form action="#" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:underline text-sm">
                                Eliminar
                            </button>
                        </form>

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
@endsection
