<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les meves incidències - Nexton</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

    <!-- Contenido Principal -->
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
            <div>
                <h1 class="page-title">Les meves incidències</h1>
                <p class="page-subtitle">Gestiona i consulta l'estat de les teves incidències</p>
            </div>
            <a href="{{ route('client.crear') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nova incidència
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
                        <label><i class="fas fa-tasks"></i> Estat</label>
                        <select name="estat" class="filter-select">
                            <option value="">Tots els estats</option>
                            @foreach($estats as $value => $label)
                                <option value="{{ $value }}" {{ $estatFilter == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Ordenar por fecha -->
                    <div class="filter-group">
                        <label><i class="fas fa-sort"></i> Ordenar per data</label>
                        <select name="orden" class="filter-select">
                            <option value="desc" {{ $ordenFilter == 'desc' ? 'selected' : '' }}>Més recents primer</option>
                            <option value="asc" {{ $ordenFilter == 'asc' ? 'selected' : '' }}>Més antigues primer</option>
                        </select>
                    </div>

                    <!-- Ocultar resoltes -->
                    <div class="filter-group">
                        <label><i class="fas fa-eye-slash"></i> Ocultar resoltes/tancades</label>
                        <select name="ocultar_resoltes" class="filter-select">
                            <option value="0" {{ !$ocultarResoltes ? 'selected' : '' }}>Mostrar totes</option>
                            <option value="1" {{ $ocultarResoltes ? 'selected' : '' }}>Ocultar resoltes/tancades</option>
                        </select>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="filter-actions">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="{{ route('client.index') }}" class="btn-clear">
                            <i class="fas fa-times"></i> Neteja
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $incidencies->where('estat', 'Sense assignar')->count() }}</div>
                <div class="stat-label">Sense assignar</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $incidencies->whereIn('estat', ['Assignada', 'En treball'])->count() }}</div>
                <div class="stat-label">En procés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $incidencies->where('estat', 'Resolta')->count() }}</div>
                <div class="stat-label">Resoltes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $incidencies->where('estat', 'Tancada')->count() }}</div>
                <div class="stat-label">Tancades</div>
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
                                        <span>Tècnic: {{ $incidencia->tecnico->name }}</span>
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
                                        <i class="fas fa-minus-circle"></i> Mitjana
                                    </span>
                                @else
                                    <span class="priority-badge priority-baixa">
                                        <i class="fas fa-check-circle"></i> Baixa
                                    </span>
                                @endif
                            @endif

                            @if($incidencia->estat === 'Sense assignar')
                                <span class="badge badge-inactive">Sense assignar</span>
                            @elseif($incidencia->estat === 'Assignada')
                                <span class="status-badge status-assignada">Assignada</span>
                            @elseif($incidencia->estat === 'En treball')
                                <span class="status-badge status-treball">En treball</span>
                            @elseif($incidencia->estat === 'Resolta')
                                <span class="status-badge status-resolta">Resolta</span>
                            @else
                                <span class="badge badge-active">Tancada</span>
                            @endif
                        </div>
                    </div>

                    <div class="incidencia-description">
                        {{ $incidencia->descripcio }}
                    </div>

                    <div class="incidencia-actions">
                        @if($incidencia->estat === 'Resolta')
                            <form method="POST" action="{{ route('client.tancar', $incidencia->id) }}">
                                @csrf
                                <button type="submit" class="btn-resolve" onclick="return confirm('Confirmes que vols tancar aquesta incidència?')">
                                    <i class="fas fa-check-double"></i>
                                    Tancar incidència
                                </button>
                            </form>
                        @elseif($incidencia->estat === 'Tancada')
                            <span style="color: var(--texto-secundario); font-size: 0.9rem;">
                                <i class="fas fa-check-circle"></i> Incidència tancada
                            </span>
                        @else
                            <span style="color: var(--texto-secundario); font-size: 0.9rem;">
                                <i class="fas fa-info-circle"></i> 
                                @if($incidencia->estat === 'Sense assignar')
                                    Pendent d'assignar a un tècnic
                                @elseif($incidencia->estat === 'Assignada')
                                    Assignada a un tècnic, pendent d'iniciar
                                @else
                                    El tècnic està treballant en aquesta incidència
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No tens incidències amb els filtres seleccionats</p>
                <a href="{{ route('client.crear') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-plus"></i> Crear la primera incidència
                </a>
            </div>
        @endif
    </div>
</body>
</html>
