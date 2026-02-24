@extends('layouts.admin')

@section('title', 'Nexton Admin - Dashboard Global')

@push('styles')
    @vite(['resources/css/admin_dashboard.css'])
@endpush

@section('content')
<div class="dashboard-header">
    <h1>Visión Global de Operaciones</h1>
    <p style="color: var(--text-secondary); margin-top: 0.5rem;">Estado del sistema en tiempo real y alertas críticas.</p>
</div>

<!-- Panel Superior: KPIs -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-info">
            <h3>Total Usuarios</h3>
            <div class="kpi-value">{{ number_format($totalUsuarios ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="kpi-icon kpi-purple">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h3>Incidencias Activas</h3>
            <div class="kpi-value">{{ number_format($incidenciasActivas ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="kpi-icon kpi-orange">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h3>Tiempo Medio Res.</h3>
            <div class="kpi-value">
                {{ $tiempoMedioResolucionHoras !== null ? ($tiempoMedioResolucionHoras . 'h') : '—' }}
            </div>
        </div>
        <div class="kpi-icon kpi-blue">
            <i class="fa-regular fa-clock"></i>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h3>Satisfacción Global</h3>
            <div class="kpi-value">{{ $satisfaccionGlobal !== null ? ($satisfaccionGlobal . '/5') : '—' }}</div>
        </div>
        <div class="kpi-icon kpi-green">
            <i class="fa-solid fa-star"></i>
        </div>
    </div>
</div>

<!-- Sección Central: Gráficos -->
<div class="charts-grid">
    <div class="chart-card">
        <h3 class="card-title">Incidencias por Sede</h3>
        <div class="bar-chart">
            <div class="bar-group">
                <div class="bar bar-bcn" style="height: {{ $barHeights['BCN'] ?? 100 }}px;"></div>
                <span class="bar-label">BCN ({{ $sedeCounts['BCN'] ?? 0 }})</span>
            </div>
            <div class="bar-group">
                <div class="bar bar-berlin" style="height: {{ $barHeights['BER'] ?? 100 }}px;"></div>
                <span class="bar-label">BER ({{ $sedeCounts['BER'] ?? 0 }})</span>
            </div>
            <div class="bar-group">
                <div class="bar bar-montreal" style="height: {{ $barHeights['MTL'] ?? 100 }}px;"></div>
                <span class="bar-label">MTL ({{ $sedeCounts['MTL'] ?? 0 }})</span>
            </div>
        </div>
    </div>
    <div class="chart-card">
        <h3 class="card-title">Tipología de Problemas</h3>
        <div class="donut-chart-container">
            @php
                $p1 = (int) (($tipologias[0]['percent'] ?? 33));
                $p2 = (int) (($tipologias[1]['percent'] ?? 33));
                $p3 = 100 - $p1 - $p2;
                $a1 = (int) round($p1 * 3.6);
                $a2 = (int) round($p2 * 3.6);
                $a12 = $a1 + $a2;
            @endphp
            <div class="donut" style="background: conic-gradient(var(--neon-orange) 0deg {{ $a1 }}deg, var(--neon-blue) {{ $a1 }}deg {{ $a12 }}deg, var(--neon-purple) {{ $a12 }}deg 360deg);"></div>
            <div class="donut-legend">
                <ul>
                    <li><span class="dot dot-orange"></span> {{ $tipologias[0]['nom'] ?? 'Hardware' }} ({{ $p1 }}%)</li>
                    <li><span class="dot dot-blue"></span> {{ $tipologias[1]['nom'] ?? 'Software' }} ({{ $p2 }}%)</li>
                    <li><span class="dot dot-purple"></span> {{ $tipologias[2]['nom'] ?? 'Redes' }} ({{ $p3 }}%)</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Sección Inferior: Tabla de Gestión -->
<div class="table-panel">
    <h3 class="card-title">Incidencias Pendientes de Asignación (Global)</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Sede</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @forelse (($pendientesAsignacion ?? collect()) as $incidencia)
                <tr>
                    <td>#INC-{{ $incidencia->id }}</td>
                    <td>{{ $incidencia->titol }}</td>
                    <td>{{ $incidencia->sede?->nom ?? '-' }}</td>
                    <td>{{ $incidencia->created_at?->locale('es')->diffForHumans() ?? '-' }}</td>
                    <td><span class="status-badge">{{ $incidencia->estat }}</span></td>
                    <td>
                        <a class="btn-action" href="{{ route('admin.incidencias') }}">Asignar Técnico</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="color: var(--text-secondary); padding: 1rem;">
                        No hay incidencias pendientes de asignación.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
