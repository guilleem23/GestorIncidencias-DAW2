// Manejo de incidencias para cliente
document.addEventListener('DOMContentLoaded', function () {

    // ========== FILTROS (FETCH AJAX) ==========
    const filterEstat = document.getElementById('estat');
    const filterOrden = document.getElementById('orden');
    const btnToggleClosed = document.getElementById('btn-toggle-closed');
    const btnClearFilters = document.getElementById('btn-clear-filters');
    const incidenciasListContainer = document.getElementById('incidencias-list-container');
    let ocultarResoltes = btnToggleClosed ? btnToggleClosed.classList.contains('active') : false;

    function updateToggleClosedButton() {
        if (!btnToggleClosed) return;

        const icon = btnToggleClosed.querySelector('i');
        if (ocultarResoltes) {
            btnToggleClosed.classList.add('active');
            if (icon) icon.className = 'fa-solid fa-eye';
            btnToggleClosed.innerHTML = `${icon ? icon.outerHTML : '<i class="fa-solid fa-eye"></i>'} Mostrar resueltas/cerradas`;
        } else {
            btnToggleClosed.classList.remove('active');
            if (icon) icon.className = 'fa-solid fa-eye-slash';
            btnToggleClosed.innerHTML = `${icon ? icon.outerHTML : '<i class="fa-solid fa-eye-slash"></i>'} Ocultar resueltas/cerradas`;
        }
    }

    function fetchIncidencias(url = null) {
        if (!incidenciasListContainer) return;

        const params = new URLSearchParams();
        if (filterEstat && filterEstat.value) params.set('estat', filterEstat.value);
        if (filterOrden && filterOrden.value && filterOrden.value !== 'desc') params.set('orden', filterOrden.value);
        if (ocultarResoltes) params.set('ocultar_resoltes', '1');
        params.set('ajax', '1');

        const finalUrl = url || `/client/mis-incidencias?${params.toString()}`;

        incidenciasListContainer.style.opacity = '0.55';

        fetch(finalUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.text())
            .then(html => {
                incidenciasListContainer.innerHTML = html;
                incidenciasListContainer.style.opacity = '1';
            })
            .catch(() => {
                incidenciasListContainer.style.opacity = '1';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron aplicar los filtros.',
                    background: '#111111',
                    color: '#f8fafc'
                });
            });
    }

    if (filterEstat) {
        filterEstat.addEventListener('change', () => fetchIncidencias());
    }

    if (filterOrden) {
        filterOrden.addEventListener('change', () => fetchIncidencias());
    }

    if (btnToggleClosed) {
        btnToggleClosed.addEventListener('click', function () {
            ocultarResoltes = !ocultarResoltes;
            updateToggleClosedButton();
            fetchIncidencias();
        });
    }

    if (btnClearFilters) {
        btnClearFilters.addEventListener('click', function () {
            if (filterEstat) filterEstat.value = '';
            if (filterOrden) filterOrden.value = 'desc';
            ocultarResoltes = false;
            updateToggleClosedButton();
            fetchIncidencias();
        });
    }

    updateToggleClosedButton();

    // ========== EDITAR INCIDENCIA ==========
    document.addEventListener('click', function (e) {
        if (e.target.closest('.btn-editar-incidencia-client')) {
            const btn = e.target.closest('.btn-editar-incidencia-client');
            const incidenciaId = btn.dataset.id;

            // Obtener datos de la incidencia
            fetch(`/client/incidencias/${incidenciaId}/editar`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.incidencia) {
                        const incidencia = data.incidencia;

                        // Rellenar formulario
                        document.getElementById('edit-titol-client').value = incidencia.titol || '';
                        document.getElementById('edit-descripcio-client').value = incidencia.descripcio || '';

                        // Cargar categorías
                        const categoriaSelect = document.getElementById('edit-categoria-client');
                        categoriaSelect.innerHTML = '<option value="">Selecciona una categoría...</option>';

                        if (window.categoriasData) {
                            window.categoriasData.forEach(categoria => {
                                const option = document.createElement('option');
                                option.value = categoria.id;
                                option.textContent = categoria.nom;
                                option.dataset.subcategorias = JSON.stringify(categoria.subcategorias || []);
                                if (categoria.id == incidencia.categoria_id) {
                                    option.selected = true;
                                }
                                categoriaSelect.appendChild(option);
                            });

                            // Trigger change para cargar subcategorías
                            if (incidencia.categoria_id) {
                                const event = new Event('change');
                                categoriaSelect.dispatchEvent(event);

                                // Después de cargar subcategorías, seleccionar la correcta
                                setTimeout(() => {
                                    document.getElementById('edit-subcategoria-client').value = incidencia.subcategoria_id || '';
                                    validarFormularioIncidencia();
                                }, 50);
                            }
                        }

                        // Configurar formulario
                        const form = document.getElementById('form-editar-incidencia-client');
                        form.action = `/client/incidencias/${incidenciaId}`;

                        // Resetear validaciones
                        resetearValidaciones();

                        // Mostrar modal
                        const modal = new bootstrap.Modal(document.getElementById('modalEditarIncidencia'));
                        modal.show();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar la incidencia',
                        background: '#111111',
                        color: '#f8fafc'
                    });
                });
        }
    });

    // ========== VALIDACIÓN FORMULARIO EDITAR INCIDENCIA ==========
    const titolInput = document.getElementById('edit-titol-client');
    const descripcioInput = document.getElementById('edit-descripcio-client');
    const categoriaSelect = document.getElementById('edit-categoria-client');
    const subcategoriaSelect = document.getElementById('edit-subcategoria-client');
    const submitBtn = document.getElementById('btn-submit-edit-incidencia-client');

    if (!titolInput || !descripcioInput || !categoriaSelect || !subcategoriaSelect || !submitBtn) {
        return;
    }

    function mostrarError(element, errorElementId, mensaje) {
        const errorElement = document.getElementById(errorElementId);
        if (errorElement) {
            errorElement.textContent = mensaje;
            errorElement.style.display = 'block';
            element.classList.add('border-danger');
        }
    }

    function ocultarError(element, errorElementId) {
        const errorElement = document.getElementById(errorElementId);
        if (errorElement) {
            errorElement.style.display = 'none';
            element.classList.remove('border-danger');
        }
    }

    function validarTitol() {
        const valor = titolInput.value.trim();

        if (!valor) {
            mostrarError(titolInput, 'error-edit-titol-client', 'El título es obligatorio');
            return false;
        }

        if (valor.length < 3) {
            mostrarError(titolInput, 'error-edit-titol-client', 'El título debe tener al menos 3 caracteres');
            return false;
        }

        if (valor.length > 255) {
            mostrarError(titolInput, 'error-edit-titol-client', 'El título no puede superar 255 caracteres');
            return false;
        }

        ocultarError(titolInput, 'error-edit-titol-client');
        return true;
    }

    function validarDescripcio() {
        const valor = descripcioInput.value.trim();

        if (!valor) {
            mostrarError(descripcioInput, 'error-edit-descripcio-client', 'La descripción es obligatoria');
            return false;
        }

        if (valor.length < 10) {
            mostrarError(descripcioInput, 'error-edit-descripcio-client', 'La descripción debe tener al menos 10 caracteres');
            return false;
        }

        if (valor.length > 1000) {
            mostrarError(descripcioInput, 'error-edit-descripcio-client', 'La descripción no puede superar 1000 caracteres');
            return false;
        }

        ocultarError(descripcioInput, 'error-edit-descripcio-client');
        return true;
    }

    function validarCategoria() {
        if (!categoriaSelect.value) {
            mostrarError(categoriaSelect, 'error-edit-categoria-client', 'Debes seleccionar una categoría');
            return false;
        }

        ocultarError(categoriaSelect, 'error-edit-categoria-client');
        return true;
    }

    function validarSubcategoria() {
        if (!subcategoriaSelect.value) {
            mostrarError(subcategoriaSelect, 'error-edit-subcategoria-client', 'Debes seleccionar una subcategoría');
            return false;
        }

        ocultarError(subcategoriaSelect, 'error-edit-subcategoria-client');
        return true;
    }

    function validarFormularioIncidencia() {
        const titolValido = validarTitol();
        const descripcioValida = validarDescripcio();
        const categoriaValida = validarCategoria();
        const subcategoriaValida = validarSubcategoria();

        const formularioValido = titolValido && descripcioValida && categoriaValida && subcategoriaValida;

        submitBtn.disabled = !formularioValido;
        submitBtn.style.opacity = formularioValido ? '1' : '0.6';
        submitBtn.style.cursor = formularioValido ? 'pointer' : 'not-allowed';

        return formularioValido;
    }

    function resetearValidaciones() {
        ocultarError(titolInput, 'error-edit-titol-client');
        ocultarError(descripcioInput, 'error-edit-descripcio-client');
        ocultarError(categoriaSelect, 'error-edit-categoria-client');
        ocultarError(subcategoriaSelect, 'error-edit-subcategoria-client');
    }

    // Event listeners de validación
    titolInput.addEventListener('input', validarFormularioIncidencia);
    titolInput.addEventListener('blur', validarTitol);

    descripcioInput.addEventListener('input', validarFormularioIncidencia);
    descripcioInput.addEventListener('blur', validarDescripcio);

    categoriaSelect.addEventListener('change', function () {
        validarCategoria();

        // Cargar subcategorías
        const selectedOption = this.options[this.selectedIndex];
        const subcategorias = selectedOption ? JSON.parse(selectedOption.dataset.subcategorias || '[]') : [];

        subcategoriaSelect.innerHTML = '<option value="">Selecciona una subcategoría...</option>';

        subcategorias.forEach(sub => {
            const option = document.createElement('option');
            option.value = sub.id;
            option.textContent = sub.nom;
            subcategoriaSelect.appendChild(option);
        });

        subcategoriaSelect.disabled = subcategorias.length === 0;
        validarFormularioIncidencia();
    });

    subcategoriaSelect.addEventListener('change', validarFormularioIncidencia);

    // ========== ENVIAR FORMULARIO EDITAR INCIDENCIA ==========
    const formEditarIncidencia = document.getElementById('form-editar-incidencia-client');
    if (formEditarIncidencia) {
        formEditarIncidencia.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!validarFormularioIncidencia()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    text: 'Por favor, corrige los errores antes de guardar.',
                    background: '#111111',
                    color: '#f8fafc'
                });
                return;
            }

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
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarIncidencia'));
                        modal.hide();

                        // Mostrar mensaje con reload
                        Swal.fire({
                            icon: 'success',
                            title: 'Incidencia actualizada',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false,
                            background: '#111111',
                            color: '#f8fafc'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.error || 'Error al actualizar la incidencia');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'No se pudo actualizar la incidencia',
                        background: '#111111',
                        color: '#f8fafc'
                    });
                });
        });
    }

    // ========== ACTUALIZAR FORMULARIO AL CAMBIAR CATEGORÍA (NUEVA INCIDENCIA) ==========
    const newCategoriaSelect = document.getElementById('categoria_id');
    const newSubcategoriaSelect = document.getElementById('subcategoria_id');

    if (newCategoriaSelect && newSubcategoriaSelect) {
        newCategoriaSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const subcategorias = selectedOption ? JSON.parse(selectedOption.dataset.subcategorias || '[]') : [];

            newSubcategoriaSelect.innerHTML = '<option value="">Selecciona una subcategoría...</option>';
            subcategorias.forEach(sub => {
                const option = document.createElement('option');
                option.value = sub.id;
                option.textContent = sub.nom;
                newSubcategoriaSelect.appendChild(option);
            });

            newSubcategoriaSelect.disabled = subcategorias.length === 0;
        });
    }

    // ========== ELIMINAR INCIDENCIA (FETCH AJAX) ==========
    document.addEventListener('click', function (e) {
        const btnEliminar = e.target.closest('.btn-eliminar-incidencia-client');
        if (btnEliminar) {
            const incidenciaId = btnEliminar.dataset.id;
            const card = btnEliminar.closest('.incidencia-card');

            Swal.fire({
                title: '¿Eliminar incidencia?',
                text: 'Esta acción no se puede deshacer y eliminará todos los comentarios asociados.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fa-solid fa-trash"></i> Sí, eliminar',
                cancelButtonText: '<i class="fa-solid fa-times"></i> Cancelar',
                background: '#111111',
                color: '#f8fafc'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/client/incidencias/${incidenciaId}`, {
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
                                    title: 'Eliminada',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                    background: '#111111',
                                    color: '#f8fafc'
                                });

                                // Animación de salida y eliminación del DOM
                                if (card) {
                                    card.classList.add('fade-out-left');
                                    setTimeout(() => {
                                        card.remove();

                                        // Si no quedan tarjetas, mostrar mensaje de vacío
                                        const container = document.getElementById('incidencias-list-container');
                                        if (container && container.querySelectorAll('.incidencia-card').length === 0) {
                                            container.innerHTML = `
                                            <div class="empty-state fade-in-up">
                                                <i class="fas fa-inbox"></i>
                                                <p>No tienes incidencias registradas.</p>
                                            </div>
                                        `;
                                        }
                                    }, 400);
                                } else {
                                    // Fallback si no hay card (ej: en vista detalle)
                                    window.location.href = '/client/mis-incidencias';
                                }
                            } else {
                                throw new Error(data.error || 'Error al eliminar la incidencia');
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message || 'No se pudo eliminar la incidencia',
                                background: '#111111',
                                color: '#f8fafc'
                            });
                        });
                }
            });
        }
    });
});
