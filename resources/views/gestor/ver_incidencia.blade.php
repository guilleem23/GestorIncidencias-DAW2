@extends('layouts.gestor')

@section('title', 'Ver Incidencia - Gestor')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/gestor_historial.css') }}"> <!-- Reusing badges -->
    <link rel="stylesheet" href="{{ asset('css/gestor_incidencia_detail.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="detail-container">
        <div class="detail-header">
            <h1>Detalles de la Incidencia #{{ $incidencia->id }}</h1>
            <a href="{{ route('gestor.incidencias') }}" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Volver al Historial
            </a>
        </div>

        <div class="card-premium">
            <div class="card-header-premium">
                <div>
                    <h2 class="incidencia-title-large">{{ $incidencia->titol }}</h2>
                    <div class="incidencia-meta-top">
                        <div class="meta-item">
                            <i class="fa-solid fa-hashtag"></i>
                            ID: {{ $incidencia->id }}
                        </div>
                        <div class="meta-item">
                            <i class="fa-solid fa-calendar"></i>
                            {{ $incidencia->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="meta-item">
                            <i class="fa-solid fa-layer-group"></i>
                            {{ $incidencia->categoria->nom }} / {{ $incidencia->subcategoria->nom }}
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
                        <span class="status-badge badge-inactive">Sense assignar</span>
                    @elseif($incidencia->estat === 'Assignada')
                        <span class="status-badge status-assignada">Assignada</span>
                    @elseif($incidencia->estat === 'En treball')
                        <span class="status-badge status-treball">En treball</span>
                    @elseif($incidencia->estat === 'Resolta')
                        <span class="status-badge status-resolta">Resolta</span>
                    @elseif($incidencia->estat === 'Tancada')
                        <span class="status-badge badge-active">Tancada</span>
                    @else
                        <span class="status-badge badge-active">{{ $incidencia->estat }}</span>
                    @endif
                </div>
            </div>

            <div class="card-body-premium">
                <div class="info-grid">
                    <div class="info-block">
                        <span class="info-label">Cliente Reportador</span>
                        <div class="user-profile-mini">
                            <div class="avatar-mini">{{ substr($incidencia->cliente->name, 0, 1) }}</div>
                            <div class="user-text-mini">
                                <a class="user-name-mini" href="{{ route('gestor.usuarios.show', $incidencia->cliente->id) }}" style="text-decoration:none; color: inherit;">
                                    {{ $incidencia->cliente->name }}
                                </a>
                                <span class="user-email-mini">{{ $incidencia->cliente->email }}</span>
                            </div>
                        </div>
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

                <div class="description-block" style="margin-top: 1.5rem;">
                    <span class="info-label">Comentarios</span>
                    @if($incidencia->comentarios && $incidencia->comentarios->count())
                        <div style="margin-top: 0.75rem; display:flex; flex-direction:column; gap:0.75rem;">
                            @foreach($incidencia->comentarios as $comentario)
                                <div style="border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.85rem 1rem; background: rgba(15, 23, 42, 0.25);">
                                    <div style="display:flex; justify-content:space-between; gap: 1rem; align-items: baseline;">
                                        <span style="font-weight: 600;">{{ $comentario->usuario?->name ?? 'Usuario' }}</span>
                                        <span style="color: var(--text-secondary); font-size: 0.85rem; white-space: nowrap;">{{ $comentario->created_at?->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div style="margin-top: 0.4rem; color: var(--text-secondary);">
                                        {!! nl2br(e($comentario->missatge)) !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="margin-top: 0.5rem; color: var(--text-secondary);">
                            Sin comentarios.
                        </div>
                    @endif
                </div>
                
                <div class="form-actions" style="margin-top: 2rem;">
                    <a href="{{ route('gestor.incidencias.edit', $incidencia->id) }}" class="btn-primary">
                        <i class="fa-solid fa-pen"></i> Editar Incidencia
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
