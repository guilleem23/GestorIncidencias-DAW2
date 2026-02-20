<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les meves tasques - Nexton</title>
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
                <i class="fas fa-wrench"></i> {{ auth()->user()->name }}
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
        <h1 class="page-title">Les meves tasques</h1>
        <p class="page-subtitle">Incidències assignades per resoldre</p>

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
                <div class="stat-label">Pendents d'iniciar</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $incidencies->where('estat', 'En treball')->count() }}</div>
                <div class="stat-label">En treball</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $incidencies->where('estat', 'Resolta')->count() }}</div>
                <div class="stat-label">Resoltes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $incidencies->count() }}</div>
                <div class="stat-label">Total assignades</div>
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
                                        <i class="fas fa-minus-circle"></i> Mitjana
                                    </span>
                                @else
                                    <span class="priority-badge priority-baixa">
                                        <i class="fas fa-check-circle"></i> Baixa
                                    </span>
                                @endif
                            @endif

                            @if($incidencia->estat === 'Assignada')
                                <span class="status-badge status-assignada">Assignada</span>
                            @elseif($incidencia->estat === 'En treball')
                                <span class="status-badge status-treball">En treball</span>
                            @else
                                <span class="status-badge status-resolta">Resolta</span>
                            @endif
                        </div>
                    </div>

                    <div class="incidencia-description">
                        {{ $incidencia->descripcio }}
                    </div>

                    <div class="incidencia-actions">
                        @if($incidencia->estat === 'Assignada')
                            <form method="POST" action="{{ route('tecnic.iniciar', $incidencia->id) }}">
                                @csrf
                                <button type="submit" class="btn-start">
                                    <i class="fas fa-play"></i>
                                    Iniciar treball
                                </button>
                            </form>
                        @elseif($incidencia->estat === 'En treball')
                            <form method="POST" action="{{ route('tecnic.resoldre', $incidencia->id) }}">
                                @csrf
                                <button type="submit" class="btn-resolve">
                                    <i class="fas fa-check"></i>
                                    Marcar com resolta
                                </button>
                            </form>
                        @else
                            <span style="color: var(--texto-secundario); font-size: 0.9rem;">
                                <i class="fas fa-info-circle"></i> Esperant que el client tanqui la incidència
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="fas fa-clipboard-check"></i>
                <p>No tens incidències assignades en aquest moment</p>
            </div>
        @endif
    </div>
</body>
</html>
