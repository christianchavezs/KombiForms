@extends('layouts.app')

@section('content')

{{-- ========== TOASTS MODERNOS ========== --}}
@if(session('success'))
    <div class="toast-modern toast-success show">
        <div class="toast-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div class="toast-content">
            <span class="toast-title">Exito!</span>
            <span class="toast-message">{{ session('success') }}</span>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="toast-modern toast-error show">
        <div class="toast-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
        </div>
        <div class="toast-content">
            <span class="toast-title">Error</span>
            <span class="toast-message">{{ session('error') }}</span>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>
@endif

{{-- ========== FONDO DECORATIVO ========== --}}
<div class="page-background">
    <div class="bg-gradient"></div>
    <div class="bg-pattern"></div>
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
</div>

{{-- ========== CONTENEDOR PRINCIPAL ========== --}}
<div class="main-container">
    
    {{-- Header de la seccion --}}
    <div class="section-header">
        <div class="header-content">
            <div class="header-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div class="header-text">
                <h1 class="page-title">Gestion de Usuarios</h1>
                <p class="page-subtitle">Administra y controla los usuarios del sistema</p>
            </div>
        </div>
        <div class="header-stats">
            <div class="stat-item">
                <span class="stat-number">{{ $usuarios->total() ?? '0' }}</span>
                <span class="stat-label">Total usuarios</span>
            </div>
        </div>
    </div>

    {{-- Card principal --}}
    <div class="card-glass">
        <div class="card-shine"></div>
        
        {{-- SECCION DEL BOTON AGREGAR - PROMINENTE --}}
        <div class="action-hero-section">
            <div class="action-hero-content">
                <div class="action-hero-text">
                    <h2 class="action-hero-title">Nuevo Usuario</h2>
                    <p class="action-hero-subtitle">Registra un nuevo usuario en el sistema</p>
                </div>
                <a href="{{ route('register') }}" class="btn-hero-add">
                    <span class="btn-hero-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="8.5" cy="7" r="4"/>
                            <line x1="20" y1="8" x2="20" y2="14"/>
                            <line x1="23" y1="11" x2="17" y2="11"/>
                        </svg>
                    </span>
                    <span class="btn-hero-text">Agregar Usuario</span>
                    <span class="btn-hero-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </span>
                </a>
            </div>
            <div class="action-hero-decoration">
                <div class="decoration-circle circle-1"></div>
                <div class="decoration-circle circle-2"></div>
                <div class="decoration-circle circle-3"></div>
            </div>
        </div>

        {{-- SECCION DE FILTROS - SEPARADA --}}
        <form id="filtrosForm" method="GET" action="{{ route('Usuarios') }}">
            <div class="filters-section">
                <div class="filters-header">
                    <div class="filters-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                        </svg>
                        <span>Filtros de busqueda</span>
                    </div>
                    <button type="button" class="filters-toggle" onclick="toggleFilters()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                </div>
                
                <div class="filters-body" id="filtersBody">
                    <div class="filters-grid">
                        {{-- Select mostrar --}}
                        <div class="filter-item">
                            <label class="filter-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/>
                                    <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
                                </svg>
                                Mostrar registros
                            </label>
                            <div class="select-wrapper">
                                <select name="mostrar" onchange="this.form.submit()" class="input-modern">
                                    <option value="25" {{ request('mostrar',25)==25 ? 'selected' : '' }}>25 registros</option>
                                    <option value="50" {{ request('mostrar')==50 ? 'selected' : '' }}>50 registros</option>
                                    <option value="100" {{ request('mostrar')==100 ? 'selected' : '' }}>100 registros</option>
                                </select>
                                <div class="select-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Select estatus --}}
                        <div class="filter-item">
                            <label class="filter-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                                Estado del usuario
                            </label>
                            <div class="select-wrapper">
                                <select name="estatus" onchange="this.form.submit()" class="input-modern">
                                    <option value="Todos" {{ request('estatus','Todos')=='Todos' ? 'selected' : '' }}>Todos los estados</option>
                                    <option value="Activos" {{ request('estatus')=='Activos' ? 'selected' : '' }}>Activos</option>
                                    <option value="Inactivos" {{ request('estatus')=='Inactivos' ? 'selected' : '' }}>Inactivos</option>
                                </select>
                                <div class="select-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Campo de busqueda --}}
                        <div class="filter-item filter-search">
                            <label class="filter-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                </svg>
                                Buscar usuario
                            </label>
                            <div class="search-input-wrapper">
                                <div class="search-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                    </svg>
                                </div>
                                <input type="text"
                                       id="buscarInput"
                                       name="buscar"
                                       value="{{ request('buscar') }}"
                                       class="input-modern input-search"
                                       placeholder="Escribe un correo o nombre...">
                                <div class="search-loader" id="searchLoader">
                                    <div class="loader-spinner"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- Contenedor de la tabla --}}
        <div id="tablaUsuariosContainer" class="table-wrapper fade-in">
            @include('profile.partials.tabla_usuario',['usuarios'=>$usuarios])
        </div>

    </div>

    {{-- Footer decorativo --}}
    <div class="card-footer-info">
        <div class="footer-left">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
            <span>Ultima actualizacion: {{ now()->format('d/m/Y H:i') }}</span>
        </div>
        <div class="footer-right">
            <span class="status-dot"></span>
            <span>Sistema activo</span>
        </div>
    </div>

