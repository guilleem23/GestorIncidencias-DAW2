<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sede;
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
            return back()->withErrors(['error' => 'Error al crear la sede: ' . $e->getMessage()]);
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
            return back()->withErrors(['error' => 'Error al actualizar la sede: ' . $e->getMessage()]);
        }
    }

    /**
     * Elimina una sede.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $sede = Sede::findOrFail($id);
            if ($sede->imagen && file_exists(public_path($sede->imagen))) {
                unlink(public_path($sede->imagen));
            }
            $sede->delete();
            DB::commit();
            return redirect()->route('admin.sedes.index')->with('success', 'Sede eliminada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar la sede: ' . $e->getMessage()]);
        }
    }
}
