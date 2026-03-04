/**
 * Gestor Incidencias - SweetAlerts y Filtros AJAX
 */
document.addEventListener('DOMContentLoaded', function () {

    // ----------------------------------------------------------------
    // 1. AJAX Filters para la tabla de incidencias (historial)
    // ----------------------------------------------------------------
    const filterBuscar = document.getElementById('filter-buscar');
    const filterEstat = document.getElementById('filter-estat');
    const filterPrioritat = document.getElementById('filter-prioritat');
    const filterTecnic = document.getElementById('filter-tecnic');
    const filterOrden = document.getElementById('filter-orden');
    const btnClear = document.getElementById('btn-clear-filters');
    const tableContainer = document.getElementById('incidencias-table-wrapper');
    let searchTimeout = null;

    function applyFilters(url = null) {
        const params = new URLSearchParams();

        if (filterBuscar && filterBuscar.value.trim()) {
            params.set('buscar', filterBuscar.value.trim());
        }
        if (filterEstat && filterEstat.value) {
            params.set('estat', filterEstat.value);
        }
        if (filterPrioritat && filterPrioritat.value) {
            params.set('prioritat', filterPrioritat.value);
        }
        if (filterTecnic && filterTecnic.value) {
            params.set('tecnic_id', filterTecnic.value);
        }
        if (filterOrden && filterOrden.value && filterOrden.value !== 'desc') {
            params.set('orden', filterOrden.value);
        }

        let finalUrl = url || '/gestor/incidencias?' + params.toString();

        if (url) {
            const tempUrl = new URL(url, window.location.origin);
            for (const [key, value] of params.entries()) {
                tempUrl.searchParams.set(key, value);
            }
            finalUrl = tempUrl.toString();
        }

        if (tableContainer) {
            tableContainer.style.opacity = '0.5';

            fetch(finalUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.text())
                .then(html => {
                    tableContainer.innerHTML = html;
                    tableContainer.style.opacity = '1';
                    if (url) {
                        tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                })
                .catch(error => {
                    console.error('Error al filtrar incidencias:', error);
                    tableContainer.style.opacity = '1';
                });
        } else {
            // Fallback just in case
            window.location.href = finalUrl;
        }
    }

    // Select filters: reload table on change
    if (filterEstat) filterEstat.addEventListener('change', () => applyFilters());
    if (filterPrioritat) filterPrioritat.addEventListener('change', () => applyFilters());
    if (filterTecnic) filterTecnic.addEventListener('change', () => applyFilters());
    if (filterOrden) filterOrden.addEventListener('change', () => applyFilters());

    // Search input: debounce 500ms then reload table
    if (filterBuscar) {
        filterBuscar.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => applyFilters(), 500);
        });
    }

    // Clear all filters
    if (btnClear) {
        btnClear.addEventListener('click', function () {
            if (filterBuscar) filterBuscar.value = '';
            if (filterEstat) filterEstat.value = '';
            if (filterPrioritat) filterPrioritat.value = '';
            if (filterTecnic) filterTecnic.value = '';
            if (filterOrden) filterOrden.value = 'desc';
            applyFilters();
        });
    }

    // Delegación de eventos para la paginación (AJAX)
    if (tableContainer) {
        tableContainer.addEventListener('click', function (e) {
            const link = e.target.closest('.pagination a');
            if (link) {
                e.preventDefault();
                applyFilters(link.href);
            }
        });
    }

    // ----------------------------------------------------------------
    // Toggle Mostrar/Ocultar incidencias cerradas
    // ----------------------------------------------------------------
    let showingClosed = false;
    const btnToggleClosed = document.getElementById('btn-toggle-closed');

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

    // Override applyFilters to restore show-closed state after AJAX reload
    applyFilters = function (url) {
        const params = new URLSearchParams();

        if (filterBuscar && filterBuscar.value.trim()) {
            params.set('buscar', filterBuscar.value.trim());
        }
        if (filterEstat && filterEstat.value) {
            params.set('estat', filterEstat.value);
        }
        if (filterPrioritat && filterPrioritat.value) {
            params.set('prioritat', filterPrioritat.value);
        }
        if (filterTecnic && filterTecnic.value) {
            params.set('tecnic_id', filterTecnic.value);
        }
        if (filterOrden && filterOrden.value && filterOrden.value !== 'desc') {
            params.set('orden', filterOrden.value);
        }

        let finalUrl = url || '/gestor/incidencias?' + params.toString();

        if (url) {
            const tempUrl = new URL(url, window.location.origin);
            for (const [key, value] of params.entries()) {
                tempUrl.searchParams.set(key, value);
            }
            finalUrl = tempUrl.toString();
        }

        if (tableContainer) {
            tableContainer.style.opacity = '0.5';

            fetch(finalUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.text())
                .then(html => {
                    tableContainer.innerHTML = html;
                    tableContainer.style.opacity = '1';
                    // Restore show-closed state after reload
                    if (showingClosed) {
                        tableContainer.classList.add('show-closed');
                    }
                    if (url) {
                        tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                })
                .catch(error => {
                    console.error('Error al filtrar incidencias:', error);
                    tableContainer.style.opacity = '1';
                });
        } else {
            window.location.href = finalUrl;
        }
    };

    // ----------------------------------------------------------------
    // 2. Manejo dinámico de las Subcategorías al cambiar de Categoría
    // ----------------------------------------------------------------
    const categoriaSelect = document.getElementById('categoria_id');
    const subcategoriaSelect = document.getElementById('subcategoria_id');

    if (categoriaSelect && subcategoriaSelect && typeof categoriasData !== 'undefined') {
        categoriaSelect.addEventListener('change', function () {
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
        });
    }

    // ----------------------------------------------------------------
    // 3. Auto-sync: Technician ↔ Status
    // ----------------------------------------------------------------
    const selectTecnicSync = document.getElementById('tecnic_id');
    const selectEstatSync = document.getElementById('estat');

    if (selectTecnicSync && selectEstatSync) {
        selectTecnicSync.addEventListener('change', function () {
            if (this.value !== "" && selectEstatSync.value === "Sense assignar") {
                selectEstatSync.value = "Assignada";
            } else if (this.value === "" && selectEstatSync.value === "Assignada") {
                selectEstatSync.value = "Sense assignar";
            }
        });
    }

    // ----------------------------------------------------------------
    // 4. SweetAlert de confirmación antes de enviar el formulario de Editar
    // ----------------------------------------------------------------
    const btnSubmitEdit = document.getElementById('btn-submit-edit');
    const formEditar = document.getElementById('form-editar-incidencia');

    if (btnSubmitEdit && formEditar) {
        btnSubmitEdit.addEventListener('click', function (e) {
            e.preventDefault();

            if (!formEditar.checkValidity()) {
                formEditar.reportValidity();
                return;
            }

            // Custom Validation: Technician vs Status
            const selectTecnic = document.getElementById('tecnic_id');
            const selectEstat = document.getElementById('estat');

            if (selectTecnic && selectEstat) {
                const isTecnicAssigned = selectTecnic.value !== "";
                const isStatusSenseAssignar = selectEstat.value === "Sense assignar";

                if (isTecnicAssigned && isStatusSenseAssignar) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Validación',
                        text: 'El estado no puede ser "Sin asignar" si hay un técnico asignado.',
                        background: '#1e293b',
                        color: '#f8fafc'
                    });
                    return;
                }

                if (!isTecnicAssigned && !isStatusSenseAssignar) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Validación',
                        text: 'Debe asignar un técnico si el estado no es "Sin asignar".',
                        background: '#1e293b',
                        color: '#f8fafc'
                    });
                    return;
                }
            }

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
        });
    }

});
