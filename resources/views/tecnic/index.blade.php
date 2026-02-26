@extends('layouts.tecnic')

@section('title', 'Mis Tareas - Nexton')

@section('content')
    <h1 class="page-title">Mis Tareas</h1>
    <p class="page-subtitle">Incidencias asignadas para resolver</p>

    @if(session('success'))
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Estadísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $incidencies->where('estat', 'Assignada')->count() }}</div>
            <div class="stat-label">Pendientes de iniciar</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $incidencies->where('estat', 'En treball')->count() }}</div>
            <div class="stat-label">En trabajo</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $incidencies->where('estat', 'Resolta')->count() }}</div>
            <div class="stat-label">Resueltas</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $incidencies->count() }}</div>
            <div class="stat-label">Total asignadas</div>
        </div>
    </div>

    <!-- Lista de Incidencias -->
    @if($incidencies->count() > 0)
        @foreach($incidencies as $incidencia)
            <div class="incidencia-card">
                <div class="incidencia-header">
                    <div style="flex: 1;">
                        <h3 class="incidencia-title">{{ $incidencia->titol }}</h3>
                        <div class="incidencia-meta">
                            <div class="meta-item">
                                <i class="fas fa-user"></i>
                                <span>{{ $incidencia->cliente->name }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-tag"></i>
                                <span>{{ $incidencia->categoria->nom }} / {{ $incidencia->subcategoria->nom }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <span>{{ $incidencia->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 0.5rem; align-items: start;">
                        @if($incidencia->prioritat)
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
                        @endif

                        @if($incidencia->estat === 'Assignada')
                            <span class="status-badge status-assignada">Asignada</span>
                        @elseif($incidencia->estat === 'En treball')
                            <span class="status-badge status-treball">En trabajo</span>
                        @else
                            <span class="status-badge status-resolta">Resuelta</span>
                        @endif
                    </div>
                </div>

                <div class="incidencia-description">
                    {{ $incidencia->descripcio }}
                </div>

                <div class="incidencia-description" style="margin-top: 1rem;">
                    <div style="display:flex; align-items:center; justify-content:space-between; gap: 1rem;">
                        <strong style="font-size: 0.95rem;">Comentarios del cliente</strong>
                        @if($incidencia->comentarios && $incidencia->comentarios->count())
                            <span style="color: var(--texto-secundario); font-size: 0.85rem;">
                                {{ $incidencia->comentarios->count() }}
                            </span>
                        @endif
                    </div>

                    @if($incidencia->comentarios && $incidencia->comentarios->count())
                        <div style="margin-top: 0.75rem; display:flex; flex-direction:column; gap:0.75rem;">
                            @foreach($incidencia->comentarios as $comentario)
                                <div style="border: 1px solid var(--borde, rgba(148, 163, 184, 0.2)); border-radius: 0.75rem; padding: 0.75rem 0.9rem; background: rgba(15, 23, 42, 0.25);">
                                    <div style="display:flex; justify-content:space-between; gap: 1rem; align-items: baseline;">
                                        <span style="font-weight: 600; font-size: 0.9rem;">{{ $comentario->usuario?->name ?? 'Usuario' }}</span>
                                        <span style="color: var(--texto-secundario); font-size: 0.8rem; white-space: nowrap;">{{ $comentario->created_at?->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div style="margin-top: 0.4rem; color: var(--texto-secundario);">
                                        {!! nl2br(e($comentario->missatge)) !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="margin-top: 0.5rem; color: var(--texto-secundario); font-size: 0.9rem;">
                            <i class="fas fa-comment-dots"></i> Sin comentarios todavía
                        </div>
                    @endif
                </div>

                <div class="incidencia-actions">
                    @if($incidencia->estat === 'Assignada')
                        <form method="POST" action="{{ route('tecnic.iniciar', $incidencia->id) }}">
                            @csrf
                            <button type="submit" class="btn-start">
                                <i class="fas fa-play"></i>
                                Iniciar trabajo
                            </button>
                        </form>
                    @elseif($incidencia->estat === 'En treball')
                        <form method="POST" action="{{ route('tecnic.resoldre', $incidencia->id) }}">
                            @csrf
                            <button type="submit" class="btn-resolve">
                                <i class="fas fa-check"></i>
                                Marcar como resuelta
                            </button>
                        </form>
                    @else
                        <span style="color: var(--texto-secundario); font-size: 0.9rem;">
                            <i class="fas fa-info-circle"></i> Esperando que el cliente cierre la incidencia
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <i class="fas fa-clipboard-check"></i>
            <p>No tienes incidencias asignadas en este momento</p>
        </div>
    @endif
@endsection