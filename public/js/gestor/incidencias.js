(function () {
    "use strict";

    // 1. Identificación de elementos DOM
    const filterBuscar = document.getElementById('filter-buscar');
    const filterEstat = document.getElementById('filter-estat');
    const filterPrioritat = document.getElementById('filter-prioritat');
    const filterTecnic = document.getElementById('filter-tecnic'); // Solo en Gestor
    const filterSede = document.getElementById('filter-sede'); // Solo en Admin
    const filterOrden = document.getElementById('filter-orden');
    const tableContainer = document.getElementById('incidencias-table-container');
    const btnClearFilters = document.getElementById('btn-clear-filters');
    const btnToggleClosed = document.getElementById('btn-toggle-closed');

    // Estado local para incidencias cerradas
    let showingClosed = false;

    // Función principal de peticiones AJAX
    window.fetchIncidencias = function (url) {
        if (!tableContainer) return;

        const params = new URLSearchParams();
        if (filterBuscar && filterBuscar.value.trim()) params.set('buscar', filterBuscar.value.trim());
        if (filterEstat && filterEstat.value) params.set('estat', filterEstat.value);
        if (filterPrioritat && filterPrioritat.value) params.set('prioritat', filterPrioritat.value);
        if (filterTecnic && filterTecnic.value) params.set('tecnic_id', filterTecnic.value);
        if (filterSede && filterSede.value) params.set('sede_id', filterSede.value);
        if (filterOrden && filterOrden.value && filterOrden.value !== 'desc') params.set('orden', filterOrden.value);

        // Marcador para que el controlador sepa que es AJAX
        params.set('ajax', '1');

        let finalUrl = url || window.location.pathname + '?' + params.toString();

        // Overlay de carga
        tableContainer.style.opacity = '0.5';

        fetch(finalUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(response => response.text())
            .then(html => {
                tableContainer.innerHTML = html;
                tableContainer.style.opacity = '1';

                // Re-aplicar estado de "mostrar cerradas" tras la carga AJAX
                if (showingClosed) {
                    tableContainer.classList.add('show-closed');
                } else {
                    tableContainer.classList.remove('show-closed');
                }

                // Scroll suave si es paginación
                if (url) {
                    tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            })
            .catch(error => {
                console.error('Error al filtrar incidencias:', error);
                tableContainer.style.opacity = '1';
            });
    }

    // Lógica para alternar incidencias cerradas
    function updateToggleButton() {
        if (!btnToggleClosed) return;
        if (showingClosed) {
            btnToggleClosed.innerHTML = '<i class="fa-solid fa-eye"></i> Ocultar cerradas';
            btnToggleClosed.classList.add('active');
            if (tableContainer) tableContainer.classList.add('show-closed');
        } else {
            btnToggleClosed.innerHTML = '<i class="fa-solid fa-eye-slash"></i> Mostrar cerradas';
            btnToggleClosed.classList.remove('active');
            if (tableContainer) tableContainer.classList.remove('show-closed');
        }
    }

    if (btnToggleClosed) {
        btnToggleClosed.addEventListener('click', function () {
            showingClosed = !showingClosed;
            updateToggleButton();
        });
    }

    // Debounce para el buscador
    let timeout = null;
    if (filterBuscar) {
        filterBuscar.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(() => fetchIncidencias(), 400);
        });
    }

    // Listeners para selects
    [filterEstat, filterPrioritat, filterTecnic, filterSede, filterOrden].forEach(el => {
        if (el) el.addEventListener('change', () => fetchIncidencias());
    });

    // Limpiar filtros
    if (btnClearFilters) {
        btnClearFilters.addEventListener('click', function () {
            if (filterBuscar) filterBuscar.value = '';
            if (filterEstat) filterEstat.value = '';
            if (filterPrioritat) filterPrioritat.value = '';
            if (filterTecnic) filterTecnic.value = '';
            if (filterSede) filterSede.value = '';
            if (filterOrden) filterOrden.value = 'desc';
            fetchIncidencias();
        });
    }

    // Paginación via AJAX (onclick delegation)
    if (tableContainer) {
        tableContainer.addEventListener('click', function (e) {
            const link = e.target.closest('.pagination a');
            if (link) {
                e.preventDefault();
                fetchIncidencias(link.href);
            }
        });
    }

    // 2. Manejo dinámico de las Subcategorías (Edición)
    const categoriaSelect = document.getElementById('categoria_id');
    const subcategoriaSelect = document.getElementById('subcategoria_id');

    if (categoriaSelect && subcategoriaSelect && typeof categoriasData !== 'undefined') {
        categoriaSelect.onchange = function () {
            const categoriaId = parseInt(this.value);
            const categoriaObj = categoriasData.find(c => c.id === categoriaId);
            subcategoriaSelect.innerHTML = '';
            if (categoriaObj && categoriaObj.subcategorias) {
                categoriaObj.subcategorias.forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub.id;
                    option.textContent = sub.nom;
                    subcategoriaSelect.appendChild(option);
                });
            }
        };
    }

    // 3. Confirmación y Validaciones al Editar Incidencia
    const btnSubmitEdit = document.getElementById('btn-submit-edit');
    const formEditar = document.getElementById('form-editar-incidencia');

    if (btnSubmitEdit && formEditar) {
        btnSubmitEdit.addEventListener('click', function (e) {
            e.preventDefault();

            // Validación HTML5 nativa primero
            if (!formEditar.checkValidity()) {
                formEditar.reportValidity();
                return;
            }

            // Validaciones de negocio (Técnico vs Estado)
            const selectTecnic = document.getElementById('tecnic_id');
            const selectEstat = document.getElementById('estat');

            if (selectTecnic && selectEstat) {
                const isTecnicAssigned = selectTecnic.value !== "";
                const isStatusSenseAssignar = selectEstat.value === "Sense assignar";

                if (isTecnicAssigned && isStatusSenseAssignar) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de Validación',
                            text: 'El estado no puede ser "Sin asignar" si hay un técnico asignado.',
                            background: '#1e293b',
                            color: '#f8fafc'
                        });
                    } else {
                        alert('El estado no puede ser "Sin asignar" si hay un técnico asignado.');
                    }
                    return;
                }

                if (!isTecnicAssigned && !isStatusSenseAssignar) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de Validación',
                            text: 'Debe asignar un técnico si el estado no es "Sin asignar".',
                            background: '#1e293b',
                            color: '#f8fafc'
                        });
                    } else {
                        alert('Debe asignar un técnico si el estado no es "Sin asignar".');
                    }
                    return;
                }
            }

            // Confirmación final con SweetAlert
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¿Guardar los cambios?',
                    text: "Los datos de la incidencia serán actualizados en el sistema.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'No, cancelar',
                    background: '#1e293b',
                    color: '#f8fafc'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Guardando...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); },
                            background: '#1e293b',
                            color: '#f8fafc'
                        });
                        formEditar.submit();
                    }
                });
            } else {
                formEditar.submit();
            }
        });
    }
    // 4. Manejo de Comentarios vía AJAX
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

                        // Añadir el nuevo comentario (se asume que el partial se inyecta al final)
                        // Usamos insertAdjacentHTML para que sea más natural
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
                    Swal.fire('Error', 'No se pudo enviar el comentario.', 'error');
                })
                .finally(() => {
                    if (btnSubmitComentario) {
                        btnSubmitComentario.disabled = false;
                        btnSubmitComentario.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Enviar';
                    }
                });
        });
    }

    // 5. Borrar Comentarios vía AJAX
    document.onclick = function (e) {
        // Usamos una clase para detectar el clic y luego getElementById si necesitamos buscar algo específico por ID
        const btnDelegado = e.target.closest('.btn-delete-comment-action');
        if (btnDelegado) {
            const id = btnDelegado.dataset.id;
            // Para encontrar el botón exacto por ID usamos getElementById como pide el usuario
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

                    fetch(`/gestor/comentarios/${id}`, {
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
                            Swal.fire('Error', 'No se pudo eliminar el comentario.', 'error');
                            btnDelete.classList.remove('btn-loading');
                        });
                }
            });
        }
    };
})();
