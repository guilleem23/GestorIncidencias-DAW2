@extends('layouts.client')

@section('title', 'Mis Incidencias - Nexton')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Mis Incidencias</h1>
            <p class="page-subtitle">Gestiona y consulta el estado de tus incidencias</p>
        </div>
        <a href="{{ route('client.crear') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Incidencia
        </a>
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

    <!-- Filtros -->
    <div class="filters-container">
        <form method="GET" action="{{ route('client.index') }}" id="filtros-form">
            <div class="filters-grid">
                <!-- Filtro por Estado -->
                <div class="filter-group">
                    <label><i class="fas fa-tasks"></i> Estado</label>
                    <select name="estat" class="filter-select">
                        <option value="">Todos los estados</option>
                        @foreach($estats as $value => $label)
                            <option value="{{ $value }}" {{ $estatFilter == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Ordenar por fecha -->
                <div class="filter-group">
                    <label><i class="fas fa-sort"></i> Ordenar por fecha</label>
                    <select name="orden" class="filter-select">
                        <option value="desc" {{ $ordenFilter == 'desc' ? 'selected' : '' }}>Más recientes primero</option>
                        <option value="asc" {{ $ordenFilter == 'asc' ? 'selected' : '' }}>Más antiguas primero</option>
                    </select>
                </div>

                <!-- Ocultar resueltas -->
                <div class="filter-group">
                    <label><i class="fas fa-eye-slash"></i> Ocultar resueltas/cerradas</label>
                    <select name="ocultar_resoltes" class="filter-select">
                        <option value="0" {{ !$ocultarResoltes ? 'selected' : '' }}>Mostrar todas</option>
                        <option value="1" {{ $ocultarResoltes ? 'selected' : '' }}>Ocultar resueltas/cerradas</option>
                    </select>
                </div>

                <!-- Botones de Acción -->
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('client.index') }}" class="btn-clear">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number" id="stat-senseassignar">{{ $incidencies->where('estat', 'Sense assignar')->count() }}</div>
            <div class="stat-label">Sin asignar</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="stat-enproces">{{ $incidencies->whereIn('estat', ['Assignada', 'En treball'])->count() }}</div>
            <div class="stat-label">En proceso</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="stat-resoltes">{{ $incidencies->where('estat', 'Resolta')->count() }}</div>
            <div class="stat-label">Resueltas</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="stat-tancades">{{ $incidencies->where('estat', 'Tancada')->count() }}</div>
            <div class="stat-label">Cerradas</div>
        </div>
    </div>

    <!-- Lista de Incidencias -->
    <div id="incidencias-container">
    @if($incidencies->count() > 0)
        @foreach($incidencies as $incidencia)
            <div class="incidencia-card">
                <div class="incidencia-header">
                    <div style="flex: 1;">
                        <h3 class="incidencia-title">{{ $incidencia->titol }}</h3>
                        <div class="incidencia-meta">
                            <div class="meta-item">
                                <i class="fas fa-tag"></i>
                                <span>{{ $incidencia->categoria->nom }} / {{ $incidencia->subcategoria->nom }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <span>{{ $incidencia->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @if($incidencia->tecnico)
                                <div class="meta-item">
                                    <i class="fas fa-user-cog"></i>
                                    <span>Técnico: {{ $incidencia->tecnico->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div style="display: flex; gap: 0.5rem; align-items: start; flex-wrap: wrap;">
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

                    <form method="POST" action="{{ route('client.incidencias.comentarios.store', $incidencia->id) }}" enctype="multipart/form-data" style="margin-top: 0.9rem;">
                        @csrf
                        <div style="display:flex; flex-direction:column; gap:0.5rem;">
                            <textarea name="missatge" rows="3" class="comment-textarea" placeholder="Añade un comentario para ayudar al técnico..."></textarea>
                            @error('missatge')
                                <span class="error-message" style="margin-top: 0;">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </span>
                            @enderror

                            <div class="comment-upload-row">
                                <label class="comment-file-label" for="imatge-{{ $incidencia->id }}">
                                    <i class="fas fa-image"></i> Adjuntar imagen
                                </label>
                                <input id="imatge-{{ $incidencia->id }}" type="file" name="imatge" class="comment-file-input" accept="image/*">
                            </div>
                            @error('imatge')
                                <span class="error-message" style="margin-top: 0;">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </span>
                            @enderror

                            <div style="display:flex; justify-content:flex-end;">
                                <button type="submit" class="btn btn-primary" style="padding: 0.55rem 1rem;">
                                    <i class="fas fa-paper-plane"></i> Enviar comentario
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="incidencia-actions">
                    @if($incidencia->estat === 'Resolta')
                        <form method="POST" action="{{ route('client.tancar', $incidencia->id) }}" class="form-close-incidencia">
                            @csrf
                            <button type="submit" class="btn-resolve">
                                <i class="fas fa-check-double"></i>
                                Cerrar incidencia
                            </button>
                        </form>
                    @elseif($incidencia->estat === 'Tancada')
                        <span style="color: var(--texto-secundario); font-size: 0.9rem;">
                            <i class="fas fa-check-circle"></i> Incidencia cerrada
                        </span>
                    @else
                        <span style="color: var(--texto-secundario); font-size: 0.9rem;">
                            <i class="fas fa-info-circle"></i> 
                            @if($incidencia->estat === 'Sense assignar')
                                Pendiente de asignar a un técnico
                            @elseif($incidencia->estat === 'Assignada')
                                Asignada a un técnico, pendiente de iniciar
                            @else
                                El técnico está trabajando en esta incidencia
                            @endif
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No tienes incidencias con los filtros seleccionados</p>
            <a href="{{ route('client.crear') }}" class="btn btn-primary" style="margin-top: 1rem;">
                <i class="fas fa-plus"></i> Crear la primera incidencia
            </a>
        </div>
    @endif
    </div>
    
    <!-- Loader AJAX -->
    <div id="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div style="background: var(--card-bg); padding: 2rem; border-radius: var(--radius-lg); text-align: center;">
            <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary-color);"></i>
            <p style="margin-top: 1rem; color: var(--text-primary);">Cargando...</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/client-actions.js') }}"></script>
@endpush