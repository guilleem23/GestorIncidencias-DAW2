@php
    $isMine = (int)($comentario->usuario_id ?? 0) === (int)auth()->id();
@endphp
<div class="comment-item-wrapper" data-id="{{ $comentario->id }}" style="display:flex; animation: fadeIn 0.3s ease;">
    <div class="comment-bubble" style="position: relative; max-width: 92%; margin-left: {{ $isMine ? 'auto' : '0' }}; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.85rem 1rem; background: {{ $isMine ? 'rgba(15, 23, 42, 0.40)' : 'rgba(15, 23, 42, 0.25)' }};">
        <div style="display:flex; justify-content:space-between; gap: 1rem; align-items: baseline;">
            <span style="font-weight: 600;">{{ $comentario->usuario?->name ?? 'Usuario' }}</span>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span style="color: var(--text-secondary); font-size: 0.85rem; white-space: nowrap;">{{ $comentario->created_at?->format('d/m/Y H:i') }}</span>
                @if($isMine)
                    <button type="button" id="btn-delete-comment-{{ $comentario->id }}" class="btn-delete-comment btn-delete-comment-action" data-id="{{ $comentario->id }}" title="Eliminar comentario" style="background: none; border: none; color: #ef4444; padding: 0; cursor: pointer; font-size: 0.85rem; opacity: 0.6; transition: opacity 0.2s;">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endif
            </div>
        </div>
        <div style="margin-top: 0.4rem; color: var(--text-secondary);">
            {!! nl2br(e($comentario->missatge)) !!}
        </div>
    </div>
</div>
