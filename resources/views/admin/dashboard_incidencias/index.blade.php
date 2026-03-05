@extends('layouts.admin')

@section('title', 'Nexton Admin - Incidencias')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_categorias.css') }}">
    <link rel="stylesheet" href="{{ asset('css/gestor_incidencia_detail.css') }}">
    <link rel="stylesheet" href="{{ asset('css/gestor_historial.css') }}">
@endpush

@section('content')
    <div class="categorias-container">
        <div class="categorias-header">
            <h1><i class="fa-solid fa-triangle-exclamation"></i>Dashboard Incidencias</h1>
                <a href="{{ route('admin.dashboard') }}" class="btn-back">
                    <i class="fa-solid fa-arrow-left"></i> Volver al Dashboard
                </a>
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
            <form method="GET" action="{{ route('admin.resum') }}" class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label"><i class="fa-solid fa-building"></i> Sede</label>
                    <select id="filter-sede" name="sede_id" class="filter-select">
                        <option value="" selected>Selecciona una sede</option>
                        @foreach ($sedes as $sede)
                            <option value="{{ $sede->id }}" {{ request('sede_id') == $sede->id ? 'selected' : '' }}>
                                {{ $sede->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <div id="dashboard-data-container">
            @include('admin.dashboard_incidencias.partials.resumen')
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.categoriasData = @json(\App\Models\Categoria::with('subcategorias')->get());
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/admin/dashboard_incidencias/filtros.js') }}"></script>
@endpush
