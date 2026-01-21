<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();
        $email = $googleUser->getEmail();
        $nombreCompleto = $googleUser->getName();

        // Validación de dominio
        if (!str_ends_with($email, '@kombitec.com.mx')) {
            return redirect('/login')->with('error', 'Solo se permiten cuentas del dominio kombitec.com.mx');
        }

        // Buscar usuario existente
        $user = User::where('email', $email)->first();

        // Si existe el usuario
        if ($user) {
            // Si el nombre NO coincide → desactivar y crear nuevo
            if ($user->name !== $nombreCompleto) {
                $user->email = '(baja_' . now()->format('Ymd').')'.$user->email;
                $user->active = false;
                $user->rol = 4;
                $user->save();

                // Creamos uno nuevo con el mismo email
                $user = User::create([
                    'name'  => $nombreCompleto,
                    'email' => $email,
                    'rol'   => 4,
                    'active' => true,
                ]);
            }

            // Si el usuario está inactivo → bloquear acceso
            if (!$user->active) {
                return redirect('/login')->with('error', 'Cuenta inactiva. Contacta al administrador.');
            }

        } else {
            // Si no existe el usuario se crea uno desde cero
            $user = User::create([
                'name'  => $nombreCompleto,
                'email' => $email,
                'rol'   => 4,
                'active' => true,
            ]);
        }

        // Hacer login
        Auth::login($user);

        // Verificación de correo
        if (!$user->hasVerifiedEmail()) {
            event(new Registered($user));
            return redirect()->route('verification.notice');
        }

        return redirect()->route('dashboard');
    }

}
