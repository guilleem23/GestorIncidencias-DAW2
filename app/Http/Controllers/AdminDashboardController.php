<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsuarios = User::count();

        $incidenciasActivas = Incidencia::where('estat', '!=', 'Tancada')->count();

        $avgResolutionSeconds = DB::table('incidencias')
            ->whereNotNull('data_inici_treball')
            ->whereNotNull('data_resolucio')
            ->avg(DB::raw('TIMESTAMPDIFF(SECOND, data_inici_treball, data_resolucio)'));

        $tiempoMedioResolucionHoras = null;
        if ($avgResolutionSeconds !== null) {
            $tiempoMedioResolucionHoras = round(((float) $avgResolutionSeconds) / 3600, 1);
        }

        // "Satisfacción" derivada de estado (no hay rating en BD)
        $weights = [
            'Sense assignar' => 1,
            'Assignada' => 2,
            'En treball' => 3,
            'Resolta' => 4,
            'Tancada' => 5,
        ];

        $estatCounts = Incidencia::select('estat', DB::raw('COUNT(*) as total'))
            ->groupBy('estat')
            ->pluck('total', 'estat');

        $weightedSum = 0;
        $totalIncidencias = 0;
        foreach ($estatCounts as $estat => $count) {
            $totalIncidencias += (int) $count;
            $weightedSum += ((int) ($weights[$estat] ?? 0)) * ((int) $count);
        }

        $satisfaccionGlobal = $totalIncidencias > 0
            ? round($weightedSum / $totalIncidencias, 1)
            : null;

        // Incidencias por sede (para BCN/BER/MTL)
        $sedes = Sede::whereIn('nom', ['Barcelona', 'Berlin', 'Montreal'])->get()->keyBy('nom');

        $incidenciasPorSede = Incidencia::select('sede_id', DB::raw('COUNT(*) as total'))
            ->groupBy('sede_id')
            ->pluck('total', 'sede_id');

        $sedeCounts = [
            'BCN' => (int) ($incidenciasPorSede[$sedes['Barcelona']->id ?? -1] ?? 0),
            'BER' => (int) ($incidenciasPorSede[$sedes['Berlin']->id ?? -1] ?? 0),
            'MTL' => (int) ($incidenciasPorSede[$sedes['Montreal']->id ?? -1] ?? 0),
        ];

        $maxCount = max($sedeCounts['BCN'], $sedeCounts['BER'], $sedeCounts['MTL']);
        $minHeight = 40;
        $maxHeight = 180;

        $barHeights = [];
        foreach ($sedeCounts as $code => $count) {
            if ($maxCount <= 0) {
                $barHeights[$code] = (int) (($minHeight + $maxHeight) / 2);
                continue;
            }
            $barHeights[$code] = (int) round($minHeight + (($count / $maxCount) * ($maxHeight - $minHeight)));
        }

        // Tipología (top 3 categorías)
        $categoriaRows = DB::table('incidencias')
            ->join('categorias', 'incidencias.categoria_id', '=', 'categorias.id')
            ->select('categorias.nom', DB::raw('COUNT(*) as total'))
            ->groupBy('categorias.nom')
            ->orderByDesc('total')
            ->limit(3)
            ->get();

        $categoriaTotal = Incidencia::count();
        $tipologias = [];
        foreach ($categoriaRows as $row) {
            $tipologias[] = [
                'nom' => $row->nom,
                'percent' => $categoriaTotal > 0 ? (int) round(((int) $row->total / $categoriaTotal) * 100) : 0,
            ];
        }
        while (count($tipologias) < 3) {
            $tipologias[] = ['nom' => '-', 'percent' => 0];
        }

        // Si hay redondeos, ajustar el último para que sume 100 (solo si hay incidencias)
        if ($categoriaTotal > 0) {
            $sum = $tipologias[0]['percent'] + $tipologias[1]['percent'] + $tipologias[2]['percent'];
            $tipologias[2]['percent'] += (100 - $sum);
        } else {
            $tipologias = [
                ['nom' => 'Hardware', 'percent' => 33],
                ['nom' => 'Software', 'percent' => 33],
                ['nom' => 'Redes', 'percent' => 34],
            ];
        }

        // Tabla inferior: pendientes de asignación
        $pendientesAsignacion = Incidencia::with('sede')
            ->where('estat', 'Sense assignar')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('admin.admin_dashboard_principal', compact(
            'totalUsuarios',
            'incidenciasActivas',
            'tiempoMedioResolucionHoras',
            'satisfaccionGlobal',
            'sedeCounts',
            'barHeights',
            'tipologias',
            'pendientesAsignacion'
        ));
    }
}
