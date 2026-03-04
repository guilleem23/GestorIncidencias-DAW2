@extends('layouts.gestor')

@section('title', 'Ver Incidencia - Gestor')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/gestor_historial.css') }}"> <!-- Reusing badges -->
    <link rel="stylesheet" href="{{ asset('css/gestor_incidencia_detail.css') }}">
    <link rel="stylesheet" href="{{ asset('css/comentarios.css') }}">
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
                    <div id="comments-container" style="margin-top: 0.75rem; display:flex; flex-direction:column; gap:0.75rem;">
                        @if($incidencia->comentarios && $incidencia->comentarios->count())
                            @foreach($incidencia->comentarios as $comentario)
                                @include('gestor.partials.comentario_item', ['comentario' => $comentario])
                            @endforeach
                        @else
                            <div id="no-comments-msg" class="fade-in-up" style="margin-top: 0.5rem; color: var(--text-secondary);">
                                Sin comentarios.
                            </div>
                        @endif
                    </div>

                    <form id="form-comentario" method="POST" action="{{ route('gestor.incidencias.comentarios.store', $incidencia->id) }}" enctype="multipart/form-data" style="margin-top: 0.9rem;">
                        @csrf
                        <div style="display:flex; flex-direction:column; gap:0.5rem;">
                            <textarea id="missatge-comentario" name="missatge" rows="3" class="comment-textarea" placeholder="Añadir comentario..."></textarea>
                            @error('missatge')
                                <span class="error-message" style="margin-top: 0;">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </span>
                            @enderror

                            <div class="comment-upload-row">
                                <label class="comment-file-label" for="imatge-comentario">
                                    <i class="fa-solid fa-image"></i> Adjuntar imagen
                                </label>
                                <input id="imatge-comentario" type="file" name="imatge" class="comment-file-input" accept="image/*">
                                <span id="file-name-display" style="color: var(--text-secondary); font-size: 0.9rem; margin-left: 0.5rem;"></span>
                            </div>
                            @error('imatge')
                                <span class="error-message" style="margin-top: 0;">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </span>
                            @enderror

                            <div style="display:flex; justify-content:flex-end;">
                                <button type="submit" id="btn-submit-comentario" class="btn-primary" style="padding: 0.55rem 1rem;">
                                    <i class="fa-solid fa-paper-plane"></i> Enviar comentario
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="form-actions" style="margin-top: 2rem;">
                    <button type="button" id="btn-edit-incidencia-{{ $incidencia->id }}" name="editar_incidencia" class="btn-primary btn-editar-incidencia" data-id="{{ $incidencia->id }}" style="padding: 0.5rem 1rem;">
                        <i class="fa-solid fa-pen-to-square"></i> Editar Incidencia
                    </button>
                </div>
            </div>
        </div>
    <!-- Modal Editar Incidencia -->
    <div class="modal fade" id="modalEditarIncidencia" tabindex="-1" aria-labelledby="modalEditarIncidenciaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="modalEditarIncidenciaLabel">
                        <i class="fa-solid fa-pen-to-square"></i> Editar Incidencia
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="modal-editar-content">
                    <!-- El contenido se cargará dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ver Imagen Comentario -->
    <div class="modal fade" id="modalImagenComentario" tabindex="-1" aria-labelledby="modalImagenComentarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="modalImagenComentarioLabel">
                        <i class="fa-solid fa-image"></i> Ver Imagen
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modal-imagen-src" src="" alt="Imagen comentario" style="max-width: 100%; height: auto; border-radius: 0.5rem;">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Comentario -->
    <div class="modal fade" id="modalEditarComentario" tabindex="-1" aria-labelledby="modalEditarComentarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="modalEditarComentarioLabel">
                        <i class="fa-solid fa-pen-to-square"></i> Editar Comentario
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="form-editar-comentario" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit-missatge-comentario" class="form-label">
                                <i class="fa-solid fa-comment"></i> Comentario
                            </label>
                            <textarea id="edit-missatge-comentario" 
                                      name="missatge"
                                      class="form-control bg-secondary text-white border-secondary"
                                      rows="4"
                                      placeholder="Edita tu comentario..."></textarea>
                            <small class="form-text text-muted">Mínimo 2 caracteres.</small>
                        </div>

                        <div class="form-group mt-3">
                            <label for="edit-imatge-comentario" class="form-label">
                                <i class="fa-solid fa-image"></i> Imagen (Opcional)
                            </label>
                            <input type="file" 
                                   id="edit-imatge-comentario"
                                   name="imatge"
                                   class="form-control bg-secondary text-white border-secondary"
                                   accept="image/*">
                            <small class="form-text text-muted">JPG, PNG, GIF, WebP. Máximo 4MB.</small>
                            <div id="edit-file-name-display" style="margin-top: 0.5rem; color: var(--text-secondary); font-size: 0.85rem;"></div>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btn-submit-edit-comentario" class="btn btn-primary">
                            <i class="fa-solid fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        window.categoriasData = @json($categorias);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/gestor/ver_incidencia.js') }}"></script>
    <script src="{{ asset('js/gestor/modales.js') }}"></script>
@endpush
