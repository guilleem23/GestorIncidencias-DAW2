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
        $incidencies = Incidencia::with(['cliente'])
            ->where('sede_id', $user->sede_id)
            ->whereNull('tecnic_id')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $tecnics = User::where('sede_id', $user->sede_id)
            ->where('rol', 'tecnic')
            ->where('actiu', true)
            ->orderBy('name')
            ->get();

        return view('gestor.index', compact('incidencies', 'tecnics'));
    }

    public function indexGestorTodas(Request $request)
    {
        $user = Auth::user();
        
        $query = Incidencia::with(['tecnico', 'cliente'])
            ->where('sede_id', $user->sede_id);

        // Text search
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('titol', 'like', "%{$buscar}%")
                  ->orWhere('descripcio', 'like', "%{$buscar}%")
                  ->orWhereHas('cliente', function($q2) use ($buscar) {
                      $q2->where('name', 'like', "%{$buscar}%");
                  });
            });
        }

        // Filters
        if ($request->filled('estat')) {
            $query->where('estat', $request->estat);
        }

        if ($request->filled('prioritat')) {
            $query->where('prioritat', $request->prioritat);
        }

        if ($request->filled('tecnic_id')) {
            $query->where('tecnic_id', $request->tecnic_id);
        }

        // Sorting
        $orden = $request->get('orden', 'desc'); // default desc
        $query->orderBy('created_at', $orden);

        $incidencies = $query->paginate(10)->withQueryString();

        $tecnicos = User::where('sede_id', $user->sede_id)
            ->where('rol', 'tecnic')
            ->where('actiu', true)
            ->orderBy('name')
            ->get();

        if ($request->ajax()) {
            return view('gestor.partials.incidencias_table', compact('incidencies'))->render();
        }

        return view('gestor.historial', compact('incidencies', 'tecnicos'));
    }

    public function showGestor($id)
    {
        $user = Auth::user();
        $incidencia = Incidencia::with(['tecnico', 'cliente', 'categoria', 'subcategoria'])->findOrFail($id);

        if ($incidencia->sede_id !== $user->sede_id) {
            abort(403, 'No tienes permiso para ver esta incidencia.');
        }

        return view('gestor.ver_incidencia', compact('incidencia'));
    }

    public function editGestor($id)
    {
        $user = Auth::user();
        $incidencia = Incidencia::with(['tecnico', 'cliente', 'categoria', 'subcategoria'])->findOrFail($id);

        if ($incidencia->sede_id !== $user->sede_id) {
            abort(403, 'No tienes permiso para editar esta incidencia.');
        }

        $tecnicos = User::where('sede_id', $user->sede_id)
            ->where('rol', 'tecnic')
            ->where('actiu', true)
            ->get();
            
        $categorias = \App\Models\Categoria::with('subcategorias')->get();

        return view('gestor.editar_incidencia', compact('incidencia', 'tecnicos', 'categorias'));
    }

    public function updateGestor(Request $request, $id)
    {
        $user = Auth::user();
        $incidencia = Incidencia::findOrFail($id);

        if ($incidencia->sede_id !== $user->sede_id) {
            abort(403, 'No tienes permiso para actualizar esta incidencia.');
        }

        $validated = $request->validate([
            'titol' => 'required|string|max:255',
            'descripcio' => 'required|string',
            'categoria_id' => 'required|exists:categorias,id',
            'subcategoria_id' => 'required|exists:subcategorias,id',
            'tecnic_id' => 'nullable|exists:usuarios,id',
            'estat' => 'required|in:Sense assignar,Assignada,En treball,Resolta,Tancada',
            'prioritat' => 'required|in:alta,mitjana,baixa',
        ]);

        if (!empty($validated['tecnic_id']) && $validated['estat'] === 'Sense assignar') {
            return back()->withErrors(['estat' => 'El estado no puede ser "Sense assignar" si hay un técnico asignado.'])->withInput();
        }

        if (empty($validated['tecnic_id']) && $validated['estat'] !== 'Sense assignar') {
            return back()->withErrors(['tecnic_id' => 'Debe asignar un técnico si el estado no es "Sense assignar".'])->withInput();
        }

        $incidencia->update($validated);

        return redirect()->route('gestor.incidencias')->with('success', 'Incidencia actualizada correctamente.');
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
