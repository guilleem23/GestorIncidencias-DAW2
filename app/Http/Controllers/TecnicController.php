<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
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

    public function storeComentario(Request $request, $id)
    {
        $validated = $request->validate([
            'missatge' => ['required', 'string', 'min:2', 'max:2000'],
        ], [
            'missatge.required' => 'El comentario es obligatorio.',
            'missatge.min' => 'El comentario debe tener al menos 2 caracteres.',
            'missatge.max' => 'El comentario no puede superar 2000 caracteres.',
        ]);

        $incidencia = Incidencia::findOrFail($id);

        if ((int) $incidencia->tecnic_id !== (int) Auth::id()) {
            abort(403, 'No tienes permiso para comentar esta incidencia.');
        }

        Comentario::create([
            'incidencia_id' => $incidencia->id,
            'usuario_id' => Auth::id(),
            'missatge' => $validated['missatge'],
        ]);

        return back()->with('success', 'Comentario añadido correctamente.');
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
