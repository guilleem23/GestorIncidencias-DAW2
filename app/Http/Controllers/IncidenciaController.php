<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidencia;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class IncidenciaController extends Controller
{
    public function index()
    {
        return "Mis Incidencias como Cliente";
    }

    public function indexGestor()
    {
        $user = Auth::user();
        
        // Ahora sí encontrará la clase Incidencia
        $incidencies = Incidencia::where('sede_id', $user->sede_id)
            ->where('estat', 'Sense assignar')
            ->get();

        $tecnics = User::where('sede_id', $user->sede_id)
            ->where('rol', 'tecnic')
            ->where('actiu', true)
            ->get();

        return view('gestor.index', compact('incidencies', 'tecnics'));
    }

    public function assignarTecnic(Request $request, $id)
    {
        $incidencia = Incidencia::findOrFail($id);
        
        $incidencia->update([
            'tecnic_id' => $request->tecnic_id,
            'estat' => 'Assignada'
        ]);

        return back()->with('success', 'Técnico asignado correctamente.');
    }

}