</div>

<script>
let timeout = null;
const searchLoader = document.getElementById('searchLoader');
const buscarInput = document.getElementById('buscarInput');

buscarInput.addEventListener('keyup', function () {
    clearTimeout(timeout);
    searchLoader.classList.add('active');

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
            searchLoader.classList.remove('active');
        })
        .catch(() => {
            searchLoader.classList.remove('active');
        });
    }, 350);
});

// Auto-hide toasts
setTimeout(() => {
    document.querySelectorAll('.toast-modern').forEach(toast => {
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 500);
    });
}, 4000);

// Toggle filtros
function toggleFilters() {
    const filtersBody = document.getElementById('filtersBody');
    const toggleBtn = document.querySelector('.filters-toggle');
    filtersBody.classList.toggle('collapsed');
    toggleBtn.classList.toggle('rotated');
}

// Efecto ripple en botones
document.querySelectorAll('.btn-hero-add').forEach(btn => {
    btn.addEventListener('mouseenter', function(e) {
        const rect = btn.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        btn.style.setProperty('--mouse-x', x + 'px');
        btn.style.setProperty('--mouse-y', y + 'px');
    });
});
</script>

<style>
/* ==========================================
   VARIABLES Y COLORES
   ========================================== */
:root {
    --verde-primary: #059669;
    --verde-dark: #047857;
    --verde-darker: #065f46;
    --verde-light: #10b981;
    --verde-lighter: #34d399;
    --verde-pale: #d1fae5;
    --verde-glow: rgba(16, 185, 129, 0.4);
    
    --emerald-50: #ecfdf5;
    --emerald-100: #d1fae5;
    --emerald-200: #a7f3d0;
    --emerald-500: #10b981;
    --emerald-600: #059669;
    --emerald-700: #047857;
    --emerald-800: #065f46;
    --emerald-900: #064e3b;
    
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    
    --white: #ffffff;
    --black: #000000;
    
    --error-red: #ef4444;
    --error-bg: #fef2f2;
    
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    --shadow-2xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);
    --shadow-green: 0 10px 40px -10px rgba(5, 150, 105, 0.3);
    
    --radius-sm: 0.375rem;
    --radius-md: 0.5rem;
    --radius-lg: 0.75rem;
    --radius-xl: 1rem;
    --radius-2xl: 1.5rem;
    --radius-full: 9999px;
    
    --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
    --transition-normal: 250ms cubic-bezier(0.4, 0, 0.2, 1);
    --transition-slow: 350ms cubic-bezier(0.4, 0, 0.2, 1);
}

