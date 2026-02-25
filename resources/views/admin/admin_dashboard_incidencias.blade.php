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
                <input type="text" id="filter-buscar" class="filter-input" placeholder="Título, descripción, cliente..." value="{{ request('buscar') }}">
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
            <div class="filter-group filter-actions">
                <button type="button" id="btn-clear-filters" class="btn-clear-filters">
                    <i class="fa-solid fa-xmark"></i> Limpiar filtros
                </button>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="table-container">
        @if($incidencias->isEmpty())
            <div class="empty-state-box">
                <i class="fa-solid fa-folder-open fa-3x"></i>
                <p>No se encontraron incidencias con los filtros seleccionados.</p>
            </div>
        @else
            <table class="historial-table">
                <thead>
                    <tr>
                        <th>Incidencia</th>
                        <th>Cliente</th>
                        <th>Sede</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incidencias as $incidencia)
                    <tr>
                        <td>
                            <span class="info-title">{{ $incidencia->titol }}</span>
                        </td>
                        <td>{{ $incidencia->cliente?->name ?? '-' }}</td>
                        <td>{{ $incidencia->sede?->nom ?? '-' }}</td>
                        <td>
                            @if($incidencia->prioritat === 'alta')
                                <span class="priority-badge priority-alta"><i class="fa-solid fa-arrow-up"></i> Alta</span>
                            @elseif($incidencia->prioritat === 'mitjana')
                                <span class="priority-badge priority-mitjana"><i class="fa-solid fa-minus"></i> Media</span>
                            @elseif($incidencia->prioritat === 'baixa')
                                <span class="priority-badge priority-baixa"><i class="fa-solid fa-arrow-down"></i> Baja</span>
                            @else
                                <span class="text-secondary">-</span>
                            @endif
                        </td>
                        <td>
                            @if($incidencia->estat === 'Sense assignar')
                                <span class="status-badge badge-inactive">Sin asignar</span>
                            @elseif($incidencia->estat === 'Assignada')
                                <span class="status-badge status-assignada">Asignada</span>
                            @elseif($incidencia->estat === 'En treball')
                                <span class="status-badge status-treball">En trabajo</span>
                            @elseif($incidencia->estat === 'Resolta')
                                <span class="status-badge status-resolta">Resuelta</span>
                            @elseif($incidencia->estat === 'Tancada')
                                <span class="status-badge badge-active">Cerrada</span>
                            @else
                                <span class="status-badge badge-active">{{ $incidencia->estat }}</span>
                            @endif
                        </td>
                        <td class="date-cell">{{ $incidencia->created_at?->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="actions-cell">
                                <a href="{{ route('admin.incidencias.show', $incidencia->id) }}" class="btn-icon btn-view" title="Ver Detalles">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.incidencias.edit', $incidencia->id) }}" class="btn-icon btn-edit" title="Editar Incidencia">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($incidencias->hasPages())
                <div class="pagination-wrapper">
                    {{ $incidencias->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const baseUrl = '{{ route("admin.incidencias") }}';

            function applyFilters() {
                const params = new URLSearchParams();

                const buscar = document.getElementById('filter-buscar').value;
                const estat = document.getElementById('filter-estat').value;
                const prioritat = document.getElementById('filter-prioritat').value;
                const sede = document.getElementById('filter-sede').value;
                const orden = document.getElementById('filter-orden').value;

                if (buscar) params.set('buscar', buscar);
                if (estat) params.set('estat', estat);
                if (prioritat) params.set('prioritat', prioritat);
                if (sede) params.set('sede_id', sede);
                if (orden && orden !== 'desc') params.set('orden', orden);

                const queryString = params.toString();
                window.location.href = baseUrl + (queryString ? '?' + queryString : '');
            }

            // Dropdowns trigger navigation immediately
            ['filter-estat', 'filter-prioritat', 'filter-sede', 'filter-orden'].forEach(function(id) {
                document.getElementById(id).addEventListener('change', applyFilters);
            });

            // Search with debounce
            let searchTimeout;
            document.getElementById('filter-buscar').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(applyFilters, 400);
            });

            // Clear filters
            document.getElementById('btn-clear-filters').addEventListener('click', function() {
                window.location.href = baseUrl;
            });
        });
    </script>
@endpush
