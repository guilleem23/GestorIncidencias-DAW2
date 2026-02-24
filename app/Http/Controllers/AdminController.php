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
        // Vista de administración de usuarios
        return view('admin.index');
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

    public function listado(Request $request)
    {
        // Obtener filtros de la petición
        $sedeFilter = $request->get('sede');
        $rolFilter = $request->get('rol');
        $estadoFilter = $request->get('estado', 'activos'); // Por defecto mostrar solo activos

        // Construir query de usuarios
        $query = User::with('sede');

        // Aplicar filtro de sede
        if ($sedeFilter) {
            $query->where('sede_id', $sedeFilter);
        }

        // Aplicar filtro de rol
        if ($rolFilter) {
            $query->where('rol', $rolFilter);
        }

        // Aplicar filtro de estado
        if ($estadoFilter === 'activos') {
            $query->where('actiu', true);
        } elseif ($estadoFilter === 'inactivos') {
            $query->where('actiu', false);
        }
        // Si es 'todos', no aplicar filtro

        $usuarios = $query->orderBy('name')->get();

        // Obtener todas las sedes para el filtro
        $sedes = Sede::orderBy('nom')->get();

        // Roles disponibles
        $roles = [
            'administrador' => 'Administrador',
            'client' => 'Cliente',
            'gestor' => 'Gestor',
            'tecnic' => 'Técnico',
        ];

        return view('admin.usuarios.listado', compact('usuarios', 'sedes', 'roles', 'sedeFilter', 'rolFilter', 'estadoFilter'));
    }

    public function categorias()
    {
        // Método para gestionar categorías y subcategorías
        return view('admin.categorias.index');
    }

    public function darBaja($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->actiu = false;
        $usuario->save();

        return redirect()
            ->route('admin.usuarios.listado', request()->only(['sede', 'rol', 'estado']))
            ->with('success', 'Usuario "' . $usuario->name . '" dado de baja correctamente.');
    }

    public function darAlta($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->actiu = true;
        $usuario->save();

        return redirect()
            ->route('admin.usuarios.listado', request()->only(['sede', 'rol', 'estado']))
            ->with('success', 'Usuario "' . $usuario->name . '" reactivado correctamente.');
    }
}
