@extends('layouts.admin')

@section('title', 'Resumen - Nexton Admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_resum.css') }}">
@endpush

@section('content')
<!-- Header -->
<div class="dashboard-header">
    <div class="header-text">
        <h1>Resumen</h1>
        <p>Vista de resumen de las incidencias</p>
    </div>
</div>

<!-- Selector de Sede -->
<div class="sede-selector-wrapper">
    <label for="sede-select" class="sede-label">Seleccionar Sede</label>
    <select id="sede-select" class="sede-select" onchange="cambiarSede()">
        @foreach($sedes as $sede)
            <option value="{{ $sede->id }}" {{ $sede->id == $sedeSeleccionadaId ? 'selected' : '' }}>
                {{ $sede->nom }}
            </option>
        @endforeach
    </select>
</div>

<!-- Contadores -->
<div class="contadores-grid">
    <!-- Incidencias Resueltas -->
    <div class="contador-card resueltas">
        <div class="contador-info">
            <h3 class="contador-titulo">Incidencias Resueltas</h3>
            <p class="contador-numero" id="contador-resueltas">0</p>
        </div>
    </div>

    <!-- Incidencias Pendientes -->
    <div class="contador-card pendientes">
        <div class="contador-info">
            <h3 class="contador-titulo">Incidencias Pendientes</h3>
            <p class="contador-numero" id="contador-pendientes">0</p>
        </div>
    </div>
</div>

<!-- Tabla de incidencias resueltas -->
<div class="tabla-resueltas-wrapper">
    <h2 class="tabla-titulo">Incidencias Resueltas por Técnico</h2>
    <table class="tabla-resueltas">
        <thead>
            <tr>
                <th>Técnico</th>
                <th>Software</th>
                <th>Hardware</th>
            </tr>
        </thead>
        <tbody id="tabla-resueltas-body">
            <tr>
                <td colspan="3" class="sin-datos">Cargando datos...</td>
            </tr>
        </tbody>
    </table>
</div>

@push('scripts')
<script src="{{ asset('js/admin/admin_resum.js') }}"></script>
@endpush
@endsection
