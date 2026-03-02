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
    // Lógica adicional (Categorías / Edición) - Si aplica
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
    }
});
