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
                            <i class="fa-solid fa-hashtag"></i>
                            ID: {{ $incidencia->id }}
                        </div>
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
                                    <a class="user-name-mini" href="{{ route('admin.usuarios.show', $incidencia->cliente->id) }}" style="text-decoration:none; color: inherit;">
                                        {{ $incidencia->cliente->name }}
                                    </a>
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

                <div class="description-block" style="margin-top: 1.5rem;">
                    <span class="info-label">Comentarios</span>
                    <div id="comments-container" style="margin-top: 0.75rem; display:flex; flex-direction:column; gap:0.75rem;">
                        @if($incidencia->comentarios && $incidencia->comentarios->count())
                            @foreach($incidencia->comentarios as $comentario)
                                @include('admin.partials.comentario_item', ['comentario' => $comentario])
                            @endforeach
                        @else
                            <div id="no-comments-msg" class="fade-in-up" style="margin-top: 0.5rem; color: var(--text-secondary);">
                                Sin comentarios.
                            </div>
                        @endif
                    </div>

                    <form id="form-comentario" method="POST" action="{{ route('admin.incidencias.comentarios.store', $incidencia->id) }}" style="margin-top: 0.9rem;">
                        @csrf
                        <div style="display:flex; flex-direction:column; gap:0.5rem;">
                            <textarea id="missatge-comentario" name="missatge" rows="3" class="comment-textarea" placeholder="Añadir comentario..." required minlength="2" maxlength="2000"></textarea>
                            @error('missatge')
                                <div class="alert-custom alert-error-custom" style="margin: 0;">
                                    <i class="fa-solid fa-circle-xmark"></i> {{ $message }}
                                </div>
                            @enderror
                            <div style="display:flex; justify-content:flex-end;">
                                <button type="submit" id="btn-submit-comentario" class="btn-primary" style="padding: 0.65rem 1rem;">
                                    <i class="fa-solid fa-paper-plane"></i> Enviar
                                </button>
                            </div>
                        </div>
                    </form>
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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/admin/ver_incidencia.js') }}"></script>
@endpush

