{{-- ===== ESTILO DE ALERTAS FLOTANTES ===== --}}
<style>
    .alert-floating {
        position: fixed;
        top: 20px;
        right: 25px;
        z-index: 9999;
        min-width: 300px;
        max-width: 380px;

        border-radius: 12px;
        padding: 15px 18px;
        font-size: 15px;

        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        animation: slideIn 0.4s ease-out;
        opacity: 0.97;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(80px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .alert-floating .btn-close {
        background-size: 40% !important;
    }

    /* Iconos más grandes y corporativos */
    .alert-floating i {
        font-size: 20px;
        margin-right: 8px;
        vertical-align: middle;
    }
</style>

{{-- ===== MENSAJE DE ÉXITO ===== --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show alert-floating" role="alert">
        <i class="bi bi-check-circle-fill"></i>
        {{ session('success') }}
    </div>
@endif

{{-- ===== MENSAJE DE ERROR ===== --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show alert-floating" role="alert">
        <i class="bi bi-x-octagon-fill"></i>
        {{ session('error') }}
    </div>
@endif

{{-- ===== ERRORES DE VALIDACIÓN ===== --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show alert-floating" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <strong>Ups, algo no cuadra:</strong>
        <ul class="mt-1 mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ===== SCRIPT PARA AUTO-CIERRE ===== --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert-floating');
            alerts.forEach(alert => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            });
        }, 8000); // 8 segundos
    });
</script>
