<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidencia;
use App\Models\User;
use App\Models\Comentario;
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
                $q->where('id', $buscar)
                  ->orWhere('titol', 'like', "%{$buscar}%")
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
        $orden = in_array($orden, ['asc', 'desc'], true) ? $orden : 'desc';
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
        $incidencia = Incidencia::with(['tecnico', 'cliente', 'categoria', 'subcategoria', 'comentarios.usuario'])->findOrFail($id);

        if ($incidencia->sede_id !== $user->sede_id) {
            abort(403, 'No tienes permiso para ver esta incidencia.');
        }

        return view('gestor.ver_incidencia', compact('incidencia'));
    }

    public function storeComentarioGestor(Request $request, $id)
    {
        $validated = $request->validate([
            'missatge' => ['required', 'string', 'min:2', 'max:2000'],
        ], [
            'missatge.required' => 'El comentario es obligatorio.',
            'missatge.min' => 'El comentario debe tener al menos 2 caracteres.',
            'missatge.max' => 'El comentario no puede superar 2000 caracteres.',
        ]);

        $user = Auth::user();
        $incidencia = Incidencia::findOrFail($id);

        if ((int) $incidencia->sede_id !== (int) $user->sede_id) {
            abort(403, 'No tienes permiso para comentar esta incidencia.');
        }

        Comentario::create([
            'incidencia_id' => $incidencia->id,
            'usuario_id' => $user->id,
            'missatge' => $validated['missatge'],
        ]);

        return back()->with('success', 'Comentario añadido correctamente.');
    }

    public function editGestor($id)
    {
        $user = Auth::user();
        $incidencia = Incidencia::with(['tecnico', 'cliente', 'categoria', 'subcategoria', 'comentarios.usuario'])->findOrFail($id);

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
        $user = Auth::user();

        $validated = $request->validate([
            'tecnic_id' => ['required', 'integer', 'exists:usuarios,id'],
        ]);

        $incidencia = Incidencia::findOrFail($id);

        if ((int) $incidencia->sede_id !== (int) $user->sede_id) {
            abort(403, 'No tienes permiso para modificar esta incidencia.');
        }

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

        $incidencia->update([
            'tecnic_id' => $tecnic->id,
            'estat' => 'Assignada',
        ]);

        return back()->with('success', 'Técnico asignado correctamente.');
    }

}
