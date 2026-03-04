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
        
        // Solo tareas activas (sin cerradas)
        $incidencies = Incidencia::where('tecnic_id', $tecnic->id)
            ->whereIn('estat', ['Assignada', 'En treball', 'Resolta'])
            ->with(['cliente', 'categoria', 'subcategoria', 'comentarios.usuario'])
            ->orderBy('prioritat', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Contador de incidencias cerradas
        $incidenciesTancades = Incidencia::where('tecnic_id', $tecnic->id)
            ->where('estat', 'Tancada')
            ->count();

        return view('tecnic.index', compact('incidencies', 'incidenciesTancades'));
    }

    public function totesTasques(Request $request)
    {
        // Obtener el técnico autenticado
        $tecnic = Auth::user();
        
        // Obtener filtros
        $estatFilter = $request->get('estat');
        
        // Construir query base - TODAS las incidencias del técnico
        $query = Incidencia::where('tecnic_id', $tecnic->id)
            ->with(['cliente', 'categoria', 'subcategoria', 'comentarios.usuario']);
        
        // Aplicar filtro por estado si existe
        if ($estatFilter) {
            $query->where('estat', $estatFilter);
        }
        
        // Ordenar
        $query->orderBy('prioritat', 'desc')
              ->orderBy('created_at', 'asc');
        
        $incidencies = $query->get();
        
        // Contador de incidencias cerradas
        $incidenciesTancades = Incidencia::where('tecnic_id', $tecnic->id)
            ->where('estat', 'Tancada')
            ->count();
        
        // Estados disponibles
        $estats = [
            'Assignada' => 'Asignada',
            'En treball' => 'En trabajo',
            'Resolta' => 'Resuelta',
            'Tancada' => 'Cerrada',
        ];
        
        // Si es petición AJAX, devolver JSON
        if ($request->ajax()) {
            $fullHtml = view('tecnic.totes', compact('incidencies', 'incidenciesTancades', 'estats', 'estatFilter'))->render();
            
            // Extraer el contenido del div incidencias-container
            if (preg_match('/<div id="incidencias-container">(.*?)<\/div>\s*<!--\s*Loader AJAX/s', $fullHtml, $matches)) {
                $html = $matches[1];
            } else {
                $html = '';
            }
            
            // Calcular estadísticas
            $stats = [
                'assignades' => Incidencia::where('tecnic_id', $tecnic->id)->where('estat', 'Assignada')->count(),
                'enTreball' => Incidencia::where('tecnic_id', $tecnic->id)->where('estat', 'En treball')->count(),
                'resoltes' => Incidencia::where('tecnic_id', $tecnic->id)->where('estat', 'Resolta')->count(),
                'tancades' => $incidenciesTancades,
                'total' => $incidencies->count(),
            ];
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'stats' => $stats,
                'count' => $incidencies->count()
            ]);
        }

        return view('tecnic.totes', compact('incidencies', 'incidenciesTancades', 'estats', 'estatFilter'));
    }

    /**
     * Muestra el detalle de una incidencia.
     */
    public function show($id)
    {
        $incidencia = Incidencia::with(['cliente', 'sede', 'tecnico', 'categoria', 'subcategoria', 'comentarios.usuario'])->findOrFail($id);
        
        // Verificar que la incidencia pertenece al técnico autenticado
        if ($incidencia->tecnic_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver esta incidencia.');
        }

        return view('tecnic.ver_incidencia', compact('incidencia'));
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

        if ((int) $incidencia->tecnic_id !== (int) Auth::id()) {
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
}
