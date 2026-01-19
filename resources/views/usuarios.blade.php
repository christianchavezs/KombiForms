@extends('layouts.app')

@section('content')
{{-- BOOTSTRAP CSS --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

{{-- BOOTSTRAP ICONS --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">


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


<div class="card border-0 shadow-lg rounded-4 card-pro">
    <div class="card-body p-4">

        <form id="filtrosForm" method="GET" action="{{ route('Usuarios') }}">
            <div class="row g-3 align-items-end mb-4">

                <div class="col-md-2">
                    <label class="form-label small text-secondary fw-semibold">
                        <i class="bi bi-plus-circle me-1"></i> Acción
                    </label>

                    <a href="{{ route('register') }}"
                    class="btn btn-sm btn-primary w-100 btn-pro-add">
                        <i class="bi bi-person-plus-fill me-1"></i>
                        Agregar usuario
                    </a>
                </div>

                {{-- Mostrar --}}
                <div class="col-md-2">
                    <label class="form-label small text-secondary fw-semibold">
                        <i class="bi bi-list-ul me-1"></i> Mostrar
                    </label>
                    <select class="form-select form-select-sm input-pro"
                            name="mostrar"
                            onchange="this.form.submit()">
                        <option value="25" {{ request('mostrar',25)==25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('mostrar')==50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('mostrar')==100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                {{-- Estatus --}}
                <div class="col-md-2">
                    <label class="form-label small text-secondary fw-semibold">
                        <i class="bi bi-toggle-on me-1"></i> Estatus
                    </label>
                    <select class="form-select form-select-sm input-pro"
                            name="estatus"
                            onchange="this.form.submit()">
                        <option value="Todos" {{ request('estatus','Todos')=='Todos' ? 'selected' : '' }}>Todos</option>
                        <option value="Activos" {{ request('estatus')=='Activos' ? 'selected' : '' }}>Activos</option>
                        <option value="Inactivos" {{ request('estatus')=='Inactivos' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>

                {{-- Buscar --}}
                <div class="col-md-4 ms-auto">
                    <label class="form-label small text-secondary fw-semibold">
                        <i class="bi bi-search me-1"></i> Buscar usuario
                    </label>
                    <div class="position-relative">
                        <i class="bi bi-search buscador-icon"></i>
                        <input type="text"
                               id="buscarInput"
                               name="buscar"
                               value="{{ request('buscar') }}"
                               class="form-control form-control-sm input-pro buscador-input"
                               placeholder="Correo o nombre">
                    </div>
                </div>

            </div>
        </form>

        <div id="tablaUsuariosContainer" class="fade-in">
            @include('profile.partials.tabla_usuario',['usuarios'=>$usuarios])
        </div>

    </div>
</div>

{{-- BUSCADOR DINÁMICO --}}
<script>
let timeout = null;

document.getElementById('buscarInput').addEventListener('keyup', function () {
    clearTimeout(timeout);

    timeout = setTimeout(() => {
        const form = document.getElementById('filtrosForm');
        const params = new URLSearchParams(new FormData(form));

        fetch(form.action + '?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.text())
        .then(html => {
            const container = document.getElementById('tablaUsuariosContainer');
            container.classList.remove('fade-in');
            container.innerHTML = html;
            void container.offsetWidth;
            container.classList.add('fade-in');
        });
    }, 350);
});

setTimeout(() => {
    document.querySelectorAll('.toast-ios').forEach(toast => {
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 400);
    });
}, 3500);

</script>

{{-- ESTILO PRO --}}
<style>
/* TOAST iOS */
.toast-ios {
    position: fixed;
    top: 24px;
    right: 24px;
    min-width: 280px;
    max-width: 360px;
    padding: 14px 18px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 500;
    font-size: .9rem;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    box-shadow: 0 10px 30px rgba(0,0,0,.15);
    z-index: 9999;
    animation: slideIn .4s ease-out;
}

/* Success */
.toast-success {
    background: rgba(25, 135, 84, .85);
    color: #fff;
}

/* Error */
.toast-error {
    background: rgba(220, 53, 69, .9);
    color: #fff;
}

/* Icon */
.toast-ios i {
    font-size: 1.3rem;
}

/* Animación entrada */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Animación salida */
.toast-ios.hide {
    animation: slideOut .35s ease-in forwards;
}

@keyframes slideOut {
    to {
        opacity: 0;
        transform: translateX(20px);
    }
}



:root {
    --primary: #0d6efd;
    --primary-soft: rgba(13,110,253,.12);
    --dark-soft: #495057;
}

/* Card */
.card-pro {
    background: linear-gradient(180deg,#ffffff,#f8f9fa);
}

/* Inputs */
.input-pro {
    border-radius: 14px;
    border: 1px solid #dee2e6;
    transition: all .25s ease;
    background-color: #fff;
}

.input-pro:hover {
    border-color: var(--primary);
}

.input-pro:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 .25rem var(--primary-soft);
}

/* Buscador */
.buscador-input {
    padding-left: 42px;
    font-weight: 500;
}

.buscador-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1rem;
    color: var(--primary);
    opacity: .75;
    pointer-events: none;
}

/* Animación suave */
.fade-in {
    animation: fadeIn .25s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(4px);
    }
    to {
        opacity: 1;
        transform: none;
    }
}
</style>

@endsection
