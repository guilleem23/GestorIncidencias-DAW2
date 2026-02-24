<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
     * Comprobar disponibilidad de email para AJAX
     */
    public function checkEmail(Request $request)
    {
        $email = $request->query('email');
        $disponible = !User::where('email', $email)->exists();
        return response()->json(['disponible' => $disponible]);
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
            'edit_name' => ['required', 'string', 'min:3', 'max:255'],
            'edit_email' => ['required', 'email', 'max:255', 'unique:usuarios,email,' . $id],
            'edit_sede_id' => ['required', 'exists:sedes,id'],
            'edit_rol' => ['required', 'in:' . implode(',', $rolesPermitidos)],
        ], [
            'edit_name.required' => 'El nombre es obligatorio.',
            'edit_name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'edit_email.required' => 'El email es obligatorio.',
            'edit_email.email' => 'El email debe ser válido.',
            'edit_email.unique' => 'Ese email ya está registrado.',
            'edit_sede_id.required' => 'La sede es obligatoria.',
            'edit_sede_id.exists' => 'La sede seleccionada no existe.',
            'edit_rol.required' => 'El rol es obligatorio.',
            'edit_rol.in' => 'El rol seleccionado no es válido.'
        ]);
        // Guardar los datos editados
        $usuario = User::findOrFail($id);
        $usuario->name = $validated['edit_name'];
        $usuario->email = $validated['edit_email'];
        $usuario->sede_id = $validated['edit_sede_id'];
        $usuario->rol = $validated['edit_rol'];
        if ($request->filled('edit_password')) {
            $usuario->password = Hash::make($request->input('edit_password'));
        }
        $usuario->save();
        DB::commit();
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
        try {
            DB::beginTransaction();
            $usuario = User::findOrFail($id);
            $usuario->name = $validated['name'];
            $usuario->email = $validated['email'];
            $usuario->sede_id = $validated['sede_id'];
            $usuario->rol = $validated['rol'];
            if ($request->filled('password')) {
                $usuario->password = Hash::make($request->input('password'));
            }
            $usuario->save();
            DB::commit();
            return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.usuarios.index')->withErrors(['error_editar' => 'Error al actualizar el usuario: ' . $e->getMessage()]);
        }
    }

    /**
     * Elimina un usuario usando transacción y rollback.
     */
    public function destroy($id)
    {
        // Validar que llega el id
        if (empty($id)) {
            return redirect()->route('admin.usuarios.index')->withErrors(['error_eliminar' => 'El id del usuario no ha llegado.']);
        }
        // Validar que el usuario existe
        $usuario = User::find($id);
        if (!$usuario) {
            return redirect()->route('admin.usuarios.index')->withErrors(['error_eliminar' => 'El usuario que intentas eliminar no existe o ya ha sido eliminado.']);
        }
        try {
            DB::beginTransaction();
            $usuario->delete();
            DB::commit();
            return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.usuarios.index')->withErrors(['error_eliminar' => 'Error al eliminar el usuario: ' . $e->getMessage()]);
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
        $rolesPermitidos = ['administrador', 'client', 'gestor', 'tecnic'];
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:usuarios,email'],
            'password' => ['required', 'string', 'min:6'],
            'sede_id' => ['required', 'exists:sedes,id'],
            'rol' => ['required', 'in:' . implode(',', $rolesPermitidos)],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe ser válido.',
            'email.unique' => 'Ese email ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'sede_id.required' => 'La sede es obligatoria.',
            'sede_id.exists' => 'La sede seleccionada no existe.',
            'rol.required' => 'El rol es obligatorio.',
            'rol.in' => 'El rol seleccionado no es válido.'
        ]);
        try {
            DB::beginTransaction();
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'sede_id' => $validated['sede_id'],
                'rol' => $validated['rol'],
                'actiu' => true,
            ]);
            DB::commit();
            return redirect()
                ->route('admin.usuarios.index')
                ->with('success', 'Usuario creado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.usuarios.index')
                ->withErrors(['error' => 'Error al crear el usuario: ' . $e->getMessage()]);
        }
    }
}
