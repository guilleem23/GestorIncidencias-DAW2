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
        // 1. Validar los datos de entrada 
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Intentar el login añadiendo la condición de 'actiu'
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'actiu' => true])) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }
        // 3. Si falla, devolver error 
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden o el usuario no está activo.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
