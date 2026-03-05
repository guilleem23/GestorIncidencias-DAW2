@extends('layouts.admin')

@section('title', 'Nexton Admin - Resumen por Sede')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-header">
    <div class="header-text">
        <h1><i class="fa-solid fa-chart-bar"></i> Resumen por Sede</h1>
        <p>Incidencias resueltas y pendientes por sede</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn-assign">
        <i class="fa-solid fa-arrow-left"></i> Volver al Dashboard
    </a>
</div>

<div class="chart-card" style="margin-bottom: 1.5rem;">
    <h3 class="card-title"><i class="fa-solid fa-building"></i> Seleccionar Sede</h3>
    <div id="sedes-list" class="kpi-grid" data-api-sedes="{{ route('admin.resum.sedes') }}" data-api-resum="{{ url('/admin/resum') }}"></div>
</div>

<div id="sede-detail" style="display: none;">
    <div class="kpi-grid" style="margin-bottom: 1.5rem;">
        <div class="kpi-card kpi-card--active">
            <div class="kpi-icon-wrap">
                <i class="fa-solid fa-check-circle"></i>
            </div>
            <div class="kpi-body">
                <span>Incidencias Resueltas</span>
                <span class="kpi-value" id="count-resueltas">0</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--pending">
            <div class="kpi-icon-wrap">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div class="kpi-body">
                <span class="kpi-label">Incidencias Pendientes</span>
                <span class="kpi-value" id="count-pendientes">0</span>
            </div>
        </div>
    </div>

    <div class="table-panel">
        <h3 class="card-title"><i class="fa-solid fa-users"></i> Resueltas por Técnico y Categoría</h3>
        <div class="table-responsive">
            <table class="data-table" id="tabla-tecnicos">
                <thead id="tabla-head"></thead>
                <tbody id="tabla-body"></tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/admin/sedes/validacion.js') }}"></script>
@endpush