/* ==========================================
   FONDO DECORATIVO
   ========================================== */
.page-background {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: -1;
    overflow: hidden;
}

.bg-gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, var(--emerald-50) 0%, var(--white) 50%, var(--gray-50) 100%);
}

.bg-pattern {
    position: absolute;
    inset: 0;
    background-image: 
        radial-gradient(circle at 25px 25px, var(--emerald-100) 2px, transparent 0),
        radial-gradient(circle at 75px 75px, var(--emerald-100) 2px, transparent 0);
    background-size: 100px 100px;
    opacity: 0.5;
}

.floating-shapes {
    position: absolute;
    inset: 0;
    overflow: hidden;
}

.shape {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.4;
    animation: float 20s infinite ease-in-out;
}

.shape-1 {
    width: 400px;
    height: 400px;
    background: linear-gradient(135deg, var(--emerald-200), var(--emerald-100));
    top: -100px;
    right: -100px;
    animation-delay: 0s;
}

.shape-2 {
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, var(--emerald-100), var(--emerald-50));
    bottom: -50px;
    left: -50px;
    animation-delay: -7s;
}

.shape-3 {
    width: 200px;
    height: 200px;
    background: linear-gradient(135deg, var(--verde-pale), var(--emerald-100));
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    animation-delay: -14s;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    25% { transform: translate(30px, -30px) scale(1.05); }
    50% { transform: translate(-20px, 20px) scale(0.95); }
    75% { transform: translate(-30px, -20px) scale(1.02); }
}

/* ==========================================
   CONTENEDOR PRINCIPAL
   ========================================== */
.main-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
    position: relative;
}

/* ==========================================
   HEADER DE SECCION
   ========================================== */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, var(--emerald-600) 0%, var(--emerald-700) 100%);
    border-radius: var(--radius-2xl);
    box-shadow: var(--shadow-green), var(--shadow-xl);
    position: relative;
    overflow: hidden;
}

.section-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: translateX(-100%);
    animation: shimmer 3s infinite;
}

@keyframes shimmer {
    100% { transform: translateX(100%); }
}

.header-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-icon {
    width: 56px;
    height: 56px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: var(--radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.header-text {
    color: var(--white);
}

.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0;
    letter-spacing: -0.025em;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.page-subtitle {
    font-size: 0.9rem;
    opacity: 0.9;
    margin: 0.25rem 0 0;
    font-weight: 400;
}

.header-stats {
    display: flex;
    gap: 1.5rem;
}

.stat-item {
    text-align: center;
    padding: 0.75rem 1.5rem;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: var(--radius-lg);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.stat-number {
    display: block;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--white);
    line-height: 1;
}

.stat-label {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.85);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-top: 0.25rem;
    display: block;
}

/* ==========================================
   CARD GLASSMORPHISM
   ========================================== */
.card-glass {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: var(--radius-2xl);
    padding: 2rem;
    box-shadow: var(--shadow-xl);
    position: relative;
    overflow: hidden;
}

.card-shine {
    position: absolute;
    top: 0;
    left: -100%;
    width: 50%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transform: skewX(-25deg);
    animation: cardShine 8s infinite;
    pointer-events: none;
}

@keyframes cardShine {
    0%, 100% { left: -100%; }
    50% { left: 150%; }
}

/* ==========================================
   SECCION HERO - BOTON AGREGAR
   ========================================== */
.action-hero-section {
    background: linear-gradient(135deg, var(--emerald-50) 0%, var(--white) 100%);
    border: 2px solid var(--emerald-200);
    border-radius: var(--radius-xl);
    padding: 2rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}

.action-hero-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    z-index: 2;
}

.action-hero-text {
    flex: 1;
}

.action-hero-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-800);
    margin: 0 0 0.25rem;
}

