@extends('layouts.tecnic')

@section('title', 'Mis Tareas - Nexton')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Mis Tareas</h1>
            <p class="page-subtitle">Incidencias asignadas para resolver</p>
        </div>
    </div>

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
            <div class="stat-number">{{ $incidenciesTancades }}</div>
            <div class="stat-label">Completadas</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $incidencies->count() }}</div>
            <div class="stat-label">Total</div>
        </div>
    </div>

    <!-- Lista de Incidencias -->
    <div>
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
                                <span>{{ $incidencia->categoria?->nom ?? 'Sin categoría' }} / {{ $incidencia->subcategoria?->nom ?? 'Sin subcategoría' }}</span>
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
                        @elseif($incidencia->estat === 'Resolta')
                            <span class="status-badge status-resolta">Resuelta</span>
                        @else
                            <span class="badge badge-active">Cerrada</span>
                        @endif
                    </div>
                </div>

                <div class="incidencia-description">
                    {{ $incidencia->descripcio }}
                </div>

                <div class="incidencia-description" style="margin-top: 1rem;">
                    <div style="display:flex; align-items:center; justify-content:space-between; gap: 1rem;">
                        <strong style="font-size: 0.95rem;">Comentarios</strong>
                        @if($incidencia->comentarios && $incidencia->comentarios->count())
                            <span style="color: var(--texto-secundario); font-size: 0.85rem;">
                                {{ $incidencia->comentarios->count() }}
                            </span>
                        @endif
                    </div>

                    @if($incidencia->comentarios && $incidencia->comentarios->count())
                        <div style="margin-top: 0.75rem; display:flex; flex-direction:column; gap:0.75rem;">
                            @foreach($incidencia->comentarios as $comentario)
                                @php $isMine = (int)($comentario->usuario_id ?? 0) === (int)auth()->id(); @endphp
                                <div style="display:flex;">
                                    <div style="max-width: 92%; margin-left: {{ $isMine ? 'auto' : '0' }}; border: 1px solid var(--borde, rgba(148, 163, 184, 0.2)); border-radius: 0.75rem; padding: 0.75rem 0.9rem; background: {{ $isMine ? 'rgba(15, 23, 42, 0.40)' : 'rgba(15, 23, 42, 0.25)' }};">
                                    <div style="display:flex; justify-content:space-between; gap: 1rem; align-items: baseline;">
                                        <span style="font-weight: 600; font-size: 0.9rem;">{{ $comentario->usuario?->name ?? 'Usuario' }}</span>
                                        <span style="color: var(--texto-secundario); font-size: 0.8rem; white-space: nowrap;">{{ $comentario->created_at?->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div style="margin-top: 0.4rem; color: var(--texto-secundario);">
                                        {!! nl2br(e($comentario->missatge)) !!}
                                    </div>
                                    @if(!empty($comentario->imatge_path))
                                        <div class="comment-attachment">
                                            <a href="{{ asset('storage/' . $comentario->imatge_path) }}" target="_blank" rel="noopener">
                                                <img class="comment-image" src="{{ asset('storage/' . $comentario->imatge_path) }}" alt="Imagen adjunta">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="margin-top: 0.5rem; color: var(--texto-secundario); font-size: 0.9rem;">
                            <i class="fas fa-comment-dots"></i> Sin comentarios todavía
                        </div>
                    @endif

                    <form method="POST" action="{{ route('tecnic.incidencias.comentarios.store', $incidencia->id) }}" enctype="multipart/form-data" style="margin-top: 0.9rem;">
                        @csrf
                        <div style="display:flex; flex-direction:column; gap:0.5rem;">
                            <textarea name="missatge" rows="3" class="comment-textarea" placeholder="Responder en la incidencia..."></textarea>
                            @error('missatge')
                                <span class="error-message" style="margin-top: 0;">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </span>
                            @enderror

                            <div class="comment-upload-row">
                                <label class="comment-file-label" for="imatge-tecnic-{{ $incidencia->id }}">
                                    <i class="fas fa-image"></i> Adjuntar imagen
                                </label>
                                <input id="imatge-tecnic-{{ $incidencia->id }}" type="file" name="imatge" class="comment-file-input" accept="image/*">
                            </div>
                            @error('imatge')
                                <span class="error-message" style="margin-top: 0;">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </span>
                            @enderror

                            <div style="display:flex; justify-content:flex-end;">
                                <button type="submit" class="btn btn-primary" style="padding: 0.55rem 1rem;">
                                    <i class="fas fa-paper-plane"></i> Enviar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="incidencia-actions">
                    <a href="{{ route('tecnic.incidencias.show', $incidencia->id) }}" class="btn-view-details">
                        <i class="fas fa-eye"></i>
                        Ver detalles
                    </a>
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
                    @elseif($incidencia->estat === 'Resolta')
                        <span style="color: var(--texto-secundario); font-size: 0.9rem;">
                            <i class="fas fa-info-circle"></i> Esperando que el cliente cierre la incidencia
                        </span>
                    @else
                        <span style="color: var(--success-color); font-size: 0.9rem;">
                            <i class="fas fa-check-double"></i> Incidencia completada y cerrada
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <i class="fas fa-clipboard-check"></i>
            <p>No tienes incidencias activas en este momento</p>
        </div>
    @endif
    </div>
@endsection