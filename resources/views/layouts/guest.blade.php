{{--Esta plantilla esta trabajando unicamente para el login y el aviso de verificacion del correo--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Vite CSS y JS -->
    @vite(['resources/css/variables.css', 'resources/css/auth.css', 'resources/js/app.js', 'resources/js/scripts.js'])

</head>

<body class="d-flex flex-column min-vh-100 bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top shadow-sm">
        <div class="container position-relative d-flex align-items-center justify-content-between">
            <!-- Logo a la izquierda -->
            <a class="navbar-brand d-flex align-items-center gap-2 mb-0" href="http://www.kombitec.com.mx/">
                <img src="{{ asset('assets/img/logoKBT.png') }}" alt="Logo Kombitec" height="40" />
            </a>

            <!-- Título centrado absoluto -->
            <span class="brand-title position-absolute top-50 start-50 translate-middle text-center">
                KombiForms
            </span>

        </div>
    </nav>

    {{--Se carga la vista de login y la vista de verificar email--}}
    <main class="d-flex flex-grow-1 justify-content-center align-items-center bg-light">
        {{-- ALERTAS GLOBALES --}}
        @include('components.alerts')
        {{ $slot }} 
    </main>

    <footer class="py-4 mt-auto navbar-custom text-center">
        <div class="container">
            <p class="text-white mb-0 small">© {{ date('Y') }} Kombitec</p>
        </div>
    </footer>
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>