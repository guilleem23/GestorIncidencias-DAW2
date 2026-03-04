<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\User;
use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminIncidenciaController extends Controller
{
    /**
     * Muestra la lista global de incidencias para el panel de admin.
     */
    public function index(Request $request)
    {
        $query = Incidencia::with(['cliente', 'sede', 'tecnico']);

        // Search
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('id', $buscar)
                  ->orWhere('titol', 'LIKE', "%{$buscar}%")
                  ->orWhere('descripcio', 'LIKE', "%{$buscar}%")
                  ->orWhereHas('cliente', fn($q2) => $q2->where('name', 'LIKE', "%{$buscar}%"));
            });
        }

        // Filters
        if ($request->filled('estat')) {
            $query->where('estat', $request->estat);
        }
        if ($request->filled('prioritat')) {
            $query->where('prioritat', $request->prioritat);
        }
        if ($request->filled('sede_id')) {
            $query->where('sede_id', $request->sede_id);
        }

        // Sorting
        $orden = $request->get('orden', 'desc');
        $orden = in_array($orden, ['asc', 'desc'], true) ? $orden : 'desc';
        $query->orderBy('created_at', $orden);

        $incidencias = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('admin.partials.tabla_incidencias', compact('incidencias'));
        }

        $sedes = \App\Models\Sede::orderBy('nom')->get();

        return view('admin.admin_dashboard_incidencias', compact('incidencias', 'sedes'));
    }

    /**
     * Muestra el detalle de una incidencia.
     */
    public function show($id)
    {
        $incidencia = Incidencia::with(['cliente', 'sede', 'tecnico', 'categoria', 'subcategoria', 'comentarios.usuario'])->findOrFail($id);
        return view('admin.ver_incidencia', compact('incidencia'));
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

    /**
     * Muestra el formulario de edición de una incidencia.
     */
    public function edit($id)
    {
        $incidencia = Incidencia::with(['tecnico', 'cliente', 'categoria', 'subcategoria'])->findOrFail($id);

        $tecnicos = User::where('sede_id', $incidencia->sede_id)
            ->where('rol', 'tecnic')
            ->where('actiu', true)
            ->get();

        $categorias = \App\Models\Categoria::with('subcategorias')->get();

        return view('admin.editar_incidencia', compact('incidencia', 'tecnicos', 'categorias'));
    }

    /**
     * Actualiza una incidencia.
     */
    public function update(Request $request, $id)
    {
        $incidencia = Incidencia::findOrFail($id);

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
            return back()->withErrors(['estat' => 'El estado no puede ser "Sin asignar" si hay un técnico asignado.'])->withInput();
        }

        if (empty($validated['tecnic_id']) && $validated['estat'] !== 'Sense assignar') {
            return back()->withErrors(['tecnic_id' => 'Debe asignar un técnico si el estado no es "Sin asignar".'])->withInput();
        }

        $incidencia->update($validated);

        return redirect()->route('admin.incidencias')->with('success', 'Incidencia actualizada correctamente.');
    }

    public function storeComentario(Request $request, $id)
    {
        $validated = $request->validate([
            'missatge' => ['required_without:imatge', 'nullable', 'string', 'min:2', 'max:2000'],
            'imatge' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:4096'],
        ], [
            'missatge.required_without' => 'Debes escribir un comentario o adjuntar una imagen.',
            'missatge.min' => 'El comentario debe tener al menos 2 caracteres.',
            'missatge.max' => 'El comentario no puede superar 2000 caracteres.',
            'imatge.image' => 'El archivo adjunto debe ser una imagen.',
            'imatge.mimes' => 'La imagen debe ser JPG, JPEG, PNG, GIF o WEBP.',
            'imatge.max' => 'La imagen no puede superar 4MB.',
        ]);

        $incidencia = Incidencia::findOrFail($id);

        $imatgePath = null;
        if ($request->hasFile('imatge')) {
            $imatgePath = $request->file('imatge')->store('comentarios', 'public');
        }

        Comentario::create([
            'incidencia_id' => $incidencia->id,
            'usuario_id' => Auth::id(),
            'missatge' => $validated['missatge'] ?? '',
            'imatge_path' => $imatgePath,
        ]);

        return back()->with('success', 'Comentario añadido correctamente.');
    }
}
