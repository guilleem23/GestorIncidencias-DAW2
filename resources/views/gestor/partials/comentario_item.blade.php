@php
    $isMine = (int)($comentario->usuario_id ?? 0) === (int)auth()->id();
@endphp
<div class="comment-item-wrapper gestor" data-comment-id="{{ $comentario->id }}">
    <div class="comment-item-container">
        <div class="comment-item-bubble gestor {{ $isMine ? 'sent' : 'received' }}">
            <div class="comment-item-header gestor">
                <span class="comment-item-name gestor">{{ $comentario->usuario?->name ?? 'Usuario' }}</span>
                <div class="comment-item-actions gestor">
                    <span class="comment-item-timestamp gestor">{{ $comentario->created_at?->format('d/m/Y H:i') }}</span>
                    @if($isMine)
                        <button type="button" 
                                class="btn-comment-action btn-edit-comment-action gestor" 
                                id="btn-edit-comment-{{ $comentario->id }}"
                                data-id="{{ $comentario->id }}"
                                title="Editar comentario">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button type="button" 
                                class="btn-comment-action btn-delete-comment-action gestor" 
                                id="btn-delete-comment-{{ $comentario->id }}"
                                data-id="{{ $comentario->id }}"
                                title="Eliminar comentario">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    @endif
                </div>
            </div>
            @if(!empty($comentario->missatge))
                <div class="comment-item-text gestor">
                    {!! nl2br(e($comentario->missatge)) !!}
                </div>
            @endif
            @if(!empty($comentario->imatge_path))
                <div class="comment-item-image gestor">
                    <img src="{{ asset('storage/' . $comentario->imatge_path) }}" 
                         alt="Imagen adjunta"
                         class="comment-image-clickable">
                </div>
            @endif
        </div>
    </div>
</div>
