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

    @if (session('success'))
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filtros -->
    <div class="filters-container">
        <form method="POST" action="{{ route('client.index') }}" id="form-filters" class="filters-form">
            @csrf
            <div class="filters-grid">
                <!-- Filtro por Estado -->
                <div class="filter-group">
                    <label for="estat"><i class="fas fa-tasks"></i> Estado</label>
                    <select name="estat" id="estat" class="filter-select">
                        <option value="">Todos los estados</option>
                        @foreach ($estats as $key => $value)
                            <option value="{{ $key }}">
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="orden"><i class="fas fa-sort"></i> Ordenar</label>
                    <select name="orden" id="orden" class="filter-select">
                        <option value="desc">Más recientes primero</option>
                        <option value="asc">Más antiguas primero</option>
                    </select>
                </div>

                <div class="filter-group">
                    <div class="filter-actions">
                        <button type="button" id="btn-toggle-closed" class="btn-toggle-closed">
                            <i class="fa-solid fa-eye-slash"></i>
                            Ocultar resueltas/cerradas
                        </button>
                        <button type="button" id="btn-clear-filters" class="btn btn-outline">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid" id="stats-container">
        <div class="stat-card">
            <div class="stat-number" id="stat-senseassignar">0</div>
            <div class="stat-label">Sin asignar</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="stat-enproces">0</div>
            <div class="stat-label">En proceso</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="stat-resoltes">0</div>
            <div class="stat-label">Resueltas</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="stat-tancades">0</div>
            <div class="stat-label">Cerradas</div>
        </div>
    </div>

    <!-- Overlay de carga -->
    <div id="loading-overlay"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center; flex-direction: column;">
        <div class="spinner-border text-primary" role="status"></div>
        <span class="mt-2 text-white">Cargando...</span>
    </div>

    <div id="incidencias-list-container">
        <div style="text-align: center; padding: 40px; color: #6b7280;">
            <div class="spinner-border text-primary mb-3" role="status"></div>
            <p>Cargando incidencias...</p>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        window.categoriasData = @json($categorias ?? []);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/client-actions.js') }}"></script>
@endpush
