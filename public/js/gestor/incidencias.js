/**
 * Gestión de filtros AJAX para la tabla de incidencias (Admin y Gestor)
 * Siguiendo el patrón de admin/usuarios/filtros.js
 */
document.addEventListener('DOMContentLoaded', function () {
    const filterBuscar = document.getElementById('filter-buscar');
    const filterEstat = document.getElementById('filter-estat');
    const filterPrioritat = document.getElementById('filter-prioritat');
    const filterTecnic = document.getElementById('filter-tecnic');
    const filterSede = document.getElementById('filter-sede');
    const filterOrden = document.getElementById('filter-orden');
    const btnLimpiar = document.getElementById('btn-clear-filters');

    // Contenedor dinámico según el panel
    const tableContainer = document.getElementById('incidencias-table-container') || document.getElementById('incidencias-table-wrapper');

    let timeout = null;

    /**
     * Realiza la petición AJAX al servidor
     */
    function fetchIncidencias(url = null) {
        if (!tableContainer) return;

        const buscarVal = filterBuscar ? filterBuscar.value.trim() : '';
        const estatVal = filterEstat ? filterEstat.value : '';
        const prioritatVal = filterPrioritat ? filterPrioritat.value : '';
        const tecnicVal = filterTecnic ? filterTecnic.value : '';
        const sedeVal = filterSede ? filterSede.value : '';
        const ordenVal = filterOrden ? filterOrden.value : 'desc';

        const paramsObj = {};
        if (buscarVal) paramsObj.buscar = buscarVal;
        if (estatVal) paramsObj.estat = estatVal;
        if (prioritatVal) paramsObj.prioritat = prioritatVal;
        if (tecnicVal) paramsObj.tecnic_id = tecnicVal;
        if (sedeVal) paramsObj.sede_id = sedeVal;
        if (ordenVal && ordenVal !== 'desc') paramsObj.orden = ordenVal;

        // El controlador espera 'ajax' para devolver el partial
        paramsObj.ajax = '1';

        const params = new URLSearchParams(paramsObj);
        let finalUrl = url || `${window.location.pathname}?${params.toString()}`;

        // Inyectar filtros actuales en la URL de paginación
        if (url) {
            const tempUrl = new URL(url, window.location.origin);
            Object.keys(paramsObj).forEach(key => {
                tempUrl.searchParams.set(key, paramsObj[key]);
            });
            finalUrl = tempUrl.toString();
        }

        // Estado de carga
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

    // Buscador (oninput con debounce de 100ms como en usuarios)
    if (filterBuscar) {
        filterBuscar.oninput = function () {
            clearTimeout(timeout);
            timeout = setTimeout(fetchIncidencias, 100);
        };
        // Prevenir Enter
        filterBuscar.onkeydown = function (e) {
            if (e.key === 'Enter') e.preventDefault();
        };
    }

    // Selectores (onchange instantáneo)
    [filterEstat, filterPrioritat, filterTecnic, filterSede, filterOrden].filter(Boolean).forEach(filter => {
        filter.onchange = () => fetchIncidencias();
    });

    // Botón Limpiar (onclick)
    if (btnLimpiar) {
        btnLimpiar.onclick = function () {
            if (filterBuscar) filterBuscar.value = '';
            if (filterEstat) filterEstat.value = '';
            if (filterPrioritat) filterPrioritat.value = '';
            if (filterTecnic) filterTecnic.value = '';
            if (filterSede) filterSede.value = '';
            if (filterOrden) filterOrden.value = 'desc';

            fetchIncidencias();
        };
    }

    // Delegación de eventos para la paginación (onclick)
    if (tableContainer) {
        tableContainer.onclick = function (e) {
            const link = e.target.closest('.pagination a');
            if (link) {
                e.preventDefault();
                fetchIncidencias(link.href);
            }
        };
    }

    // ----------------------------------------------------------------
<<<<<<< HEAD
    // Lógica adicional (Categorías / Edición) - Si aplica
=======
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
>>>>>>> a87193e6da0f44f2221609adf8ecf6112a692e92
    // ----------------------------------------------------------------
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

    const btnSubmitEdit = document.getElementById('btn-submit-edit');
    const formEditar = document.getElementById('form-editar-incidencia');
    if (btnSubmitEdit && formEditar) {
        btnSubmitEdit.onclick = function (e) {
            e.preventDefault();
            if (!formEditar.checkValidity()) {
                formEditar.reportValidity();
                return;
            }
<<<<<<< HEAD
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
        };
=======

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
>>>>>>> a87193e6da0f44f2221609adf8ecf6112a692e92
    }
});
