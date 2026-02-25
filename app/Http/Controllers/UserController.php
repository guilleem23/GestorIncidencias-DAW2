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
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 5);

        $query = User::with('sede');

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('username', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por Rol
        if ($request->filled('rol')) {
            $query->where('rol', $request->input('rol'));
        }

        // Filtro por Sede
        if ($request->filled('sede')) {
            $query->where('sede_id', $request->input('sede'));
        }

        // Filtro por Estado (Activo/Inactivo)
        if ($request->has('activo') && $request->input('activo') !== '') {
            $query->where('actiu', $request->input('activo'));
        }

        // Ordenar siempre por nombre alfabéticamente (limpiando cualquier orden previo)
        $usuarios = $query->reorder('name', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        $sedes = Sede::orderBy('nom')->get();
        $roles = [
            'administrador' => 'Admin',
            'client' => 'Cliente',
            'gestor' => 'Gestor de Equipo',
            'tecnic' => 'Tecnico de Mantenimiento',
        ];

        if ($request->ajax()) {
            return view('admin.usuarios.partial.tabla_usuarios', compact('usuarios'));
        }

        return view('admin.usuarios.index', compact('usuarios', 'sedes', 'roles'));
    }

    /**
     * Comprobar disponibilidad de email para AJAX
     */
    public function checkEmail(Request $request)
    {
        $email = $request->query('email');
        $id = $request->query('exclude_id');
        $query = User::where('email', $email);
        if ($id) {
            $query->where('id', '!=', $id);
        }
        $disponible = !$query->exists();
        return response()->json(['disponible' => $disponible]);
    }

    /**
     * Comprobar disponibilidad de nombre de usuario para AJAX
     */
    public function checkUsername(Request $request)
    {
        $username = $request->query('username');
        $id = $request->query('exclude_id');
        $query = User::where('username', $username);
        if ($id) {
            $query->where('id', '!=', $id);
        }
        $disponible = !$query->exists();
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
            'edit_username' => ['required', 'string', 'min:3', 'max:255', 'unique:usuarios,username,' . $id],
            'edit_email' => ['required', 'email', 'max:255', 'unique:usuarios,email,' . $id],
            'edit_sede_id' => ['required', 'exists:sedes,id'],
            'edit_rol' => ['required', 'in:' . implode(',', $rolesPermitidos)],
            'edit_activo' => ['required', 'boolean'],
        ], [
            'edit_name.required' => 'El nombre es obligatorio.',
            'edit_name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'edit_username.required' => 'El nombre de usuario es obligatorio.',
            'edit_username.min' => 'El nombre de usuario debe tener al menos 3 caracteres.',
            'edit_username.unique' => 'Ese nombre de usuario ya está registrado.',
            'edit_email.required' => 'El email es obligatorio.',
            'edit_email.email' => 'El email debe ser válido.',
            'edit_email.unique' => 'Ese email ya está registrado.',
            'edit_sede_id.required' => 'La sede es obligatoria.',
            'edit_sede_id.exists' => 'La sede seleccionada no existe.',
            'edit_rol.required' => 'El rol es obligatorio.',
            'edit_rol.in' => 'El rol seleccionado no es válido.',
            'edit_activo.required' => 'El estado es obligatorio.',
            'edit_activo.boolean' => 'El estado seleccionado no es válido.'
        ]);

        try {
            DB::beginTransaction();
            $usuario = User::findOrFail($id);
            $usuario->name = $validated['edit_name'];
            $usuario->username = $validated['edit_username'];
            $usuario->email = $validated['edit_email'];
            $usuario->sede_id = $validated['edit_sede_id'];
            $usuario->rol = $validated['edit_rol'];
            $usuario->actiu = $validated['edit_activo'];
            
            if ($request->filled('edit_password')) {
                $usuario->password = Hash::make($request->input('edit_password'));
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
            
            // Verificar si tiene incidencias como cliente o técnico
            $tieneIncidencias = $usuario->incidenciasComoCliente()->exists() || $usuario->incidenciasComoTecnico()->exists();

            if ($tieneIncidencias) {
                // Si tiene historial, solo lo desactivamos para no romper la integridad referencial
                $usuario->actiu = false;
                $usuario->save();
                DB::commit();
                return redirect()->route('admin.usuarios.index')->with('success', 'El usuario tiene incidencias asociadas, por lo que se ha desactivado en lugar de eliminarse para conservar el historial.');
            }

            // Si no tiene historial, eliminación física
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
            'username' => ['required', 'string', 'min:3', 'max:255', 'unique:usuarios,username'],
            'email' => ['required', 'email', 'max:255', 'unique:usuarios,email'],
            'password' => ['required', 'string', 'min:6'],
            'sede_id' => ['required', 'exists:sedes,id'],
            'rol' => ['required', 'in:' . implode(',', $rolesPermitidos)],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.min' => 'El nombre de usuario debe tener al menos 3 caracteres.',
            'username.unique' => 'Ese nombre de usuario ya está registrado.',
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
                'username' => $validated['username'],
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

    /**
     * Muestra la lista de técnicos para el gestor de la sede.
     */
    public function indexGestor()
    {
        $user = auth()->user();
        $tecnicos = User::where('sede_id', $user->sede_id)
            ->where('rol', 'tecnic')
            ->orderBy('name')
            ->get();

        return view('gestor.usuarios', compact('tecnicos'));
    }
}
