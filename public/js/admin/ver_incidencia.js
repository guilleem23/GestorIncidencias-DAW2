(function () {
    "use strict";

    // 1. Manejo de Comentarios vía AJAX
    const formComentario = document.getElementById('form-comentario');
    const commentsContainer = document.getElementById('comments-container');
    const missatgeInput = document.getElementById('missatge-comentario');
    const btnSubmitComentario = document.getElementById('btn-submit-comentario');
    const noCommentsMsg = document.getElementById('no-comments-msg');

    if (formComentario && commentsContainer) {
        formComentario.addEventListener('submit', function (e) {
            e.preventDefault();

            const missatge = missatgeInput.value.trim();
            if (missatge.length < 2) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Comentario muy corto',
                    text: 'El comentario debe tener al menos 2 caracteres.',
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
                        // Limpiar textarea
                        missatgeInput.value = '';

                        // Si existía el mensaje de "Sin comentarios", quitarlo
                        if (noCommentsMsg) noCommentsMsg.remove();

                        // Añadir el nuevo comentario al final
                        commentsContainer.insertAdjacentHTML('beforeend', data.html);

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
                        if (data.errors && data.errors.missatge) {
                            errorMsg = data.errors.missatge[0];
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
                                // Animación de salida vía CSS
                                commentItem.classList.add('fade-out-right');

                                setTimeout(() => {
                                    commentItem.remove();

                                    // Si ya no hay comentarios, mostrar el mensaje de "Sin comentarios"
                                    if (commentsContainer && commentsContainer.getElementsByClassName('comment-item-wrapper').length === 0) {
                                        commentsContainer.innerHTML = `
                                        <div id="no-comments-msg" class="fade-in-up" style="margin-top: 0.5rem; color: var(--text-secondary);">
                                            Sin comentarios.
                                        </div>
                                    `;
                                    }
                                }, 300);

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
})();
