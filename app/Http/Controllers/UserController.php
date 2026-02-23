<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
   /**
     * Muestra la lista de usuarios.
     */
    public function index()
    {
        $usuarios = User::with('sede')->orderBy('name')->get();
        $sedes = Sede::orderBy('nom')->get();
        $roles = [
            'administrador' => 'Admin',
            'client' => 'Cliente',
            'gestor' => 'Gestor de Equipo',
            'tecnic' => 'Tecnico de Mantenimiento',
        ];
        return view('admin.usuarios.index', compact('usuarios', 'sedes', 'roles'));
    }

    /**
     * Muestra el formulario de edición de un usuario.
     */
    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        $sedes = Sede::orderBy('nom')->get();
        $roles = [
            'administrador' => 'Admin',
            'client' => 'Cliente',
            'gestor' => 'Gestor de Equipo',
            'tecnic' => 'Tecnico de Mantenimiento',
        ];
        return view('admin.usuarios.partial.editar_usuario', compact('usuario', 'sedes', 'roles'));
    }

    /**
     * Actualiza un usuario en la base de datos usando transacción.
     */
    public function update(Request $request, $id)
    {
        $rolesPermitidos = ['administrador', 'client', 'gestor', 'tecnic'];
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:usuarios,email,' . $id],
            'sede_id' => ['required', 'exists:sedes,id'],
            'rol' => ['required', 'in:' . implode(',', $rolesPermitidos)],
            'actiu' => ['required', 'boolean'],
        ]);
        User::beginTransaction();
        try {
            $usuario = User::findOrFail($id);
            $usuario->update($validated);
            User::commit();
            return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
        } catch (\Exception $e) {
            User::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el usuario: ' . $e->getMessage()]);
        }
    }

    /**
     * Elimina un usuario usando transacción y rollback.
     */
    public function destroy($id)
    {
        User::beginTransaction();
        try {
            $usuario = User::findOrFail($id);
            $usuario->delete();
            \DB::commit();
            return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar el usuario: ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        $sedes = Sede::orderBy('nom')->get();

        $roles = [
            'administrador' => 'Admin',
            'client' => 'Cliente',
            'gestor' => 'Gestor de Equipo',
            'tecnic' => 'Tecnico de Mantenimiento',
        ];
        return view('admin.usuarios.partial.crear_usuario', compact('sedes', 'roles'));
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
