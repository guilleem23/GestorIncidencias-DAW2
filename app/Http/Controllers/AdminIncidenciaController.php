<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminIncidenciaController extends Controller
{
    /**
     * Muestra la lista global de incidencias para el panel de admin.
     */
    public function index()
    {
        $incidencias = Incidencia::with(['cliente', 'sede', 'tecnico'])
            ->orderByDesc('created_at')
            ->get();

        $tecnicsBySede = User::where('rol', 'tecnic')
            ->where('actiu', true)
            ->orderBy('name')
            ->get()
            ->groupBy('sede_id');

        return view('admin.admin_dashboard_incidencias', compact('incidencias', 'tecnicsBySede'));
    }

    /**
     * Asigna un técnico a una incidencia (obligatorio que sea de la misma sede).
     */
    public function assignTecnic(Request $request, $id)
    {
        $validated = $request->validate([
            'tecnic_id' => ['required', 'integer', 'exists:usuarios,id'],
        ]);

        $incidencia = Incidencia::findOrFail($id);

        $tecnic = User::where('id', $validated['tecnic_id'])
            ->where('rol', 'tecnic')
            ->where('actiu', true)
            ->first();

        if (!$tecnic) {
            return back()->withErrors([
                'tecnic_id' => 'El técnico seleccionado no es válido o no está activo.',
            ]);
        }

        if ((int) $tecnic->sede_id !== (int) $incidencia->sede_id) {
            return back()->withErrors([
                'tecnic_id' => 'No puedes asignar un técnico de otra sede a esta incidencia.',
            ]);
        }

        DB::beginTransaction();
        try {
            $incidencia->tecnic_id = $tecnic->id;

            // Si todavía no estaba asignada, pasamos a "Assignada"
            if ($incidencia->estat === 'Sense assignar') {
                $incidencia->estat = 'Assignada';
            }

            $incidencia->save();
            DB::commit();

            return back()->with('success', 'Técnico asignado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Error al asignar técnico: ' . $e->getMessage(),
            ]);
        }
    }
}
