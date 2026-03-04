<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('client.index', compact('incidencies', 'estats', 'estatFilter', 'ordenFilter', 'ocultarResoltes'));
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

        if ((int) $incidencia->client_id !== (int) Auth::id()) {
            abort(403, 'No tienes permiso para comentar esta incidencia.');
        }

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

        return back()->with('success', 'Incidència tancada correctament.');
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
}
