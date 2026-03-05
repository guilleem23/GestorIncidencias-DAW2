@extends('layouts.admin')

@section('title', 'Nexton Admin - Dashboard Global')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-header">
    <div class="header-text">
        <h1><i class="fa-solid fa-chart-line"></i> Panel de Control</h1>
        <p>Estado del sistema en tiempo real</p>
    </div>
    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
        <a href="{{ route('admin.resum') }}" class="btn-assign">
            <i class="fa-solid fa-chart-bar"></i> Resum
        </a>
        <div class="header-date">
            <i class="fa-regular fa-calendar"></i> {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
        </div>
    </div>
</div>

<!-- KPIs -->
<div class="kpi-grid">
    <div class="kpi-card kpi-card--users">
        <div class="kpi-icon-wrap">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="kpi-body">
            <span class="kpi-label">Total Usuarios</span>
            <span class="kpi-value">{{ number_format($totalUsuarios, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="kpi-card kpi-card--active">
        <div class="kpi-icon-wrap">
            <i class="fa-solid fa-fire"></i>
        </div>
        <div class="kpi-body">
            <span class="kpi-label">Incidencias Activas</span>
            <span class="kpi-value">{{ number_format($incidenciasActivas, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="kpi-card kpi-card--time">
        <div class="kpi-icon-wrap">
            <i class="fa-solid fa-stopwatch"></i>
        </div>
        <div class="kpi-body">
            <span class="kpi-label">Tiempo Medio Resolución</span>
            <span class="kpi-value">{{ $tiempoMedioResolucion ?? '—' }}</span>
        </div>
    </div>

    <div class="kpi-card kpi-card--pending">
        <div class="kpi-icon-wrap">
            <i class="fa-solid fa-hourglass-half"></i>
        </div>
        <div class="kpi-body">
            <span class="kpi-label">Pendientes de Asignación</span>
            <span class="kpi-value">{{ number_format($pendientesCount, 0, ',', '.') }}</span>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="charts-grid">
    <!-- Incidencias por Sede -->
    <div class="chart-card">
        <h3 class="card-title"><i class="fa-solid fa-building"></i> Incidencias por Sede</h3>
        <div class="bar-chart">
            @foreach ($sedeStats as $i => $sede)
                @php
                    $colors = ['#3b82f6', '#8b5cf6', '#10b981', '#f97316', '#ef4444', '#ec4899', '#06b6d4', '#eab308'];
                    $color = $colors[$i % count($colors)];
                @endphp
                <div class="bar-group">
                    <div class="bar-value">{{ $sede['count'] }}</div>
                    <div class="bar" style="height: {{ $sede['height'] }}px; background: linear-gradient(to top, {{ $color }}, {{ $color }}aa);"></div>
                    <span class="bar-label">{{ $sede['nom'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Tipología de Problemas -->
    <div class="chart-card">
        <h3 class="card-title"><i class="fa-solid fa-tags"></i> Tipología de Problemas</h3>
        @if(count($tipologias) > 0)
            @php
                $tipColors = ['#3b82f6', '#8b5cf6', '#10b981', '#f97316', '#ef4444', '#ec4899', '#06b6d4', '#eab308'];
                // Build conic-gradient stops
                $gradientParts = [];
                $currentDeg = 0;
                foreach ($tipologias as $i => $tipo) {
                    $color = $tipColors[$i % count($tipColors)];
                    $slice = round(($tipo['percent'] / 100) * 360, 1);
                    $endDeg = $currentDeg + $slice;
                    $gradientParts[] = "{$color} {$currentDeg}deg {$endDeg}deg";
                    $currentDeg = $endDeg;
                }
                $gradient = implode(', ', $gradientParts);
            @endphp
            <div class="donut-chart-container">
                <div class="donut" style="background: conic-gradient({{ $gradient }});"></div>
                <div class="donut-legend">
                    <ul>
                        @foreach ($tipologias as $i => $tipo)
                            @php $tColor = $tipColors[$i % count($tipColors)]; @endphp
                            <li>
                                <span class="dot" style="background: {{ $tColor }};"></span>
                                <span class="legend-name">{{ $tipo['nom'] }}</span>
                                <span class="legend-stats">{{ $tipo['count'] }} <small>({{ $tipo['percent'] }}%)</small></span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @else
            <div class="empty-state">
                <i class="fa-solid fa-chart-pie"></i>
                <p>No hay datos de categorías</p>
            </div>
        @endif
    </div>
</div>

<!-- Tabla: Pendientes de Asignación -->
<div class="table-panel">
    <h3 class="card-title"><i class="fa-solid fa-clipboard-list"></i> Incidencias Pendientes de Asignación (Global)</h3>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Sede</th>
                    <th>Categoría</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pendientesAsignacion as $incidencia)
                    <tr>
                        <td><span class="id-badge">#{{ $incidencia->id }}</span></td>
                        <td>{{ Str::limit($incidencia->titol, 40) }}</td>
                        <td>{{ $incidencia->sede?->nom ?? '—' }}</td>
                        <td>{{ $incidencia->categoria?->nom ?? 'Sin categoría' }}</td>
                        <td>
                            <span class="date-text" title="{{ $incidencia->created_at }}">
                                {{ $incidencia->created_at?->locale('es')->diffForHumans() ?? '—' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-row">
                            <i class="fa-solid fa-check-circle"></i> Todas las incidencias están asignadas
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