.action-hero-subtitle {
    font-size: 0.95rem;
    color: var(--gray-500);
    margin: 0;
}

.btn-hero-add {
    display: inline-flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, var(--verde-primary) 0%, var(--verde-dark) 100%);
    color: var(--white);
    font-weight: 600;
    font-size: 1.05rem;
    text-decoration: none;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg), 0 8px 30px -8px var(--verde-glow);
    transition: all var(--transition-normal);
    position: relative;
    overflow: hidden;
}

.btn-hero-add::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(255,255,255,0.2), transparent 50%);
    opacity: 0;
    transition: opacity var(--transition-fast);
}

.btn-hero-add:hover::before {
    opacity: 1;
}

.btn-hero-add:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: var(--shadow-xl), 0 15px 40px -10px var(--verde-glow);
    background: linear-gradient(135deg, var(--verde-light) 0%, var(--verde-primary) 100%);
}

.btn-hero-add:active {
    transform: translateY(-1px) scale(1.01);
}

.btn-hero-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: var(--radius-lg);
    transition: all var(--transition-normal);
}

.btn-hero-add:hover .btn-hero-icon {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

.btn-hero-arrow {
    display: flex;
    align-items: center;
    transition: transform var(--transition-normal);
}

.btn-hero-add:hover .btn-hero-arrow {
    transform: translateX(4px);
}

.action-hero-decoration {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: 200px;
    pointer-events: none;
}

.decoration-circle {
    position: absolute;
    border-radius: 50%;
    border: 2px solid var(--emerald-200);
    opacity: 0.5;
}

.circle-1 {
    width: 150px;
    height: 150px;
    top: -40px;
    right: -40px;
}

.circle-2 {
    width: 100px;
    height: 100px;
    top: 50%;
    right: 20px;
    transform: translateY(-50%);
    border-color: var(--emerald-300);
}

.circle-3 {
    width: 60px;
    height: 60px;
    bottom: -20px;
    right: 60px;
}

/* ==========================================
   SECCION DE FILTROS
   ========================================== */
.filters-section {
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-xl);
    margin-bottom: 2rem;
    overflow: hidden;
}

.filters-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    background: var(--white);
    border-bottom: 1px solid var(--gray-200);
}

.filters-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 600;
    color: var(--gray-700);
}

.filters-title svg {
    color: var(--verde-primary);
}

.filters-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: var(--gray-100);
    border: none;
    border-radius: var(--radius-md);
    color: var(--gray-500);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.filters-toggle:hover {
    background: var(--gray-200);
    color: var(--verde-primary);
}

.filters-toggle.rotated {
    transform: rotate(180deg);
}

.filters-body {
    padding: 1.5rem;
    transition: all var(--transition-normal);
    max-height: 500px;
    opacity: 1;
}

.filters-body.collapsed {
    max-height: 0;
    padding: 0 1.5rem;
    opacity: 0;
    overflow: hidden;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}

.filter-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-item.filter-search {
    grid-column: span 1;
}

.filter-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.filter-label svg {
    color: var(--verde-primary);
}

/* ==========================================
   INPUTS Y SELECTS MODERNOS
   ========================================== */
.select-wrapper {
    position: relative;
}

.input-modern {
    width: 100%;
    padding: 0.875rem 1rem;
    padding-right: 2.5rem;
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--gray-700);
    background: var(--white);
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-lg);
    outline: none;
    transition: all var(--transition-normal);
    appearance: none;
    -webkit-appearance: none;
    cursor: pointer;
}

.input-modern:hover {
    border-color: var(--gray-300);
}

.input-modern:focus {
    border-color: var(--verde-primary);
    box-shadow: 0 0 0 4px var(--verde-glow);
}

.select-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
    pointer-events: none;
    transition: all var(--transition-fast);
}

.select-wrapper:hover .select-icon {
    color: var(--verde-primary);
}

/* ==========================================
   CAMPO DE BUSQUEDA
   ========================================== */
