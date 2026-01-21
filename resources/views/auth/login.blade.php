<x-guest-layout>
    <div class="login-card p-4 shadow">
        <h4 class="text-center mb-4">Iniciar sesión</h4>

        {{-- Mensajes de error --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <p class="mb-0">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Usuario</label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    <input id="email" type="text" class="form-control login-input"
                        name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                </div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock-fill"></i>
                    </span>

                    <input id="password" type="password" class="form-control login-input"
                        name="password" required autocomplete="current-password">

                    <button type="button" class="btn btn-outline-secondary"
                        id="toggle-password" tabindex="-1"
                        onclick="togglePassword('password','icono')">
                        <i class="bi bi-eye-fill" id="icono"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="form-check mb-3">
                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                <label for="remember_me" class="form-check-label">Recordarme</label>
            </div>


            <!-- Botón login -->
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Iniciar sesión
                </button>
            </div>
        </form>

        <hr>

        <!-- Login Google -->
        <a href="{{ route('google.login') }}" class="btn btn-google w-100">
            <i class="bi bi-google me-2"></i> Iniciar sesión con Google
        </a>
    </div>
</x-guest-layout>
