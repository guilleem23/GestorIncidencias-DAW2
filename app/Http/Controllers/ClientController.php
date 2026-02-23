<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
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
            ->with(['tecnico', 'categoria', 'subcategoria']);

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
        // Cargar sedes, categorías para el formulario
        $sedes = \App\Models\Sede::all();
        $categorias = \App\Models\Categoria::with('subcategorias')->get();
        
        return view('client.crear', compact('sedes', 'categorias'));
    }

    public function store(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            'titol' => 'required|string|max:255',
            'descripcio' => 'required|string',
            'sede_id' => 'required|exists:sedes,id',
            'categoria_id' => 'required|exists:categorias,id',
            'subcategoria_id' => 'required|exists:subcategorias,id',
        ], [
            'titol.required' => 'El títol és obligatori',
            'descripcio.required' => 'La descripció és obligatòria',
            'sede_id.required' => 'Has de seleccionar una seu',
            'categoria_id.required' => 'Has de seleccionar una categoria',
            'subcategoria_id.required' => 'Has de seleccionar una subcategoria',
        ]);

        // Crear la incidencia
        $incidencia = new \App\Models\Incidencia();
        $incidencia->titol = $validated['titol'];
        $incidencia->descripcio = $validated['descripcio'];
        $incidencia->sede_id = $validated['sede_id'];
        $incidencia->categoria_id = $validated['categoria_id'];
        $incidencia->subcategoria_id = $validated['subcategoria_id'];
        $incidencia->client_id = Auth::id(); // El cliente autenticado
        $incidencia->estat = 'Sense assignar'; // Estado inicial
        $incidencia->save();

        return redirect()->route('client.index')->with('success', 'Incidència creada correctament!');
    }
}
