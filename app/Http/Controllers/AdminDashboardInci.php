<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\Sede;
use Illuminate\Http\Request;

class AdminDashboardInci extends Controller
{
    /**
     * Muestra la lista global de incidencias para el panel de admin.
     */
    public function resum(Request $request)
    {
        $hasSedeSelected = $request->filled('sede_id');

        $totalResueltas = 0;
        $totalPendientes = 0;
        $desgloseTecnicoCategoria = collect();
        $categoriasDesglose = collect();

        if ($hasSedeSelected) {
            $incidencias = Incidencia::with(['tecnico', 'categoria'])
                ->where('sede_id', $request->sede_id)
                ->get();

            $totalResueltas = $incidencias
                ->whereIn('estat', ['Resolta', 'Tancada'])
                ->count();

            $totalPendientes = $incidencias
                ->whereNotIn('estat', ['Resolta', 'Tancada'])
                ->count();

            $desgloseTecnicoCategoria = $incidencias
                ->whereNotNull('tecnic_id')
                ->groupBy(fn ($incidencia) => $incidencia->tecnico?->name ?? 'Sin tecnico')
                ->map(function ($incidenciasTecnico) {
                    return $incidenciasTecnico
                        ->groupBy(fn ($incidencia) => $incidencia->categoria?->nom ?? 'Sin categoria')
                        ->map(fn ($incidenciasCategoria) => $incidenciasCategoria->count())
                        ->sortKeys();
                })
                ->sortKeys();

            $categoriasDesglose = $desgloseTecnicoCategoria
                ->flatMap(fn ($categorias) => $categorias->keys())
                ->unique()
                ->values();
        }

        if ($request->ajax()) {
            return view('admin.dashboard_incidencias.partials.resumen', compact(
                'hasSedeSelected',
                'totalResueltas',
                'totalPendientes',
                'desgloseTecnicoCategoria',
                'categoriasDesglose'
            ));
        }

        $sedes = Sede::orderBy('nom')->get();

        return view('admin.dashboard_incidencias.index', compact(
            'sedes',
            'hasSedeSelected',
            'totalResueltas',
            'totalPendientes',
            'desgloseTecnicoCategoria',
            'categoriasDesglose'
        ));
    }
}
