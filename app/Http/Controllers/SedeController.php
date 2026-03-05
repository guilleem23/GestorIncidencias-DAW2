<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\User;
use App\Models\Incidencia;
use App\Models\Comentario;
use Illuminate\Support\Facades\DB;

class SedeController extends Controller
{
    /**
     * Muestra la lista de sedes.
     */
    public function index()
    {
        $sedes = Sede::with('gestor')
            ->withCount(['incidencies as incidencies_obertes_count' => function($q) {
            $q->whereNotIn('estat', ['Tancada', 'Resolta']);
        }])
        ->orderBy('nom')
        ->get();
        return view('admin.sedes.index', compact('sedes'));
    }

    /**
     * Crea una nueva sede.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'min:2', 'max:255', 'unique:sedes,nom'],
            'responsable' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'imagen' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ], [
            'nom.required' => 'El nombre de la sede es obligatorio.',
            'nom.string' => 'El nombre debe ser texto.',
            'nom.min' => 'El nombre debe tener al menos 2 caracteres.',
            'nom.max' => 'El nombre no puede superar 255 caracteres.',
            'nom.unique' => 'Esta sede ya existe.',
            'responsable.string' => 'El responsable debe ser texto.',
            'responsable.max' => 'El responsable no puede superar 255 caracteres.',
            'descripcion.string' => 'La descripción debe ser texto.',
            'descripcion.max' => 'La descripción no puede superar 1000 caracteres.',
            'imagen.image' => 'El archivo debe ser una imagen válida.',
            'imagen.mimes' => 'La imagen debe ser JPG, PNG, GIF, SVG o WEBP.',
            'imagen.max' => 'La imagen no puede superar 2MB.',
        ]);

        DB::beginTransaction();
        try {
            $data = $validated;
            if ($request->hasFile('imagen')) {
                $file = $request->file('imagen');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('img/sedes'), $filename);
                $data['imagen'] = 'img/sedes/' . $filename;
            }

            Sede::create($data);
            DB::commit();
            return redirect()->route('admin.sedes.index')->with('success', 'Sede creada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'No se pudo crear la sede. Inténtalo de nuevo.']);
        }
    }

    /**
     * Devuelve el partial de edición de sede.
     */
    public function edit($id)
    {
        $sede = Sede::findOrFail($id);
        $gestor = \App\Models\User::where('sede_id', $sede->id)
            ->where('rol', 'gestor')
            ->first();
        return view('admin.sedes.partial.editar_sede', compact('sede', 'gestor'));
    }

    /**
     * Actualiza una sede.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'min:2', 'max:255', 'unique:sedes,nom,' . $id],
            'responsable' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'imagen' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ], [
            'nom.required' => 'El nombre de la sede es obligatorio.',
            'nom.string' => 'El nombre debe ser texto.',
            'nom.min' => 'El nombre debe tener al menos 2 caracteres.',
            'nom.max' => 'El nombre no puede superar 255 caracteres.',
            'nom.unique' => 'Esta sede ya existe.',
            'responsable.string' => 'El responsable debe ser texto.',
            'responsable.max' => 'El responsable no puede superar 255 caracteres.',
            'descripcion.string' => 'La descripción debe ser texto.',
            'descripcion.max' => 'La descripción no puede superar 1000 caracteres.',
            'imagen.image' => 'El archivo debe ser una imagen válida.',
            'imagen.mimes' => 'La imagen debe ser JPG, PNG, GIF, SVG o WEBP.',
            'imagen.max' => 'La imagen no puede superar 2MB.',
        ]);

        DB::beginTransaction();
        try {
            $sede = Sede::findOrFail($id);
            $data = $validated;

            if ($request->hasFile('imagen')) {
                // Remove old image if needed
                if ($sede->imagen && file_exists(public_path($sede->imagen))) {
                    unlink(public_path($sede->imagen));
                }
                $file = $request->file('imagen');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('img/sedes'), $filename);
                $data['imagen'] = 'img/sedes/' . $filename;
            }

            $sede->update($data);
            DB::commit();
            return redirect()->route('admin.sedes.index')->with('success', 'Sede actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'No se pudo actualizar la sede. Inténtalo de nuevo.']);
        }
    }

    /**
     * Elimina una sede con todas sus dependencias (transacción).
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $sede = Sede::findOrFail($id);

            // 1. Guardar IDs de usuarios de esta sede (antes de limpiar sede_id)
            $userIds = User::where('sede_id', $sede->id)->pluck('id');

            // 2. Obtener IDs de incidencias de esta sede
            $incidenciaIds = Incidencia::where('sede_id', $sede->id)->pluck('id');

            // 3. Eliminar todos los comentarios de esas incidencias
            if ($incidenciaIds->isNotEmpty()) {
                Comentario::whereIn('incidencia_id', $incidenciaIds)->delete();
            }

            // 4. Eliminar todas las incidencias de esta sede
            Incidencia::where('sede_id', $sede->id)->delete();

            // 5. Desasignar gestor y técnicos (sede_id = null)
            User::where('sede_id', $sede->id)->update(['sede_id' => null]);

            // 6. Desactivar todos los usuarios que pertenecían a esta sede
            if ($userIds->isNotEmpty()) {
                User::whereIn('id', $userIds)->update(['actiu' => false]);
            }

            // 7. Eliminar imagen del filesystem
            if ($sede->imagen && file_exists(public_path($sede->imagen))) {
                unlink(public_path($sede->imagen));
            }

            // 8. Eliminar la sede
            $sede->delete();

            DB::commit();
            return redirect()->route('admin.sedes.index')
                ->with('success', 'Sede eliminada correctamente. Usuarios desactivados y desasignados.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'No se pudo eliminar la sede. Inténtalo de nuevo.']);
        }
    }

    /**
     * Comprueba si un nombre de sede está disponible (AJAX).
     */
    public function checkNombre(Request $request)
    {
        $nom = $request->query('nom');
        $excludeId = $request->query('exclude_id');

        $query = Sede::where('nom', $nom);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $exists = $query->exists();
        return response()->json(['disponible' => !$exists]);
    }
}
