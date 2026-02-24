@extends('layouts.client')

@section('title', 'Dashboard - Nexton')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-header">
    <h1>Panel de Control</h1>
    <p style="color: var(--text-secondary); margin-top: 0.5rem;">Resumen de tus incidencias y estado actual</p>
</div>

<!-- Panel Superior: KPIs -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-info">
            <h3>Total Incidencias</h3>
            <div class="kpi-value">{{ $totalIncidencies }}</div>
        </div>
        <div class="kpi-icon kpi-purple">
            <i class="fa-solid fa-ticket"></i>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h3>Sin Asignar</h3>
            <div class="kpi-value">{{ $senseAssignar }}</div>
        </div>
        <div class="kpi-icon kpi-orange">
            <i class="fa-solid fa-clock"></i>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h3>En Proceso</h3>
            <div class="kpi-value">{{ $enProces }}</div>
        </div>
        <div class="kpi-icon kpi-blue">
            <i class="fa-solid fa-spinner"></i>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h3>Resueltas</h3>
            <div class="kpi-value">{{ $resoltes + $tancades }}</div>
        </div>
        <div class="kpi-icon kpi-green">
            <i class="fa-solid fa-check-circle"></i>
        </div>
    </div>
</div>

<!-- Acciones Rápidas -->
<div style="margin: 2rem 0;">
    <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: var(--text-primary);">Acciones Rápidas</h2>
    <div class="cards-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
        <a href="{{ route('client.crear') }}" class="card" style="text-decoration: none;">
            <div class="card-icon" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                <i class="fas fa-plus"></i>
            </div>
            <h3 class="card-title">Nueva Incidencia</h3>
            <p class="card-description">Crear una nueva incidencia para reportar un problema</p>
        </a>
        
        <a href="{{ route('client.index') }}" class="card" style="text-decoration: none;">
            <div class="card-icon" style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                <i class="fas fa-list"></i>
            </div>
            <h3 class="card-title">Ver Todas</h3>
            <p class="card-description">Ver todas tus incidencias con filtros avanzados</p>
        </a>

        <a href="{{ route('client.index', ['estat' => 'En treball']) }}" class="card" style="text-decoration: none;">
            <div class="card-icon" style="background-color: rgba(16, 185, 129, 0.1); color: #10b981;">
                <i class="fas fa-tools"></i>
            </div>
            <h3 class="card-title">En Trabajo</h3>
            <p class="card-description">Ver incidencias que están siendo trabajadas</p>
        </a>
    </div>
</div>

<!-- Últimas Incidencias -->
<div class="table-panel" style="background-color: var(--card-bg); padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
    <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: var(--text-primary);">
        Últimas Incidencias
    </h2>
    
    @if($ultimesIncidencies->count() > 0)
        <table class="data-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 1rem; color: var(--text-secondary); font-size: 0.875rem;">Título</th>
                    <th style="text-align: left; padding: 1rem; color: var(--text-secondary); font-size: 0.875rem;">Categoría</th>
                    <th style="text-align: left; padding: 1rem; color: var(--text-secondary); font-size: 0.875rem;">Estado</th>
                    <th style="text-align: left; padding: 1rem; color: var(--text-secondary); font-size: 0.875rem;">Fecha</th>
                    <th style="text-align: left; padding: 1rem; color: var(--text-secondary); font-size: 0.875rem;">Técnico</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ultimesIncidencies as $incidencia)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 1rem; color: var(--text-primary);">{{ $incidencia->titol }}</td>
                        <td style="padding: 1rem; color: var(--text-secondary); font-size: 0.9rem;">
                            {{ $incidencia->categoria->nom }}
                        </td>
                        <td style="padding: 1rem;">
                            @if($incidencia->estat === 'Sense assignar')
                                <span class="badge badge-inactive">Sin asignar</span>
                            @elseif($incidencia->estat === 'Assignada')
                                <span class="status-badge status-assignada">Asignada</span>
                            @elseif($incidencia->estat === 'En treball')
                                <span class="status-badge status-treball">En trabajo</span>
                            @elseif($incidencia->estat === 'Resolta')
                                <span class="status-badge status-resolta">Resuelta</span>
                            @else
                                <span class="badge badge-active">Cerrada</span>
                            @endif
                        </td>
                        <td style="padding: 1rem; color: var(--text-secondary); font-size: 0.9rem;">
                            {{ $incidencia->created_at->format('d/m/Y') }}
                        </td>
                        <td style="padding: 1rem; color: var(--text-secondary); font-size: 0.9rem;">
                            {{ $incidencia->tecnico ? $incidencia->tecnico->name : '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No tienes incidencias todavía</p>
            <a href="{{ route('client.crear') }}" class="btn btn-primary" style="margin-top: 1rem;">
                <i class="fas fa-plus"></i> Crear primera incidencia
            </a>
        </div>
    @endif
</div>
@endsection
