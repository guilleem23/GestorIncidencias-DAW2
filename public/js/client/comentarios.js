// Manejo de comentarios para cliente
document.addEventListener('DOMContentLoaded', function () {

    // ========== VER IMAGEN COMENTARIO ==========
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('comment-image-clickable') || e.target.classList.contains('comment-image-clickable-client')) {
            const imageSrc = e.target.src;
            const modal = new bootstrap.Modal(document.getElementById('modalImagenComentario'));
            document.getElementById('modal-imagen-src').src = imageSrc;
            modal.show();
        }
    });

    // ========== AÑADIR COMENTARIO (FETCH) ==========
    const formComentario = document.getElementById('form-comentario');
    const commentsContainer = document.getElementById('comments-container');
    const missatgeInput = document.getElementById('missatge-comentario');
    const fileInput = document.getElementById('imatge-comentario');
    const fileNameDisplay = document.getElementById('file-name-display');
    const btnSubmitComentario = document.getElementById('btn-submit-comentario');

    if (fileInput && fileNameDisplay) {
        fileInput.onchange = function () {
            fileNameDisplay.textContent = this.files && this.files.length ? this.files[0].name : '';
        };
    }

    if (formComentario && commentsContainer) {
        formComentario.addEventListener('submit', function (e) {
            e.preventDefault();

            const missatge = (missatgeInput?.value || '').trim();
            const hasImage = !!(fileInput && fileInput.files && fileInput.files.length > 0);

            if (missatge.length < 1 && !hasImage) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Comentario muy corto',
                    text: 'Escribe al menos 1 carácter o adjunta una imagen.',
                    background: '#111111',
                    color: '#f8fafc'
                });
                return;
            }

            const formData = new FormData(this);

            if (btnSubmitComentario) {
                btnSubmitComentario.disabled = true;
                btnSubmitComentario.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enviando...';
            }

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'No se pudo añadir el comentario.');
                    }

                    const noCommentsMsg = document.getElementById('no-comments-msg');
                    if (noCommentsMsg) noCommentsMsg.remove();

                    if (data.html) {
                        commentsContainer.insertAdjacentHTML('beforeend', data.html);
                    }

                    if (missatgeInput) missatgeInput.value = '';
                    if (fileInput) fileInput.value = '';
                    if (fileNameDisplay) fileNameDisplay.textContent = '';

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message || 'Comentario añadido.',
                        showConfirmButton: false,
                        timer: 1800,
                        background: '#111111',
                        color: '#f8fafc'
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'No se pudo enviar el comentario.',
                        background: '#111111',
                        color: '#f8fafc'
                    });
                })
                .finally(() => {
                    if (btnSubmitComentario) {
                        btnSubmitComentario.disabled = false;
                        btnSubmitComentario.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Enviar comentario';
                    }
                });
        });
    }

    // ========== EDITAR COMENTARIO ==========
    document.addEventListener('click', function (e) {
        if (e.target.closest('.btn-edit-comment-client')) {
            const btn = e.target.closest('.btn-edit-comment-client');
            const comentarioId = btn.dataset.id;

            // Obtener datos del comentario desde el DOM
            const comentarioItem = btn.closest('.comment-item-wrapper');
            const mensajeDiv = comentarioItem ? comentarioItem.querySelector('.comment-item-text') : null;
            const mensaje = mensajeDiv ? mensajeDiv.textContent.trim() : '';

            // Rellenar el modal
            document.getElementById('edit-missatge-comentario').value = mensaje;
            document.getElementById('edit-imatge-comentario').value = '';
            document.getElementById('edit-file-name-display').textContent = '';

            // Configurar formulario
            const form = document.getElementById('form-editar-comentario');
            form.action = `/client/comentarios/${comentarioId}`;

            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('modalEditarComentario'));
            modal.show();
        }
    });

    // Mostrar nombre de archivo seleccionado
    const editImatgeInput = document.getElementById('edit-imatge-comentario');
    if (editImatgeInput) {
        editImatgeInput.addEventListener('change', function () {
            const display = document.getElementById('edit-file-name-display');
            if (this.files.length > 0) {
                display.textContent = `📎 ${this.files[0].name}`;
            } else {
                display.textContent = '';
            }
        });
    }

    // Enviar formulario de edición
    const formEditarComentario = document.getElementById('form-editar-comentario');
    if (formEditarComentario) {
        formEditarComentario.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const actionUrl = this.action;

            fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cerrar modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarComentario'));
                        modal.hide();

                        // Mostrar mensaje
                        Swal.fire({
                            icon: 'success',
                            title: 'Comentario actualizado',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false,
                            background: '#111111',
                            color: '#f8fafc'
                        }).then(() => {
                            // Recargar página para mostrar cambios
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.error || 'Error al actualizar el comentario');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'No se pudo actualizar el comentario',
                        background: '#111111',
                        color: '#f8fafc'
                    });
                });
        });
    }

    // ========== ELIMINAR COMENTARIO ==========
    document.addEventListener('click', function (e) {
        if (e.target.closest('.btn-delete-comment-client')) {
            const btn = e.target.closest('.btn-delete-comment-client');
            const comentarioId = btn.dataset.id;

            Swal.fire({
                title: '¿Eliminar comentario?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fa-solid fa-trash"></i> Eliminar',
                cancelButtonText: '<i class="fa-solid fa-times"></i> Cancelar',
                background: '#111111',
                color: '#f8fafc'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Realizar petición DELETE
                    fetch(`/client/comentarios/${comentarioId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                    background: '#111111',
                                    color: '#f8fafc'
                                }).then(() => {
                                    // Eliminar comentario del DOM con animación
                                    const comentarioItem = btn.closest('.comment-item-wrapper');
                                    if (!comentarioItem) {
                                        window.location.reload();
                                        return;
                                    }

                                    // Animación de salida usando clase CSS
                                    comentarioItem.classList.add('fade-out-right');

                                    setTimeout(() => {
                                        comentarioItem.remove();

                                        // Verificar si no quedan comentarios
                                        const container = document.getElementById('comments-container');
                                        if (container && container.querySelectorAll('.comment-item-wrapper').length === 0) {
                                            // Mostrar mensaje de "sin comentarios" con animación de entrada
                                            container.innerHTML = '<div id="no-comments-msg" class="fade-in-up" style="margin-top: 0.5rem; color: var(--text-secondary);"><i class="fas fa-comment-dots"></i> Sin comentarios todavía</div>';
                                        }
                                    }, 400); // Dar tiempo a la animación fade-out-right (0.4s)
                                });
                            } else {
                                throw new Error(data.error || 'Error al eliminar el comentario');
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message || 'No se pudo eliminar el comentario',
                                background: '#111111',
                                color: '#f8fafc'
                            });
                        });
                }
            });
        }
    });

    // ========== HOVER EFFECTS ==========
    // Hover effects via CSS classes already in global stylesheets (or admin_sidebar.css)
});
