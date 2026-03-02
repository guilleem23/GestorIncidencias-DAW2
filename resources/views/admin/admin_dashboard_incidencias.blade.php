@extends('layouts.admin')

@section('title', 'Nexton Admin - Incidencias')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_categorias.css') }}">
    <link rel="stylesheet" href="{{ asset('css/gestor_historial.css') }}">
@endpush

@section('content')
<div class="categorias-container">
    <div class="categorias-header">
        <h1><i class="fa-solid fa-triangle-exclamation"></i> Incidencias</h1>
    </div>

    {{-- Mensajes --}}
    @if (session('success'))
        <div class="alert-custom alert-success-custom">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert-custom alert-error-custom">
            <i class="fa-solid fa-circle-xmark"></i>
            @foreach ($errors->all() as $error)
                <span>{{ $error }}</span>
            @endforeach
        </div>
    @endif

    {{-- Filtros --}}
    <div class="filters-container">
        <div class="filters-grid">
            <div class="filter-group filter-search">
                <label class="filter-label"><i class="fa-solid fa-magnifying-glass"></i> Buscar</label>
                <input type="text" id="filter-buscar" class="filter-input" placeholder="ID, Título, descripción, cliente..." value="{{ request('buscar') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label"><i class="fa-solid fa-filter"></i> Estado</label>
                <select id="filter-estat" class="filter-select">
                    <option value="">Todos los estados</option>
                    <option value="Sense assignar" {{ request('estat') === 'Sense assignar' ? 'selected' : '' }}>Sin asignar</option>
                    <option value="Assignada" {{ request('estat') === 'Assignada' ? 'selected' : '' }}>Asignada</option>
                    <option value="En treball" {{ request('estat') === 'En treball' ? 'selected' : '' }}>En trabajo</option>
                    <option value="Resolta" {{ request('estat') === 'Resolta' ? 'selected' : '' }}>Resuelta</option>
                    <option value="Tancada" {{ request('estat') === 'Tancada' ? 'selected' : '' }}>Cerrada</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label"><i class="fa-solid fa-flag"></i> Prioridad</label>
                <select id="filter-prioritat" class="filter-select">
                    <option value="">Todas las prioridades</option>
                    <option value="alta" {{ request('prioritat') === 'alta' ? 'selected' : '' }}>Alta</option>
                    <option value="mitjana" {{ request('prioritat') === 'mitjana' ? 'selected' : '' }}>Media</option>
                    <option value="baixa" {{ request('prioritat') === 'baixa' ? 'selected' : '' }}>Baja</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label"><i class="fa-solid fa-building"></i> Sede</label>
                <select id="filter-sede" class="filter-select">
                    <option value="">Todas las sedes</option>
                    @foreach($sedes as $sede)
                        <option value="{{ $sede->id }}" {{ request('sede_id') == $sede->id ? 'selected' : '' }}>{{ $sede->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label"><i class="fa-solid fa-sort"></i> Ordenar</label>
                <select id="filter-orden" class="filter-select">
                    <option value="desc" {{ request('orden', 'desc') === 'desc' ? 'selected' : '' }}>Más recientes primero</option>
                    <option value="asc" {{ request('orden') === 'asc' ? 'selected' : '' }}>Más antiguas primero</option>
                </select>
            </div>
            <div class="filter-group filter-actions" style="gap: 0.5rem;">
                <button type="button" id="btn-toggle-closed" class="btn-toggle-closed" title="Mostrar/Ocultar cerradas">
                    <i class="fa-solid fa-eye-slash"></i> Mostrar cerradas
                </button>
                <button type="button" id="btn-clear-filters" class="btn-clear-filters">
                    <i class="fa-solid fa-xmark"></i> Limpiar filtros
                </button>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div id="incidencias-table-container">
        @include('admin.partials.tabla_incidencias')
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/gestor/incidencias.js') }}"></script>
@endpush
