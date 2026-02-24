<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les meves incidències - Nexton</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/client_incidencias.css') }}">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo-header">
            <i class="fas fa-cube logo-icon"></i>
            <span class="logo-text">Nexton</span>
        </div>
        <div class="user-info">
            <span class="user-name">
                <i class="fas fa-user"></i> {{ auth()->user()->name }}
            </span>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Tancar Sessió
                </button>
            </form>
        </div>
    </header>

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
        <form method="GET" action="{{ route('client.index') }}">
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
            <div class="stat-number">{{ $incidencies->where('estat', 'Sense assignar')->count() }}</div>
            <div class="stat-label">Sin asignar</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $incidencies->whereIn('estat', ['Assignada', 'En treball'])->count() }}</div>
            <div class="stat-label">En proceso</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $incidencies->where('estat', 'Resolta')->count() }}</div>
            <div class="stat-label">Resueltas</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $incidencies->where('estat', 'Tancada')->count() }}</div>
            <div class="stat-label">Cerradas</div>
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
@endsection

@push('scripts')
    <script src="{{ asset('js/client-actions.js') }}"></script>
@endpush