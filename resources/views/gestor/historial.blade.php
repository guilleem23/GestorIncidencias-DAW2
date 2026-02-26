@extends('layouts.gestor')

@section('title', 'Historial de Incidencias - Gestor')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/gestor_historial.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="gestor-header">
        <h1>Incidencias de la Sede: {{ auth()->user()->sede->nom ?? 'General' }}</h1>
    </div>

    <!-- Filtros dinámicos -->
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
                    <option value="Sense assignar" {{ request('estat') === 'Sense assignar' ? 'selected' : '' }}>Sense assignar</option>
                    <option value="Assignada" {{ request('estat') === 'Assignada' ? 'selected' : '' }}>Assignada</option>
                    <option value="En treball" {{ request('estat') === 'En treball' ? 'selected' : '' }}>En treball</option>
                    <option value="Resolta" {{ request('estat') === 'Resolta' ? 'selected' : '' }}>Resolta</option>
                    <option value="Tancada" {{ request('estat') === 'Tancada' ? 'selected' : '' }}>Tancada</option>
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
                <label class="filter-label"><i class="fa-solid fa-user-gear"></i> Técnico</label>
                <select id="filter-tecnic" class="filter-select">
                    <option value="">Todos los técnicos</option>
                    @foreach($tecnicos as $tecnico)
                        <option value="{{ $tecnico->id }}" {{ request('tecnic_id') == $tecnico->id ? 'selected' : '' }}>{{ $tecnico->name }}</option>
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

    <!-- Tabla de incidencias (reemplazada vía AJAX) -->
    <div class="table-container" id="incidencias-table-wrapper">
        @include('gestor.partials.incidencias_table')
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/gestor/incidencias.js') }}"></script>
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: {
                        popup: 'swal-dark-popup'
                    }
                });
            });
        </script>
    @endif
@endpush
