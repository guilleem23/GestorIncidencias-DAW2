@extends('layouts.admin')

@section('title', 'Dashboard Validación')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/gestor_incidencia_detail.css') }}">
@endpush

@section('content')
    <div class="dashboard-header">
        <div class="header-text">
            <h1><i class="fa-solid fa-clipboard-check"></i> Dashboard Validación</h1>
            <p>Resumen por sede</p>
        </div>
    </div>

    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('admin.dashboard') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Volver al Dashboard Global
        </a>
    </div>

    <div class="charts-grid">
        <div class="chart-card">
            <h3 class="card-title"><i class="fa-solid fa-building"></i> Seleccionar sede</h3>
            <label for="sede_id" class="form-label">Sede</label>
            <select id="sede_id" class="form-select" data-resum-url="{{ route('admin.resum.data') }}">
                <option value="">Selecciona una sede</option>
                @foreach($sedes as $sede)
                    <option value="{{ $sede->id }}">{{ $sede->nom }}</option>
                @endforeach
            </select>
        </div>

        <div class="chart-card">
            <h3 class="card-title"><i class="fa-solid fa-chart-column"></i> Volumen de incidencias</h3>

            <div id="resum-kpis">
                <div class="empty-state">
                    <i class="fa-regular fa-circle-question"></i>
                    <p>Selecciona una sede para ver el volumen de incidencias.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="resum-table"></div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/resum.js') }}"></script>
@endpush
