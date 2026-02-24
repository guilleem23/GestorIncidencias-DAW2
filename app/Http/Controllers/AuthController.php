<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Validar los datos
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Intentar el login (incluyendo la condición de 'actiu')
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'actiu' => true])) {
            $request->session()->regenerate();

            // 3. Redirección inteligente por ROL
            $user = Auth::user();

            return match ($user->rol) {
                'administrador' => redirect()->intended('/admin/dashboard'),
                'gestor'        => redirect()->intended('/gestor/incidencies'),
                'tecnic'        => redirect()->intended('/tecnic/tasques'),
                'client'        => redirect()->intended('/client/mis-incidencias'),
                default         => redirect()->intended('/dashboard'),
            };
        }

        // 4. Si falla el login
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden o el usuario no está activo.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // Usar localStorage para mostrar SweetAlert tras logout
        echo '<script>localStorage.setItem("logout_success", "1");window.location.href="/";</script>';
        exit;
    }
}
