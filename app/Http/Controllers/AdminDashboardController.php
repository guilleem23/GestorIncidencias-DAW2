<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\Sede;
use App\Models\User;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // --- KPI 1: Total Usuarios ---
        $totalUsuarios = User::count();

        // --- KPI 2: Incidencias Activas (no cerradas) ---
        $incidenciasActivas = Incidencia::where('estat', '!=', 'Tancada')->count();

        // --- KPI 3: Tiempo Medio de Resolución (creación → cierre) ---
        $avgResolutionSeconds = DB::table('incidencias')
            ->where('estat', 'Tancada')
            ->whereNotNull('data_resolucio')
            ->avg(DB::raw('TIMESTAMPDIFF(SECOND, data_creacio, data_resolucio)'));

        $tiempoMedioResolucion = null;
        if ($avgResolutionSeconds !== null) {
            $hours = (float) $avgResolutionSeconds / 3600;
            if ($hours >= 24) {
                $tiempoMedioResolucion = round($hours / 24, 1) . 'd';
            } else {
                $tiempoMedioResolucion = round($hours, 1) . 'h';
            }
        }

        // --- KPI 4: Pendientes de Asignación (count global) ---
        $pendientesCount = Incidencia::where('estat', 'Sense assignar')->count();

        // --- Incidencias por Sede (todas las sedes dinámicamente) ---
        $sedes = Sede::orderBy('nom')->get();
        $incidenciasPorSede = Incidencia::select('sede_id', DB::raw('COUNT(*) as total'))
            ->groupBy('sede_id')
            ->pluck('total', 'sede_id');

        $sedeStats = [];
        $maxCount = 0;
        foreach ($sedes as $sede) {
            $count = (int) ($incidenciasPorSede[$sede->id] ?? 0);
            $sedeStats[] = [
                'nom' => $sede->nom,
                'count' => $count,
            ];
            if ($count > $maxCount) $maxCount = $count;
        }

        // Compute bar heights
        $minHeight = 40;
        $maxHeight = 200;
        foreach ($sedeStats as &$s) {
            $s['height'] = $maxCount > 0
                ? (int) round($minHeight + (($s['count'] / $maxCount) * ($maxHeight - $minHeight)))
                : (int) (($minHeight + $maxHeight) / 2);
        }
        unset($s);

        // --- Tipología de problemas (todas las categorías) ---
        $categoriaRows = DB::table('incidencias')
            ->join('categorias', 'incidencias.categoria_id', '=', 'categorias.id')
            ->select('categorias.nom', DB::raw('COUNT(*) as total'))
            ->groupBy('categorias.nom')
            ->orderByDesc('total')
            ->get();

        $categoriaTotal = $categoriaRows->sum('total');
        $tipologias = [];
        foreach ($categoriaRows as $row) {
            $tipologias[] = [
                'nom' => $row->nom,
                'count' => (int) $row->total,
                'percent' => $categoriaTotal > 0 ? round(((int) $row->total / $categoriaTotal) * 100) : 0,
            ];
        }

        // --- Tabla: Incidencias pendientes de asignación ---
        $pendientesAsignacion = Incidencia::with('sede', 'categoria')
            ->where('estat', 'Sense assignar')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.admin_dashboard_principal', compact(
            'totalUsuarios',
            'incidenciasActivas',
            'tiempoMedioResolucion',
            'pendientesCount',
            'sedeStats',
            'tipologias',
            'categoriaTotal',
            'pendientesAsignacion'
        ));
    }
}
