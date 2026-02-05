<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidencia;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class IncidenciaController extends Controller
{
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

    public function cambiarEstado(Request $request, $id){
        $incidencia= Incidencia::findOrFail($id);

        // Validar que el técnico sea el asignado
        if ($incidencia->tecnic_id !== Auth::id()) {
            return back()->with('error', 'No tens permisos per canviar aquesta incidència.');
        }
        $nouEstat = $request->input('estat');
        // Actualizar estado y fechas correspondientes
        $incidencia->estat = $nouEstat;

        if ($nouEstat === 'En treball' && !$incidencia->data_inici_treball) {
            $incidencia->data_inici_treball = now();
        }

        if ($nouEstat === 'Resolta') {
            $incidencia->data_resolucio = now();
        }
        $incidencia->save();

        return back()->with('success', 'Estado de la incidencia actualizado correctamente.');
    }
}