.search-input-wrapper {
    position: relative;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
    transition: all var(--transition-fast);
    z-index: 1;
}

.input-search {
    padding-left: 3rem;
    padding-right: 3rem;
}

.input-search:focus ~ .search-icon,
.input-search:not(:placeholder-shown) ~ .search-icon {
    color: var(--verde-primary);
}

.search-loader {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0;
    visibility: hidden;
    transition: all var(--transition-fast);
}

.search-loader.active {
    opacity: 1;
    visibility: visible;
}

.loader-spinner {
    width: 20px;
    height: 20px;
    border: 2px solid var(--gray-200);
    border-top-color: var(--verde-primary);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* ==========================================
   CONTENEDOR DE TABLA - NUEVO ESTILO
   ========================================== */
.table-wrapper {
    background: var(--white);
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--gray-200);
}

/* Estilos para la tabla dentro del partial */
.table-wrapper table,
.table-wrapper .table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin: 0 auto;
}

.table-wrapper thead tr {
    background: linear-gradient(135deg, var(--emerald-600) 0%, var(--emerald-700) 100%);
}

.table-wrapper thead th {
    padding: 1rem 1.25rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--white);
    border: none;
}

.table-wrapper thead th:first-child {
    border-top-left-radius: var(--radius-lg);
}

.table-wrapper thead th:last-child {
    border-top-right-radius: var(--radius-lg);
}

.table-wrapper tbody tr {
    transition: all var(--transition-fast);
    border-bottom: 1px solid var(--gray-100);
}

.table-wrapper tbody tr:hover {
    background: var(--emerald-50);
}

.table-wrapper tbody tr:last-child {
    border-bottom: none;
}

.table-wrapper tbody td {
    padding: 1rem 1.25rem;
    font-size: 0.95rem;
    color: var(--gray-700);
    vertical-align: middle;
    border: none;
}

/* ==========================================
   BADGES DE ROL - NUEVO ESTILO ELEGANTE
   ========================================== */
.table-wrapper .badge,
.table-wrapper .role-badge,
.table-wrapper [class*="badge"],
.table-wrapper span[class*="rol"],
.badge-role {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
    font-weight: 600;
    border-radius: var(--radius-full);
    text-transform: uppercase;
    letter-spacing: 0.03em;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition-fast);
}

/* Admin */
.table-wrapper .badge-admin,
.table-wrapper [class*="admin"],
.badge-role.admin {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    border: 1px solid #fcd34d;
}

.table-wrapper .badge-admin::before,
.badge-role.admin::before {
    content: '';
    width: 8px;
    height: 8px;
    background: #f59e0b;
    border-radius: 50%;
    box-shadow: 0 0 6px rgba(245, 158, 11, 0.5);
}

/* Usuario */
.table-wrapper .badge-user,
.table-wrapper [class*="user"],
.table-wrapper [class*="usuario"],
.badge-role.user {
    background: linear-gradient(135deg, var(--emerald-100) 0%, var(--emerald-200) 100%);
    color: var(--emerald-800);
    border: 1px solid var(--emerald-300);
}

.table-wrapper .badge-user::before,
.badge-role.user::before {
    content: '';
    width: 8px;
    height: 8px;
    background: var(--verde-primary);
    border-radius: 50%;
    box-shadow: 0 0 6px var(--verde-glow);
}

/* Moderador */
.table-wrapper .badge-moderator,
.table-wrapper [class*="moderator"],
.table-wrapper [class*="moderador"],
.badge-role.moderator {
    background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
    color: #5b21b6;
    border: 1px solid #c4b5fd;
}

.table-wrapper .badge-moderator::before,
.badge-role.moderator::before {
    content: '';
    width: 8px;
    height: 8px;
    background: #8b5cf6;
    border-radius: 50%;
    box-shadow: 0 0 6px rgba(139, 92, 246, 0.5);
}

/* Editor */
.table-wrapper .badge-editor,
.table-wrapper [class*="editor"],
.badge-role.editor {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
    border: 1px solid #93c5fd;
}

