<x-guest-layout>
    <div class="verify-box">
        <div class="icon-wrapper">
            <i class="fa-solid fa-envelope-circle-check icon icon-success"></i>
        </div>

        <h1 class="title">Verificación de Correo</h1>
        <p class="message">
            ¡Gracias por registrarte! Antes de comenzar, verifica tu correo electrónico haciendo clic en el enlace que te enviamos.<br>
            Si no lo recibiste, con gusto te enviaremos otro.
        </p>

        @if (session('status') == 'verification-link-sent')
        <div class="alert-success">
            Se ha enviado un nuevo enlace de verificación al correo que proporcionaste.
        </div>
        @endif

        <div class="actions">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-success" id="resend-btn">
                    Reenviar Verificación
                </button>
            </form>
            <div id="timer" style="margin-top:10px; font-weight: bold;"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>

</x-guest-layout>