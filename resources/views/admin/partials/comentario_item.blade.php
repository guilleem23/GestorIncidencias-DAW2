@php
    $isMine = (int)($comentario->usuario_id ?? 0) === (int)auth()->id();
@endphp
<div class="comment-item-wrapper fade-in-up" data-comment-id="{{ $comentario->id }}">
    <div style="display:flex;">
        <div style="max-width: 92%; margin-left: {{ $isMine ? 'auto' : '0' }}; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.85rem 1rem; background: {{ $isMine ? 'rgba(15, 23, 42, 0.40)' : 'rgba(15, 23, 42, 0.25)' }};">
            <div style="display:flex; justify-content:space-between; gap: 1rem; align-items: baseline;">
                <span style="font-weight: 600;">{{ $comentario->usuario?->name ?? 'Usuario' }}</span>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <span style="color: var(--text-secondary); font-size: 0.85rem; white-space: nowrap;">{{ $comentario->created_at?->format('d/m/Y H:i') }}</span>
                    @if($isMine)
                        <button type="button" 
                                class="btn-delete-comment-action" 
                                id="btn-delete-comment-{{ $comentario->id }}"
                                data-id="{{ $comentario->id }}"
                                style="background: transparent; border: none; color: #ef4444; cursor: pointer; padding: 0.25rem 0.5rem; font-size: 0.9rem; transition: color 0.2s;"
                                title="Eliminar comentario">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    @endif
                </div>
            </div>
            <div style="margin-top: 0.4rem; color: var(--text-secondary);">
                {!! nl2br(e($comentario->missatge)) !!}
            </div>
        </div>
    </div>
</div>