/* Guest/Invitado */
.table-wrapper .badge-guest,
.table-wrapper [class*="guest"],
.table-wrapper [class*="invitado"],
.badge-role.guest {
    background: linear-gradient(135deg, var(--gray-100) 0%, var(--gray-200) 100%);
    color: var(--gray-600);
    border: 1px solid var(--gray-300);
}

/* Status badges */
.table-wrapper .badge-active,
.table-wrapper [class*="activo"],
.status-active {
    background: linear-gradient(135deg, var(--emerald-100) 0%, var(--emerald-200) 100%);
    color: var(--emerald-800);
    border: 1px solid var(--emerald-300);
}

.table-wrapper .badge-inactive,
.table-wrapper [class*="inactivo"],
.status-inactive {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
    border: 1px solid #fca5a5;
}

/* Hover en badges */
.table-wrapper .badge:hover,
.table-wrapper [class*="badge"]:hover,
.badge-role:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* ==========================================
   BOTONES DE ACCION EN TABLA
   ========================================== */
.table-wrapper .btn,
.table-wrapper button,
.table-wrapper a.btn {
    padding: 0.5rem 0.875rem;
    font-size: 0.85rem;
    font-weight: 500;
    border-radius: var(--radius-md);
    transition: all var(--transition-fast);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
}

.table-wrapper .btn-primary,
.table-wrapper .btn-edit {
    background: var(--verde-primary);
    color: var(--white);
    border: none;
}

.table-wrapper .btn-primary:hover,
.table-wrapper .btn-edit:hover {
    background: var(--verde-dark);
    transform: translateY(-1px);
}

.table-wrapper .btn-danger,
.table-wrapper .btn-delete {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.table-wrapper .btn-danger:hover,
.table-wrapper .btn-delete:hover {
    background: #dc2626;
    color: var(--white);
    transform: translateY(-1px);
}

.table-wrapper .btn-secondary,
.table-wrapper .btn-view {
    background: var(--gray-100);
    color: var(--gray-700);
    border: 1px solid var(--gray-200);
}

.table-wrapper .btn-secondary:hover,
.table-wrapper .btn-view:hover {
    background: var(--gray-200);
    transform: translateY(-1px);
}

/* ==========================================
   PAGINACION MODERNA
   ========================================== */
.table-wrapper .pagination,
.table-wrapper nav[aria-label="Pagination"] {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    padding: 1.5rem;
    background: var(--gray-50);
    border-top: 1px solid var(--gray-200);
}

.table-wrapper .pagination .page-link,
.table-wrapper .pagination a,
.table-wrapper .pagination span {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 0.75rem;
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--gray-600);
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    text-decoration: none;
    transition: all var(--transition-fast);
}

.table-wrapper .pagination .page-link:hover,
.table-wrapper .pagination a:hover {
    background: var(--emerald-50);
    border-color: var(--verde-primary);
    color: var(--verde-primary);
}

.table-wrapper .pagination .active .page-link,
.table-wrapper .pagination .active span,
.table-wrapper .pagination a.active {
    background: linear-gradient(135deg, var(--verde-primary) 0%, var(--verde-dark) 100%);
    border-color: var(--verde-primary);
    color: var(--white);
    box-shadow: 0 4px 12px -2px var(--verde-glow);
}

.table-wrapper .pagination .disabled .page-link,
.table-wrapper .pagination .disabled span {
    opacity: 0.5;
    cursor: not-allowed;
}

/* ==========================================
   ANIMACION FADE IN
   ========================================== */
