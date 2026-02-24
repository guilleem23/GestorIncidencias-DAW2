@extends('layouts.admin')

@section('title', 'Nexton Admin - Gestión de Incidencias')

@push('styles')
    @vite(['resources/css/admin_dashboard.css', 'resources/css/admin_incidencias.css'])
@endpush

@section('content')
<div class="header-actions">
    <div>
        <h1>Gestión de Incidencias</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Administra las incidencias reportadas por los clientes.</p>
    </div>
    
    <div class="search-bar">
        <input type="text" class="search-input" placeholder="Buscar por ID, Cliente o Sede...">
        <button class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
    </div>
</div>

@if (session('success'))
    <div class="admin-alert admin-alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="admin-alert admin-alert-error">
        <i class="fa-solid fa-circle-xmark"></i>
        <div>
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    </div>
@endif

<div class="incidents-table-container">
    <table class="incidents-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Cliente</th>
                <th>Sede</th>
                <th>Fecha Creación</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($incidencias as $incidencia)
                @php
                    $tieneTecnics = isset($tecnicsBySede[$incidencia->sede_id]) && $tecnicsBySede[$incidencia->sede_id]->count() > 0;
                @endphp
                <tr>
                    <td>#INC-{{ $incidencia->id }}</td>
                    <td>{{ $incidencia->titol }}</td>
                    <td>{{ $incidencia->cliente?->name ?? '-' }}</td>
                    <td>{{ $incidencia->sede?->nom ?? '-' }}</td>
                    <td>{{ $incidencia->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $incidencia->estat === 'Sense assignar' ? 'badge-pending' : 'badge-assigned' }}">
                            {{ $incidencia->estat }}
                        </span>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.incidencias.assign', $incidencia->id) }}" class="assign-form">
                            @csrf
                            <select name="tecnic_id" class="assign-select" {{ $tieneTecnics ? '' : 'disabled' }}>
                                <option value="">Selecciona técnico...</option>
                                @foreach (($tecnicsBySede[$incidencia->sede_id] ?? collect()) as $tecnic)
                                    <option value="{{ $tecnic->id }}" {{ (int) $incidencia->tecnic_id === (int) $tecnic->id ? 'selected' : '' }}>
                                        {{ $tecnic->name }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="submit" class="btn-action" {{ $tieneTecnics ? '' : 'disabled' }}>
                                Asignar técnico
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding: 2rem; color: var(--text-secondary);">
                        No hay incidencias registradas.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

