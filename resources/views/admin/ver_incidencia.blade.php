@extends('layouts.admin')

@section('title', 'Ver Incidencia - Admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/gestor_historial.css') }}">
    <link rel="stylesheet" href="{{ asset('css/gestor_incidencia_detail.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="detail-container">
        <div class="detail-header">
            <h1>Detalles de la Incidencia #{{ $incidencia->id }}</h1>
            <a href="{{ route('admin.incidencias') }}" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Volver al Listado
            </a>
        </div>

        <div class="card-premium">
            <div class="card-header-premium">
                <div>
                    <h2 class="incidencia-title-large">{{ $incidencia->titol }}</h2>
                    <div class="incidencia-meta-top">
                        <div class="meta-item">
                            <i class="fa-solid fa-calendar"></i>
                            {{ $incidencia->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="meta-item">
                            <i class="fa-solid fa-building"></i>
                            {{ $incidencia->sede?->nom ?? '-' }}
                        </div>
                        <div class="meta-item">
                            <i class="fa-solid fa-layer-group"></i>
                            {{ $incidencia->categoria?->nom ?? '-' }} / {{ $incidencia->subcategoria?->nom ?? '-' }}
                        </div>
                    </div>
                </div>
                <div style="display: flex; gap: 0.75rem; align-items: center;">
                    @if($incidencia->prioritat)
                        @if($incidencia->prioritat === 'alta')
                            <span class="priority-badge priority-alta"><i class="fa-solid fa-arrow-up"></i> Alta</span>
                        @elseif($incidencia->prioritat === 'mitjana')
                            <span class="priority-badge priority-mitjana"><i class="fa-solid fa-minus"></i> Media</span>
                        @else
                            <span class="priority-badge priority-baixa"><i class="fa-solid fa-arrow-down"></i> Baja</span>
                        @endif
                    @endif

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
                </div>
            </div>

            <div class="card-body-premium">
                <div class="info-grid">
                    <div class="info-block">
                        <span class="info-label">Cliente Reportador</span>
                        @if($incidencia->cliente)
                            <div class="user-profile-mini">
                                <div class="avatar-mini">{{ substr($incidencia->cliente->name, 0, 1) }}</div>
                                <div class="user-text-mini">
                                    <span class="user-name-mini">{{ $incidencia->cliente->name }}</span>
                                    <span class="user-email-mini">{{ $incidencia->cliente->email }}</span>
                                </div>
                            </div>
                        @else
                            <div class="user-profile-mini" style="opacity: 0.6">
                                <div class="avatar-mini" style="background: #4b5563;"><i class="fa-solid fa-user-xmark"></i></div>
                                <div class="user-text-mini">
                                    <span class="user-name-mini">Sin cliente</span>
                                    <span class="user-email-mini">-</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="info-block">
                        <span class="info-label">Técnico Asignado</span>
                        @if($incidencia->tecnico)
                            <div class="user-profile-mini">
                                <div class="avatar-mini">{{ substr($incidencia->tecnico->name, 0, 1) }}</div>
                                <div class="user-text-mini">
                                    <span class="user-name-mini">{{ $incidencia->tecnico->name }}</span>
                                    <span class="user-email-mini">{{ $incidencia->tecnico->email }}</span>
                                </div>
                            </div>
                        @else
                            <div class="user-profile-mini" style="opacity: 0.6">
                                <div class="avatar-mini" style="background: #4b5563;"><i class="fa-solid fa-user-xmark"></i></div>
                                <div class="user-text-mini">
                                    <span class="user-name-mini">Sin asignar</span>
                                    <span class="user-email-mini">Esperando asignación</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="description-block">
                    <span class="info-label">Descripción de la incidencia</span>
                    <div class="description-content">
                        {!! nl2br(e($incidencia->descripcio)) !!}
                    </div>
                </div>
                
                <div class="form-actions" style="margin-top: 2rem;">
                    <a href="{{ route('admin.incidencias.edit', $incidencia->id) }}" class="btn-primary">
                        <i class="fa-solid fa-pen"></i> Editar Incidencia
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
