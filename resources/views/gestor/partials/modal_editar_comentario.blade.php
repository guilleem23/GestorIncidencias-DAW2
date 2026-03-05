<!-- Modal Editar Comentario -->
<div class="modal fade" id="modalEditarComentario" tabindex="-1" aria-labelledby="modalEditarComentarioLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="modalEditarComentarioLabel">
                    <i class="fa-solid fa-pen-to-square"></i> Editar Comentario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Cerrar"></button>
            </div>
            <form id="form-editar-comentario" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit-missatge-comentario" class="form-label">
                            <i class="fa-solid fa-comment"></i> Comentario
                        </label>
                        <textarea id="edit-missatge-comentario" name="missatge" class="form-control bg-secondary text-white border-secondary"
                            rows="4" placeholder="Escribe un comentario..."></textarea>
                        <small class="form-text text-muted">Opcional si adjuntas una imagen.</small>
                    </div>

                    <div class="form-group mt-3">
                        <label for="edit-imatge-comentario" class="form-label">
                            <i class="fa-solid fa-image"></i> Imagen
                        </label>
                        <input type="file" id="edit-imatge-comentario" name="imatge"
                            class="form-control bg-secondary text-white border-secondary" accept="image/*">
                        <small class="form-text text-muted">JPG, PNG, GIF, WebP. Máximo 4MB.</small>
                        <div id="edit-file-name-display"
                            style="margin-top: 0.5rem; color: var(--text-secondary); font-size: 0.85rem;"></div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary py-2" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btn-submit-edit-comentario" class="btn btn-primary py-2">
                        <i class="fa-solid fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
