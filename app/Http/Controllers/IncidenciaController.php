<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidencia;
use App\Models\User;
use App\Models\Comentario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        // Soporte para AJAX (Filtros y Paginación)
        if ($request->ajax() || $request->wantsJson() || $request->has('ajax')) {
            return view('gestor.partials.incidencias_table', compact('incidencies'));
        }

        $categorias = \App\Models\Categoria::with('subcategorias')->get();

        return view('gestor.historial', compact('incidencies', 'tecnicos', 'categorias'));
    }

    public function showGestor($id)
    {
        $user = Auth::user();
        $incidencia = Incidencia::with(['tecnico', 'cliente', 'categoria', 'subcategoria', 'comentarios.usuario'])->findOrFail($id);

        if ($incidencia->sede_id !== $user->sede_id) {
            abort(403, 'No tienes permiso para ver esta incidencia.');
        }

        $categorias = \App\Models\Categoria::with('subcategorias')->get();

        return view('gestor.ver_incidencia', compact('incidencia', 'categorias'));
    }

    public function storeComentarioGestor(Request $request, $id)
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

        $user = Auth::user();
        $incidencia = Incidencia::findOrFail($id);

        if ((int) $incidencia->sede_id !== (int) $user->sede_id) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para comentar esta incidencia.'
                ], 403);
            }
            abort(403, 'No tienes permiso para comentar esta incidencia.');
        }

        $imagePath = null;
        if ($request->hasFile('imatge')) {
            $imagePath = $request->file('imatge')->store('comentarios', 'public');
        }

        $comentario = Comentario::create([
            'incidencia_id' => $incidencia->id,
            'usuario_id' => $user->id,
            'missatge' => $validated['missatge'] ?? '',
            'imatge_path' => $imagePath,
        ]);

        // Cargar la relación del usuario
        $comentario->load('usuario');

        if ($request->ajax()) {
            $html = view('gestor.partials.comentario_item', compact('comentario'))->render();
            return response()->json([
                'success' => true,
                'message' => 'Comentario añadido correctamente.',
                'html' => $html
            ]);
        }

        return back()->with('success', 'Comentario añadido correctamente.');
    }

    public function destroyComentarioGestor($id)
    {
        $user = Auth::user();
        $comentario = Comentario::with('incidencia')->findOrFail($id);

        // Solo el gestor dueño del comentario puede borrarlo. (O podrías permitir que el gestor borre cualquiera de su sede, pero el usuario pidió "si un comentario es tuyo")
        if ((int) $comentario->usuario_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar este comentario.'
            ], 403);
        }

        // Además verificamos que sea de su sede por seguridad extra
        if ((int) $comentario->incidencia->sede_id !== (int) $user->sede_id) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado.'
            ], 403);
        }

        // Eliminar la imagen del storage si existe
        if (!empty($comentario->imatge_path)) {
            Storage::disk('public')->delete($comentario->imatge_path);
        }

        $comentario->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comentario eliminado.'
        ]);
    }

    public function editComentarioGestor($id)
    {
        $user = Auth::user();
        $comentario = Comentario::with('incidencia')->findOrFail($id);

        // Verificar que el usuario es el dueño del comentario
        if ((int) $comentario->usuario_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para editar este comentario.'
            ], 403);
        }

        // Verificar que sea de su sede
        if ((int) $comentario->incidencia->sede_id !== (int) $user->sede_id) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado.'
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

        abort(404);
    }

    public function updateComentarioGestor(Request $request, $id)
    {
        $user = Auth::user();
        $comentario = Comentario::with('incidencia')->findOrFail($id);

        // Verificar que el usuario es el dueño del comentario
        if ((int) $comentario->usuario_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para editar este comentario.'
            ], 403);
        }

        // Verificar que sea de su sede
        if ((int) $comentario->incidencia->sede_id !== (int) $user->sede_id) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado.'
            ], 403);
        }

        $validated = $request->validate([
            'missatge' => 'required_without:imatge|string|min:2|max:2000',
            'imatge' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096'
        ]);

        // Actualizar mensaje
        if (!empty($validated['missatge'])) {
            $comentario->missatge = $validated['missatge'];
        }

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
                'html' => view('gestor.partials.comentario_item', ['comentario' => $comentario])->render()
            ]);
        }

        return back()->with('success', 'Comentario actualizado correctamente.');
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

        if (request()->ajax()) {
            return view('gestor.partials.editar_incidencia_form', compact('incidencia', 'tecnicos', 'categorias'));
        }

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
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El estado no puede ser "Sense assignar" si hay un técnico asignado.',
                    'errors' => ['estat' => ['El estado no puede ser "Sense assignar" si hay un técnico asignado.']]
                ], 422);
            }
            return back()->withErrors(['estat' => 'El estado no puede ser "Sense assignar" si hay un técnico asignado.'])->withInput();
        }

        if (empty($validated['tecnic_id']) && $validated['estat'] !== 'Sense assignar') {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe asignar un técnico si el estado no es "Sense assignar".',
                    'errors' => ['tecnic_id' => ['Debe asignar un técnico si el estado no es "Sense assignar".']]
                ], 422);
            }
            return back()->withErrors(['tecnic_id' => 'Debe asignar un técnico si el estado no es "Sense assignar".'])->withInput();
        }

        $incidencia->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Incidencia actualizada correctamente.'
            ]);
        }

        return back()->with('success', 'Incidencia actualizada correctamente.');
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
            $msg = 'El técnico seleccionado no es válido o no está activo.';
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                    'errors' => ['tecnic_id' => [$msg]]
                ], 422);
            }
            return back()->withErrors(['tecnic_id' => $msg]);
        }

        if ((int) $tecnic->sede_id !== (int) $incidencia->sede_id) {
            $msg = 'No puedes asignar un técnico de otra sede a esta incidencia.';
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                    'errors' => ['tecnic_id' => [$msg]]
                ], 422);
            }
            return back()->withErrors(['tecnic_id' => $msg]);
        }

        $incidencia->update([
            'tecnic_id' => $tecnic->id,
            'estat' => 'Assignada',
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Técnico asignado correctamente.',
                'incidencia_id' => $incidencia->id
            ]);
        }

        return back()->with('success', 'Técnico asignado correctamente.');
    }

    public function destroyGestor(Request $request, $id)
    {
        $user = Auth::user();
        $incidencia = Incidencia::findOrFail($id);

        if ((int) $incidencia->sede_id !== (int) $user->sede_id) {
            $msg = 'No tienes permiso para eliminar esta incidencia de otra sede.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->with('error', $msg);
        }

        // Primero eliminar comentarios para evitar el error de clave foránea
        $incidencia->comentarios()->delete();
        $incidencia->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Incidencia eliminada con éxito.'
            ]);
        }

        return redirect()->route('gestor.incidencias')->with('success', 'Incidencia eliminada con éxito.');
    }
}
