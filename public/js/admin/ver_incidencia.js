(function () {
    "use strict";

    // 1. Manejo de Comentarios vía AJAX con soporte para imágenes
    const formComentario = document.getElementById('form-comentario');
    const commentsContainer = document.getElementById('comments-container');
    const missatgeInput = document.getElementById('missatge-comentario');
    const imatgeInput = document.getElementById('imatge-comentario');
    const btnSubmitComentario = document.getElementById('btn-submit-comentario');
    const noCommentsMsg = document.getElementById('no-comments-msg');
    const fileNameDisplay = document.getElementById('file-name-display');

    // Mostrar nombre del archivo seleccionado
    if (imatgeInput && fileNameDisplay) {
        imatgeInput.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                fileNameDisplay.textContent = this.files[0].name;
            } else {
                fileNameDisplay.textContent = '';
            }
        });
    }

    if (formComentario && commentsContainer) {
        formComentario.addEventListener('submit', function (e) {
            e.preventDefault();

            const missatge = missatgeInput.value.trim();
            const hasImage = imatgeInput && imatgeInput.files && imatgeInput.files.length > 0;

            // Validar que haya al menos mensaje o imagen
            if (missatge.length < 1 && !hasImage) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Contenido requerido',
                    text: 'Debes escribir un comentario o adjuntar una imagen.',
                    background: '#1e293b',
                    color: '#f8fafc'
                });
                return;
            }

            // Validar longitud mínima solo si hay mensaje
            if (missatge.length > 0 && missatge.length < 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Comentario muy corto',
                    text: 'El comentario debe tener al menos 1 carácter.',
                    background: '#1e293b',
                    color: '#f8fafc'
                });
                return;
            }

            const formData = new FormData(this);
            const url = this.action;

            // Deshabilitar botón para evitar doble envío
            if (btnSubmitComentario) {
                btnSubmitComentario.disabled = true;
                btnSubmitComentario.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enviando...';
            }

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Limpiar textarea e input de imagen
                        missatgeInput.value = '';
                        if (imatgeInput) {
                            imatgeInput.value = '';
                            if (fileNameDisplay) fileNameDisplay.textContent = '';
                        }

                        // Si existía el mensaje de "Sin comentarios", quitarlo
                        if (noCommentsMsg) noCommentsMsg.remove();

                        // Añadir el nuevo comentario al final
                        commentsContainer.insertAdjacentHTML('beforeend', data.html);

                        // Scroll suave al último comentario
                        const lastComment = commentsContainer.lastElementChild;
                        if (lastComment) {
                            lastComment.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }

                        // Notificación tipo toast
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 2000,
                            background: '#1e293b',
                            color: '#f8fafc'
                        });
                    } else {
                        let errorMsg = data.message;
                        if (data.errors) {
                            const firstError = Object.values(data.errors)[0];
                            if (Array.isArray(firstError)) {
                                errorMsg = firstError[0];
                            }
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al comentar',
                            text: errorMsg,
                            background: '#1e293b',
                            color: '#f8fafc'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error en fetch de comentarios:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo enviar el comentario.',
                        background: '#1e293b',
                        color: '#f8fafc'
                    });
                })
                .finally(() => {
                    if (btnSubmitComentario) {
                        btnSubmitComentario.disabled = false;
                        btnSubmitComentario.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Enviar';
                    }
                });
        });
    }

    // 2. Borrar Comentarios vía AJAX
    document.onclick = function (e) {
        const btnDelegado = e.target.closest('.btn-delete-comment-action');
        if (btnDelegado) {
            const id = btnDelegado.dataset.id;
            const btnDelete = document.getElementById(`btn-delete-comment-${id}`);
            const commentItem = btnDelete ? btnDelete.closest('.comment-item-wrapper') : null;
            if (!commentItem) return;

            Swal.fire({
                title: '¿Eliminar comentario?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#4b5563',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                background: '#1e293b',
                color: '#f8fafc'
            }).then((result) => {
                if (result.isConfirmed) {
                    btnDelete.classList.add('btn-loading');

                    fetch(`/admin/comentarios/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                // Animación de salida
                                commentItem.classList.add('fade-out-right');

                                setTimeout(() => {
                                    commentItem.remove();

                                    // Verificar si quedan comentarios
                                    const container = document.getElementById('comments-container');
                                    if (container && container.querySelectorAll('.comment-item-wrapper').length === 0) {
                                        const noCommentsHtml = `
                                        <div id="no-comments-msg" class="fade-in-up" style="margin-top: 0.5rem; color: var(--text-secondary);">
                                            Sin comentarios.
                                        </div>
                                    `;
                                        container.insertAdjacentHTML('beforeend', noCommentsHtml);
                                    }
                                }, 400); // Tiempo de la animación CSS
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: data.message,
                                    showConfirmButton: false,
                                    timer: 2000,
                                    background: '#1e293b',
                                    color: '#f8fafc'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message,
                                    background: '#1e293b',
                                    color: '#f8fafc'
                                });
                                btnDelete.classList.remove('btn-loading');
                            }
                        })
                        .catch(error => {
                            console.error('Error al borrar comentario:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo eliminar el comentario.',
                                background: '#1e293b',
                                color: '#f8fafc'
                            });
                            btnDelete.classList.remove('btn-loading');
                        });
                }
            });
        }
    };

    // 4. Manejo de clicks en imágenes de comentarios para mostrar en modal
    if (commentsContainer) {
        commentsContainer.addEventListener('click', function (e) {
            const img = e.target.closest('.comment-image-clickable');
            if (img) {
                const imgSrc = img.src;
                const modalImg = document.getElementById('modal-imagen-src');
                if (modalImg) {
                    modalImg.src = imgSrc;
                    const modalImagenComentario = new bootstrap.Modal(document.getElementById('modalImagenComentario'));
                    modalImagenComentario.show();
                }
            }
        });
    }

    // 5. Manejo de edición de comentarios
    const formEditarComentario = document.getElementById('form-editar-comentario');
    const editMissatgeInput = document.getElementById('edit-missatge-comentario');
    const editImatgeInput = document.getElementById('edit-imatge-comentario');
    const editFileNameDisplay = document.getElementById('edit-file-name-display');
    const incidenciaId = window.location.pathname.match(/\d+/)?.[0];
    let comentarioIdEnEdicion = null;

    // Mostrar nombre del archivo en modal de edición
    if (editImatgeInput && editFileNameDisplay) {
        editImatgeInput.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                editFileNameDisplay.textContent = this.files[0].name;
            } else {
                editFileNameDisplay.textContent = '';
            }
        });
    }

    // Capturar clicks en botones de editar comentario
    if (commentsContainer) {
        commentsContainer.addEventListener('click', function (e) {
            const btnEdit = e.target.closest('.btn-edit-comment-action');
            if (btnEdit) {
                const id = btnEdit.dataset.id;
                comentarioIdEnEdicion = id;

                // Cargar datos del comentario
                fetch(`/admin/comentarios/${id}/edit`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            editMissatgeInput.value = data.data.missatge || '';
                            editImatgeInput.value = '';
                            editFileNameDisplay.textContent = data.data.imatge_path ? 'Imagen actual: ' + data.data.imatge_path.split('/').pop() : '';

                            // Limpiar el atributo method del formulario de edición
                            formEditarComentario.action = `/admin/comentarios/${id}`;

                            const modalEditarComentario = new bootstrap.Modal(document.getElementById('modalEditarComentario'));
                            modalEditarComentario.show();
                        }
                    })
                    .catch(error => console.error('Error al cargar comentario:', error));
            }
        });
    }

    // Enviar formulario de edición
    if (formEditarComentario) {
        formEditarComentario.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!comentarioIdEnEdicion) return;

            const missatge = editMissatgeInput.value.trim();
            const hasNewImage = editImatgeInput && editImatgeInput.files && editImatgeInput.files.length > 0;
            const hasExistingImage = !!editFileNameDisplay.textContent.includes('Imagen actual');

            // Validación
            if (missatge.length < 1 && !hasNewImage && !hasExistingImage) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Contenido requerido',
                    text: 'Debes escribir un comentario o adjuntar una imagen.',
                    background: '#1e293b',
                    color: '#f8fafc'
                });
                return;
            }

            const btnSubmitEdit = document.getElementById('btn-submit-edit-comentario');
            const formData = new FormData(this);

            if (btnSubmitEdit) {
                btnSubmitEdit.disabled = true;
                btnSubmitEdit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';
            }

            fetch(`/admin/comentarios/${comentarioIdEnEdicion}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar el comentario en la vista
                        const commentWrapper = document.querySelector(`.comment-item-wrapper[data-comment-id="${comentarioIdEnEdicion}"]`);
                        if (commentWrapper && data.html) {
                            commentWrapper.outerHTML = data.html;
                        }

                        // Cerrar modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarComentario'));
                        if (modal) modal.hide();

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 2000,
                            background: '#1e293b',
                            color: '#f8fafc'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                            background: '#1e293b',
                            color: '#f8fafc'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error al actualizar comentario:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo actualizar el comentario.',
                        background: '#1e293b',
                        color: '#f8fafc'
                    });
                })
                .finally(() => {
                    if (btnSubmitEdit) {
                        btnSubmitEdit.disabled = false;
                        btnSubmitEdit.innerHTML = '<i class="fa-solid fa-save"></i> Guardar';
                    }
                });
        });
    }
})();
