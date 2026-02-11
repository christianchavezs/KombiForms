@extends('layouts.app')

@section('title', 'Formulario ya contestado')

@section('content')
<div class="p-6 flex flex-col items-center justify-center text-center min-h-[60vh]">
    <h1 class="text-3xl font-bold text-yellow-600 mb-4">
        ⚠️ Formulario ya contestado
    </h1>
    <p class="text-gray-700 mb-6">
        Este formulario ya ha sido contestado anteriormente con tu correo electrónico.
    </p>

    <a href="{{ route('dashboard') }}" 
       class="mt-6 inline-flex items-center gap-2 bg-[#025742] hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition-all duration-200 transform hover:scale-105">
        <i class="bi bi-house-door"></i>
        Volver al Dashboard
    </a>
</div>
@endsection