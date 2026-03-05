<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\User;
use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    public function storeComentario(Request $request, $id)
    {
        $validated = $request->validate([
            'missatge' => ['required_without:imatge', 'nullable', 'string', 'min:1', 'max:2000'],
            'imatge' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:4096'],
        ], [
            'missatge.required_without' => 'Debes escribir un comentario o adjuntar una imagen.',
            'missatge.min' => 'El comentario debe tener al menos 1 carácter.',
            'missatge.max' => 'El comentario no puede superar 2000 caracteres.',
            'imatge.image' => 'El archivo adjunto debe ser una imagen.',
            'imatge.mimes' => 'La imagen debe ser JPG, JPEG, PNG, GIF o WEBP.',
            'imatge.max' => 'La imagen no puede superar 4MB.',
        ]);

        $incidencia = Incidencia::findOrFail($id);

        $imagePath = null;
        if ($request->hasFile('imatge')) {
            $imagePath = $request->file('imatge')->store('comentarios', 'public');
        }

        $comentario = Comentario::create([
            'incidencia_id' => $incidencia->id,
            'usuario_id' => Auth::id(),
            'missatge' => $validated['missatge'] ?? '',
            'imatge_path' => $imagePath,
        ]);

        // Cargar la relación del usuario
        $comentario->load('usuario');

        // Si es una petición AJAX, retornar JSON
        if ($request->ajax() || $request->wantsJson()) {
            $html = view('admin.partials.comentario_item', compact('comentario'))->render();
            return response()->json([
                'success' => true,
                'message' => 'Comentario añadido correctamente.',
                'html' => $html,
            ]);
        }

        return back()->with('success', 'Comentario añadido correctamente.');
    }

    /**
     * Elimina un comentario.
     */
    public function destroyComentario($id)
    {
        $comentario = Comentario::findOrFail($id);

        // Verificar que el usuario es el dueño del comentario o es admin
        if ($comentario->usuario_id !== Auth::id() && Auth::user()->rol !== 'admin') {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para eliminar este comentario.',
                ], 403);
            }
            return back()->withErrors(['error' => 'No tienes permiso para eliminar este comentario.']);
        }

        // Eliminar la imagen del storage si existe
        if (!empty($comentario->imatge_path)) {
            Storage::disk('public')->delete($comentario->imatge_path);
        }

        $comentario->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comentario eliminado correctamente.',
            ]);
        }

        return back()->with('success', 'Comentario eliminado correctamente.');
    }

    /**
     * Obtiene los datos de un comentario para editar.
     */
    public function editComentario($id)
    {
        $comentario = Comentario::findOrFail($id);

        // Verificar que el usuario es el dueño del comentario o es admin
        if ($comentario->usuario_id !== Auth::id() && Auth::user()->rol !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para editar este comentario.'
            ], 403);
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $comentario->id,
                    'missatge' => $comentario->missatge,
                    'imatge_path' => $comentario->imatge_path
                ]
            ]);
        }
    }

    /**
     * Actualiza un comentario.
     */
    public function updateComentario(Request $request, $id)
    {
        $comentario = Comentario::findOrFail($id);

        // Verificar que el usuario es el dueño del comentario o es admin
        if ($comentario->usuario_id !== Auth::id() && Auth::user()->rol !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para editar este comentario.'
            ], 403);
        }

        // Si ya tiene imagen, el mensaje es opcional. Si no, es requerido a menos que se suba una nueva.
        $rules = [
            'missatge' => ($comentario->imatge_path ? 'nullable' : 'required_without:imatge|nullable') . '|string|min:1|max:2000',
            'imatge' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096'
        ];

        $validated = $request->validate($rules);

        // Actualizar mensaje (aunque sea vacío)
        $comentario->missatge = $validated['missatge'] ?? '';

        // Manejar imagen
        if ($request->hasFile('imatge')) {
            // Eliminar imagen anterior si existe
            if (!empty($comentario->imatge_path)) {
                Storage::disk('public')->delete($comentario->imatge_path);
            }
            // Guardar nueva imagen
            $path = $request->file('imatge')->store('comentarios', 'public');
            $comentario->imatge_path = $path;
        }

        $comentario->save();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comentario actualizado correctamente.',
                'html' => view('admin.partials.comentario_item', ['comentario' => $comentario])->render()
            ]);
        }

        return back()->with('success', 'Comentario actualizado correctamente.');
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
                'error' => 'No se pudo asignar el técnico. Inténtalo de nuevo.',
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

        if (request()->ajax()) {
            return view('admin.partials.editar_incidencia_form', compact('incidencia', 'tecnicos', 'categorias'));
        }

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
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El estado no puede ser "Sin asignar" si hay un técnico asignado.',
                    'errors' => ['estat' => ['El estado no puede ser "Sin asignar" si hay un técnico asignado.']]
                ], 422);
            }
            return back()->withErrors(['estat' => 'El estado no puede ser "Sin asignar" si hay un técnico asignado.'])->withInput();
        }

        if (empty($validated['tecnic_id']) && $validated['estat'] !== 'Sense assignar') {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe asignar un técnico si el estado no es "Sin asignar".',
                    'errors' => ['tecnic_id' => ['Debe asignar un técnico si el estado no es "Sin asignar".']]
                ], 422);
            }
            return back()->withErrors(['tecnic_id' => 'Debe asignar un técnico si el estado no es "Sin asignar".'])->withInput();
        }

        $incidencia->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Incidencia actualizada correctamente.',
                'redirect' => route('admin.incidencias.show', $incidencia->id)
            ]);
        }

        return redirect()->route('admin.incidencias')->with('success', 'Incidencia actualizada correctamente.');
    }

    public function destroy(Request $request, $id)
    {
        $incidencia = Incidencia::findOrFail($id);
        
        // Eliminar imágenes de los comentarios
        foreach ($incidencia->comentarios as $comentario) {
            if ($comentario->imatge_path && Storage::disk('public')->exists($comentario->imatge_path)) {
                Storage::disk('public')->delete($comentario->imatge_path);
            }
        }
        
        // Eliminar comentarios para evitar error de clave foránea
        $incidencia->comentarios()->delete();
        $incidencia->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Incidencia eliminada con éxito.'
            ]);
        }

        return redirect()->route('admin.incidencias')->with('success', 'Incidencia eliminada con éxito.');
    }
}
