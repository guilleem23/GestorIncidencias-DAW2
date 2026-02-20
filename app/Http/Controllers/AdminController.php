<?php

namespace App\Http\Controllers;

use App\Models\Sede;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        // Asegúrate de crear esta vista o dará error
        return view('admin.usuarios.index'); 
    }

    public function create()
    {
        $sedes = Sede::orderBy('nom')->get();
        
        // CONSEJO: Podrías llevar esto a una clase Enum o Constante para no repetirlo
        $roles = [
            'administrador' => 'Admin',
            'client' => 'Cliente',
            'gestor' => 'Gestor de Equipo',
            'tecnic' => 'Tecnico de Mantenimiento',
        ];

        return view('admin.crear_usuario', compact('sedes', 'roles'));
    }

    public function store(Request $request)
    {
        // Repetir el array aquí es peligroso si cambias uno y olvidas el otro.
        // Lo ideal es validar contra las claves del array anterior, pero así funciona:
        $rolesPermitidos = ['administrador', 'client', 'gestor', 'tecnic'];

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'], 
            'email' => ['required', 'email', 'max:255', 'unique:usuarios,email'], 
            'password' => ['required', 'string', 'min:6'],
            'sede_id' => ['required', 'exists:sedes,id'],
            'rol' => ['required', 'in:' . implode(',', $rolesPermitidos)], 
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'sede_id' => $validated['sede_id'],
            'rol' => $validated['rol'],
            'actiu' => true, // Asegúrate de que este campo está en $fillable del modelo User
        ]);

        return redirect()
            ->route('admin.usuarios.create') // Asegúrate de que esta ruta existe en web.php
            ->with('success', 'Usuario creado correctamente.');
    }
}