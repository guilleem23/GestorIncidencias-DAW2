@extends('layouts.tecnic')

@section('title', 'Dashboard - Nexton')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-header">
    <h1>Panel de Control</h1>
    <p style="color: var(--text-secondary); margin-top: 0.5rem;">Resumen de tus tareas y rendimiento</p>
</div>

<!-- Panel Superior: KPIs -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-info">
            <h3>Total Asignadas</h3>
            <div class="kpi-value">{{ $totalAssignades }}</div>
        </div>
        <div class="kpi-icon kpi-purple">
            <i class="fa-solid fa-clipboard-list"></i>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h3>Pendientes Iniciar</h3>
            <div class="kpi-value">{{ $pendentsIniciar }}</div>
        </div>
        <div class="kpi-icon kpi-orange">
            <i class="fa-solid fa-hourglass-start"></i>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h3>En Trabajo</h3>
            <div class="kpi-value">{{ $enTreball }}</div>
        </div>
        <div class="kpi-icon kpi-blue">
            <i class="fa-solid fa-cog"></i>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h3>Resueltas</h3>
            <div class="kpi-value">{{ $resoltes }}</div>
        </div>
        <div class="kpi-icon kpi-green">
            <i class="fa-solid fa-check-double"></i>
        </div>
    </div>
</div>

<!-- Acciones Rápidas -->
<div style="margin: 2rem 0;">
    <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: var(--text-primary);">Acciones Rápidas</h2>
    <div class="cards-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
        <a href="{{ route('tecnic.index') }}" class="card" style="text-decoration: none;">
            <div class="card-icon" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                <i class="fas fa-tasks"></i>
            </div>
            <h3 class="card-title">Ver Todas las Tareas</h3>
            <p class="card-description">Ver todas las incidencias asignadas a ti</p>
        </a>
        
        <a href="{{ route('tecnic.index') }}#en-trabajo" class="card" style="text-decoration: none;">
            <div class="card-icon" style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                <i class="fas fa-wrench"></i>
            </div>
            <h3 class="card-title">En Trabajo</h3>
            <p class="card-description">Continuar con incidencias en progreso</p>
        </a>

        <a href="{{ route('tecnic.index') }}#pendientes" class="card" style="text-decoration: none;">
            <div class="card-icon" style="background-color: rgba(249, 115, 22, 0.1); color: #f97316;">
                <i class="fas fa-play"></i>
            </div>
            <h3 class="card-title">Iniciar Nueva</h3>
            <p class="card-description">Comenzar a trabajar en una nueva incidencia</p>
        </a>
    </div>
</div>

<!-- Últimas Tareas -->
<div class="table-panel" style="background-color: var(--card-bg); padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
    <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: var(--text-primary);">
        Últimas Tareas Asignadas
    </h2>
    
    @if($ultimesTasques->count() > 0)
        <table class="data-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 1rem; color: var(--text-secondary); font-size: 0.875rem;">Título</th>
                    <th style="text-align: left; padding: 1rem; color: var(--text-secondary); font-size: 0.875rem;">Cliente</th>
                    <th style="text-align: left; padding: 1rem; color: var(--text-secondary); font-size: 0.875rem;">Estado</th>
                    <th style="text-align: left; padding: 1rem; color: var(--text-secondary); font-size: 0.875rem;">Prioridad</th>
                    <th style="text-align: left; padding: 1rem; color: var(--text-secondary); font-size: 0.875rem;">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ultimesTasques as $incidencia)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 1rem; color: var(--text-primary);">{{ $incidencia->titol }}</td>
                        <td style="padding: 1rem; color: var(--text-secondary); font-size: 0.9rem;">
                            {{ $incidencia->cliente->name }}
                        </td>
                        <td style="padding: 1rem;">
                            @if($incidencia->estat === 'Assignada')
                                <span class="status-badge status-assignada">Asignada</span>
                            @elseif($incidencia->estat === 'En treball')
                                <span class="status-badge status-treball">En trabajo</span>
                            @else
                                <span class="status-badge status-resolta">Resuelta</span>
                            @endif
                        </td>
                        <td style="padding: 1rem;">
                            @if($incidencia->prioritat === 'alta')
                                <span class="priority-badge priority-alta">
                                    <i class="fas fa-exclamation-circle"></i> Alta
                                </span>
                            @elseif($incidencia->prioritat === 'mitjana')
                                <span class="priority-badge priority-mitjana">
                                    <i class="fas fa-minus-circle"></i> Media
                                </span>
                            @else
                                <span class="priority-badge priority-baixa">
                                    <i class="fas fa-check-circle"></i> Baja
                                </span>
                            @endif
                        </td>
                        <td style="padding: 1rem; color: var(--text-secondary); font-size: 0.9rem;">
                            {{ $incidencia->created_at->format('d/m/Y') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <i class="fas fa-clipboard-check"></i>
            <p>No tienes tareas asignadas en este momento</p>
        </div>
    @endif
</div>
@endsection