.fade-in {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ==========================================
   TOAST MODERNO
   ========================================== */
.toast-modern {
    position: fixed;
    top: 1.5rem;
    right: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    background: var(--white);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-2xl);
    z-index: 9999;
    min-width: 320px;
    max-width: 450px;
    border-left: 4px solid var(--verde-primary);
    transform: translateX(0);
    opacity: 1;
    transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.toast-modern.show {
    animation: slideIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.toast-modern.hide {
    transform: translateX(120%);
    opacity: 0;
}

@keyframes slideIn {
    from {
        transform: translateX(120%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast-success {
    border-left-color: var(--verde-primary);
}

.toast-success .toast-icon {
    background: var(--verde-pale);
    color: var(--verde-primary);
}

.toast-error {
    border-left-color: var(--error-red);
}

.toast-error .toast-icon {
    background: var(--error-bg);
    color: var(--error-red);
}

.toast-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.toast-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

.toast-title {
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--gray-800);
}

.toast-message {
    font-size: 0.85rem;
    color: var(--gray-500);
}

.toast-close {
    background: none;
    border: none;
    color: var(--gray-400);
    cursor: pointer;
    padding: 0.25rem;
    border-radius: var(--radius-md);
    transition: all var(--transition-fast);
    display: flex;
    align-items: center;
    justify-content: center;
}

.toast-close:hover {
    background: var(--gray-100);
    color: var(--gray-600);
}

/* ==========================================
   FOOTER INFO
   ========================================== */
.card-footer-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1.5rem;
    padding: 1rem 1.5rem;
    background: var(--gray-50);
    border-radius: var(--radius-xl);
    font-size: 0.85rem;
    color: var(--gray-500);
}

.footer-left,
.footer-right {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.footer-left svg {
    color: var(--verde-primary);
}

.status-dot {
    width: 8px;
    height: 8px;
    background: var(--verde-light);
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.2); }
}

/* ==========================================
   RESPONSIVE DESIGN
   ========================================== */
@media (max-width: 1200px) {
    .filters-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .filter-item.filter-search {
        grid-column: span 2;
    }
}

@media (max-width: 768px) {
    .main-container {
        padding: 1rem;
    }
    
    .section-header {
        flex-direction: column;
        gap: 1.5rem;
        text-align: center;
        padding: 1.5rem;
    }
    
    .header-content {
        flex-direction: column;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .action-hero-content {
        flex-direction: column;
        gap: 1.5rem;
        text-align: center;
    }
    
    .btn-hero-add {
        width: 100%;
        justify-content: center;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-item.filter-search {
        grid-column: span 1;
    }
    
    .card-glass {
        padding: 1.25rem;
    }
    
    .toast-modern {
        left: 1rem;
        right: 1rem;
        min-width: auto;
    }
    
    .card-footer-info {
        flex-direction: column;
        gap: 0.75rem;
        text-align: center;
    }
    
    /* Tabla responsive */
    .table-wrapper {
        overflow-x: auto;
    }
    
    .table-wrapper table {
        min-width: 600px;
    }
}

@media (max-width: 480px) {
    .action-hero-section {
        padding: 1.5rem;
    }
    
    .action-hero-title {
        font-size: 1.25rem;
    }
    
    .header-stats {
        width: 100%;
    }
    
    .stat-item {
        flex: 1;
    }
}

/* ==========================================
   UTILIDADES ADICIONALES
   ========================================== */
::selection {
    background: var(--verde-pale);
    color: var(--verde-darker);
}

/* Scrollbar personalizada */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: var(--gray-100);
    border-radius: var(--radius-full);
}

::-webkit-scrollbar-thumb {
    background: var(--gray-300);
    border-radius: var(--radius-full);
    transition: all var(--transition-fast);
}

::-webkit-scrollbar-thumb:hover {
    background: var(--verde-primary);
}

/* Empty state para tabla vacia */
.table-wrapper .empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--gray-500);
}

.table-wrapper .empty-state svg {
    width: 64px;
    height: 64px;
    color: var(--gray-300);
    margin-bottom: 1rem;
}

.table-wrapper .empty-state h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gray-700);
    margin: 0 0 0.5rem;
}

.table-wrapper .empty-state p {
    margin: 0;
}
</style>
@endsection