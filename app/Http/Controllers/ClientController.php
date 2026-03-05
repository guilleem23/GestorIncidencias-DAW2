<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        // Obtener el cliente autenticado
        $client = Auth::user();
        
        // Obtener filtros
        $estatFilter = $request->get('estat');
        $ordenFilter = $request->get('orden', 'desc'); // Por defecto DESC (más recientes primero)
        $ocultarResoltes = $request->get('ocultar_resoltes', false);

        // Construir query de incidencias del cliente
        $query = Incidencia::where('client_id', $client->id)
            ->with(['tecnico', 'categoria', 'subcategoria', 'comentarios.usuario']);

        // Filtro por estado
        if ($estatFilter) {
            $query->where('estat', $estatFilter);
        }

        // Ocultar resueltas y cerradas si está activado
        if ($ocultarResoltes) {
            $query->whereNotIn('estat', ['Resolta', 'Tancada']);
        }

        // Ordenar por fecha
        if ($ordenFilter === 'asc') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $incidencies = $query->get();

        // Estados disponibles
        $estats = [
            'Sense assignar' => 'Sense assignar',
            'Assignada' => 'Assignada',
            'En treball' => 'En treball',
            'Resolta' => 'Resolta',
            'Tancada' => 'Tancada',
        ];

        // Cargar categorías para el modal de edición
        $categorias = \App\Models\Categoria::with('subcategorias')->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('client.partials.incidencias_list', compact('incidencies'))->render(),
                'stats' => [
                    'senseAssignar' => $incidencies->where('estat', 'Sense assignar')->count(),
                    'enProces' => $incidencies->whereIn('estat', ['Assignada', 'En treball'])->count(),
                    'resoltes' => $incidencies->where('estat', 'Resolta')->count(),
                    'tancades' => $incidencies->where('estat', 'Tancada')->count(),
                ]
            ]);
        }

        return view('client.index', compact('incidencies', 'estats', 'estatFilter', 'ordenFilter', 'ocultarResoltes', 'categorias'));
    }

    public function verIncidencia($id)
    {
        $incidencia = Incidencia::with(['cliente', 'tecnico', 'categoria', 'subcategoria', 'sede', 'comentarios.usuario'])
            ->findOrFail($id);

        // Verificar que la incidencia pertenece al cliente autenticado
        if ((int) $incidencia->client_id !== (int) Auth::id()) {
            abort(403, 'No tienes permiso para ver esta incidencia.');
        }

        // Cargar categorías para el modal de edición
        $categorias = \App\Models\Categoria::with('subcategorias')->get();

        return view('client.ver_incidencia', compact('incidencia', 'categorias'));
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

        if ((int) $incidencia->client_id !== (int) Auth::id()) {
            abort(403, 'No tienes permiso para comentar esta incidencia.');
        }

        $imatgePath = null;
        if ($request->hasFile('imatge')) {
            $imatgePath = $request->file('imatge')->store('comentarios', 'public');
        }

        $comentario = Comentario::create([
            'incidencia_id' => $incidencia->id,
            'usuario_id' => Auth::id(),
            'missatge' => $validated['missatge'] ?? '',
            'imatge_path' => $imatgePath,
        ]);

        if ($request->ajax()) {
            $comentario->load('usuario');

            return response()->json([
                'success' => true,
                'message' => 'Comentario añadido correctamente.',
                'html' => view('client.partials.comentario_item', compact('comentario'))->render(),
            ]);
        }

        return back()->with('success', 'Comentario añadido correctamente.');
    }

    public function tancarIncidencia($id)
    {
        $incidencia = Incidencia::findOrFail($id);
        
        // Verificar que es del cliente autenticado
        if ($incidencia->client_id !== Auth::id()) {
            return back()->with('error', 'No tens permís per modificar aquesta incidència.');
        }

        // Verificar que está en estado "Resolta"
        if ($incidencia->estat !== 'Resolta') {
            return back()->with('error', 'Només pots tancar incidències que estiguin resoltes.');
        }

        // Cambiar estado a "Tancada"
        $incidencia->estat = 'Tancada';
        $incidencia->save();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Incidencia cerrada correctamente.'
            ]);
        }

        return back()->with('success', 'Incidencia cerrada correctamente.');
    }

    public function crear()
    {
        // Cargar categorías para el formulario
        $categorias = \App\Models\Categoria::with('subcategorias')->get();
        
        // Obtener la sede del cliente autenticado
        $sedeCliente = Auth::user()->sede;
        
        return view('client.crear', compact('categorias', 'sedeCliente'));
    }

    public function store(Request $request)
    {
        // Validar datos del formulario
        $validated = $request->validate([
            'titol' => 'required|string|min:3|max:255',
            'descripcio' => 'required|string|min:10|max:1000',
            'categoria_id' => 'required|exists:categorias,id',
            'subcategoria_id' => 'required|exists:subcategorias,id',
        ], [
            // Mensajes de error para título
            'titol.required' => 'El título es obligatorio',
            'titol.min' => 'El título debe tener al menos 3 caracteres',
            'titol.max' => 'El título no puede superar 255 caracteres',
            
            // Mensajes de error para descripción
            'descripcio.required' => 'La descripción es obligatoria',
            'descripcio.min' => 'La descripción debe tener al menos 10 caracteres',
            'descripcio.max' => 'La descripción no puede superar 1000 caracteres',
            
            // Mensajes de error para categoría
            'categoria_id.required' => 'Debes seleccionar una categoría',
            'categoria_id.exists' => 'La categoría seleccionada no es válida',
            
            // Mensajes de error para subcategoría
            'subcategoria_id.required' => 'Debes seleccionar una subcategoría',
            'subcategoria_id.exists' => 'La subcategoría seleccionada no es válida',
        ]);

        // Crear la incidencia
        $incidencia = new \App\Models\Incidencia();
        $incidencia->titol = $validated['titol'];
        $incidencia->descripcio = $validated['descripcio'];
        $incidencia->sede_id = Auth::user()->sede_id; // La sede del cliente autenticado
        $incidencia->categoria_id = $validated['categoria_id'];
        $incidencia->subcategoria_id = $validated['subcategoria_id'];
        $incidencia->client_id = Auth::id(); // El cliente autenticado
        $incidencia->estat = 'Sense assignar'; // Estado inicial
        $incidencia->save();

        return redirect()->route('client.index')->with('success', 'Incidència creada correctament!');
    }

    public function updateComentario(Request $request, $id)
    {
        $comentario = Comentario::findOrFail($id);

        // Verificar que el comentario pertenece al usuario autenticado
        if ((int) $comentario->usuario_id !== (int) Auth::id()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'No tienes permiso para editar este comentario.'], 403);
            }
            abort(403, 'No tienes permiso para editar este comentario.');
        }

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

        // Actualizar mensaje
        $comentario->missatge = $validated['missatge'] ?? '';

        // Si hay nueva imagen, reemplazar la anterior
        if ($request->hasFile('imatge')) {
            // Eliminar imagen anterior si existe
            if ($comentario->imatge_path && \Storage::disk('public')->exists($comentario->imatge_path)) {
                \Storage::disk('public')->delete($comentario->imatge_path);
            }
            $comentario->imatge_path = $request->file('imatge')->store('comentarios', 'public');
        }

        $comentario->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comentario actualizado correctamente.'
            ]);
        }

        return back()->with('success', 'Comentario actualizado correctamente.');
    }

    public function destroyComentario(Request $request, $id)
    {
        $comentario = Comentario::findOrFail($id);

        // Verificar que el comentario pertenece al usuario autenticado
        if ((int) $comentario->usuario_id !== (int) Auth::id()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'No tienes permiso para eliminar este comentario.'], 403);
            }
            abort(403, 'No tienes permiso para eliminar este comentario.');
        }

        // Eliminar imagen si existe
        if ($comentario->imatge_path && \Storage::disk('public')->exists($comentario->imatge_path)) {
            \Storage::disk('public')->delete($comentario->imatge_path);
        }

        $comentario->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comentario eliminado correctamente.'
            ]);
        }

        return back()->with('success', 'Comentario eliminado correctamente.');
    }

    public function editarIncidencia(Request $request, $id)
    {
        $incidencia = Incidencia::findOrFail($id);

        // Verificar que la incidencia pertenece al cliente autenticado
        if ((int) $incidencia->client_id !== (int) Auth::id()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'No tienes permiso para editar esta incidencia.'], 403);
            }
            abort(403, 'No tienes permiso para editar esta incidencia.');
        }

        // Solo se puede editar si está sin asignar o asignada (no en trabajo, resuelta o cerrada)
        if (!in_array($incidencia->estat, ['Sense assignar', 'Assignada'])) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Solo puedes editar incidencias que estén sin asignar o recién asignadas.'], 403);
            }
            return back()->with('error', 'Solo puedes editar incidencias que estén sin asignar o recién asignadas.');
        }

        // Si es petición AJAX, devolver los datos en JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'incidencia' => [
                    'id' => $incidencia->id,
                    'titol' => $incidencia->titol,
                    'descripcio' => $incidencia->descripcio,
                    'categoria_id' => $incidencia->categoria_id,
                    'subcategoria_id' => $incidencia->subcategoria_id,
                ]
            ]);
        }

        $categorias = \App\Models\Categoria::with('subcategorias')->get();
        $sedeCliente = Auth::user()->sede;

        return view('client.editar', compact('incidencia', 'categorias', 'sedeCliente'));
    }

    public function updateIncidencia(Request $request, $id)
    {
        $incidencia = Incidencia::findOrFail($id);

        // Verificar que la incidencia pertenece al cliente autenticado
        if ((int) $incidencia->client_id !== (int) Auth::id()) {
            abort(403, 'No tienes permiso para editar esta incidencia.');
        }

        // Solo se puede editar si está sin asignar o asignada
        if (!in_array($incidencia->estat, ['Sense assignar', 'Assignada'])) {
            return back()->with('error', 'Solo puedes editar incidencias que estén sin asignar o recién asignadas.');
        }

        $validated = $request->validate([
            'titol' => 'required|string|min:3|max:255',
            'descripcio' => 'required|string|min:10|max:1000',
            'categoria_id' => 'required|exists:categorias,id',
            'subcategoria_id' => 'required|exists:subcategorias,id',
        ], [
            'titol.required' => 'El título es obligatorio',
            'titol.min' => 'El título debe tener al menos 3 caracteres',
            'titol.max' => 'El título no puede superar 255 caracteres',
            'descripcio.required' => 'La descripción es obligatoria',
            'descripcio.min' => 'La descripción debe tener al menos 10 caracteres',
            'descripcio.max' => 'La descripción no puede superar 1000 caracteres',
            'categoria_id.required' => 'Debes seleccionar una categoría',
            'categoria_id.exists' => 'La categoría seleccionada no es válida',
            'subcategoria_id.required' => 'Debes seleccionar una subcategoría',
            'subcategoria_id.exists' => 'La subcategoría seleccionada no es válida',
        ]);

        $incidencia->titol = $validated['titol'];
        $incidencia->descripcio = $validated['descripcio'];
        $incidencia->categoria_id = $validated['categoria_id'];
        $incidencia->subcategoria_id = $validated['subcategoria_id'];
        $incidencia->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Incidencia actualizada correctamente.'
            ]);
        }

        return redirect()->route('client.index')->with('success', 'Incidencia actualizada correctamente.');
    }

    public function destroyIncidencia(Request $request, $id)
    {
        $incidencia = Incidencia::findOrFail($id);

        // Verificar que la incidencia pertenece al cliente autenticado
        if ((int) $incidencia->client_id !== (int) Auth::id()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'No tienes permiso para eliminar esta incidencia.'], 403);
            }
            abort(403, 'No tienes permiso para eliminar esta incidencia.');
        }

        // Solo se puede eliminar si está sin asignar
        if ($incidencia->estat !== 'Sense assignar') {
            if ($request->ajax()) {
                return response()->json(['error' => 'Solo puedes eliminar incidencias que estén sin asignar.'], 403);
            }
            return back()->with('error', 'Solo puedes eliminar incidencias que estén sin asignar.');
        }

        // Eliminar comentarios asociados y sus imágenes
        foreach ($incidencia->comentarios as $comentario) {
            if ($comentario->imatge_path && \Storage::disk('public')->exists($comentario->imatge_path)) {
                \Storage::disk('public')->delete($comentario->imatge_path);
            }
            $comentario->delete();
        }

        $incidencia->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Incidencia eliminada correctamente.'
            ]);
        }

        return redirect()->route('client.index')->with('success', 'Incidencia eliminada correctamente.');
    }
}
