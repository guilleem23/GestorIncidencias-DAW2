<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TecnicController extends Controller
{
    public function index()
    {
        // Obtener el técnico autenticado
        $tecnic = Auth::user();
        
        // Obtener incidencias asignadas a este técnico
        // Filtrar por defecto para no mostrar las cerradas
        $incidencies = Incidencia::where('tecnic_id', $tecnic->id)
            ->whereIn('estat', ['Assignada', 'En treball', 'Resolta'])
            ->with(['cliente', 'categoria', 'subcategoria', 'comentarios.usuario'])
            ->orderBy('prioritat', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('tecnic.index', compact('incidencies'));
    }

    public function iniciarTreball($id)
    {
        $incidencia = Incidencia::findOrFail($id);
        
        // Verificar que es del técnico autenticado
        if ($incidencia->tecnic_id !== Auth::id()) {
            return back()->with('error', 'No tens permís per modificar aquesta incidència.');
        }

        // Cambiar estado a "En treball"
        $incidencia->estat = 'En treball';
        $incidencia->data_inici_treball = now();
        $incidencia->save();

        return back()->with('success', 'Incidència marcada com "En treball".');
    }

    public function marcarResolta($id)
    {
        $incidencia = Incidencia::findOrFail($id);
        
        // Verificar que es del técnico autenticado
        if ($incidencia->tecnic_id !== Auth::id()) {
            return back()->with('error', 'No tens permís per modificar aquesta incidència.');
        }

        // Cambiar estado a "Resolta"
        $incidencia->estat = 'Resolta';
        $incidencia->data_resolucio = now();
        $incidencia->save();

        return back()->with('success', 'Incidència marcada com "Resolta". El client la pot tancar.');
    }
}
