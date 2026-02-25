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
    let searchTimeout = null;

    function applyFilters() {
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

        // Recarga la página con los filtros (page=1 implícito al no incluirlo)
        window.location.href = '/gestor/incidencias?' + params.toString();
    }

    // Select filters: reload page on change
    if (filterEstat) filterEstat.addEventListener('change', applyFilters);
    if (filterPrioritat) filterPrioritat.addEventListener('change', applyFilters);
    if (filterTecnic) filterTecnic.addEventListener('change', applyFilters);
    if (filterOrden) filterOrden.addEventListener('change', applyFilters);

    // Search input: debounce 500ms then reload
    if (filterBuscar) {
        filterBuscar.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 500);
        });
    }

    // Clear all filters
    if (btnClear) {
        btnClear.addEventListener('click', function () {
            window.location.href = '/gestor/incidencias';
        });
    }

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
    // 3. SweetAlert de confirmación antes de enviar el formulario de Editar
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
