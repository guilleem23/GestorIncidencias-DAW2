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
        <form id="form-filters" method="GET" action="{{ route('client.index') }}" class="filters-form" onsubmit="return false;">
            <div class="filter-group">
                <label for="estat"><i class="fas fa-filter"></i> Estado</label>
                <select name="estat" id="estat" class="filter-select">
                    <option value="">Todos los estados</option>
                    @foreach($estats as $key => $value)
                        <option value="{{ $key }}" {{ $estatFilter == $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="orden"><i class="fas fa-sort"></i> Ordenar</label>
                <select name="orden" id="orden" class="filter-select">
                    <option value="desc" {{ $ordenFilter == 'desc' ? 'selected' : '' }}>Más recientes primero</option>
                    <option value="asc" {{ $ordenFilter == 'asc' ? 'selected' : '' }}>Más antiguas primero</option>
                </select>
            </div>

            <div class="filter-group">
                <div class="filter-actions">
                    <button type="button" id="btn-toggle-closed" class="btn-toggle-closed {{ $ocultarResoltes ? 'active' : '' }}">
                        <i class="fa-solid {{ $ocultarResoltes ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                        {{ $ocultarResoltes ? 'Mostrar resueltas/cerradas' : 'Ocultar resueltas/cerradas' }}
                    </button>
                    <button type="button" id="btn-clear-filters" class="btn btn-outline">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div id="incidencias-list-container">
        @include('client.partials.incidencias_list', ['incidencies' => $incidencies])
    </div>

    <!-- Modal Editar Incidencia -->
    <div class="modal fade" id="modalEditarIncidencia" tabindex="-1" aria-labelledby="modalEditarIncidenciaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="modalEditarIncidenciaLabel">
                        <i class="fa-solid fa-pen-to-square"></i> Editar Incidencia
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="modal-editar-incidencia-content">
                    @include('client.partials.editar_incidencia_form')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.categoriasData = @json($categorias ?? []);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/client-actions.js') }}"></script>
    <script src="{{ asset('js/client/incidencias.js') }}"></script>
@endpush
