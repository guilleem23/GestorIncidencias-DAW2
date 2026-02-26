/**
 * Gestión de filtros AJAX para la tabla de incidencias (historial)
 */
document.addEventListener('DOMContentLoaded', function () {
    const filterBuscar = document.getElementById('filter-buscar');
    const filterEstat = document.getElementById('filter-estat');
    const filterPrioritat = document.getElementById('filter-prioritat');
    const filterTecnic = document.getElementById('filter-tecnic');
    const filterOrden = document.getElementById('filter-orden');
    const btnLimpiar = document.getElementById('btn-clear-filters');
    const tableContainer = document.getElementById('incidencias-table-wrapper');

    let timeout = null;

    /**
     * Realiza la petición AJAX al servidor
     */
    function fetchIncidencias(url = null) {
        const paramsObj = {};
        if (filterBuscar && filterBuscar.value.trim()) paramsObj.buscar = filterBuscar.value.trim();
        if (filterEstat && filterEstat.value) paramsObj.estat = filterEstat.value;
        if (filterPrioritat && filterPrioritat.value) paramsObj.prioritat = filterPrioritat.value;
        if (filterTecnic && filterTecnic.value) paramsObj.tecnic_id = filterTecnic.value;
        if (filterOrden && filterOrden.value) paramsObj.orden = filterOrden.value;

        const params = new URLSearchParams(paramsObj);
        // Base URL limpia (sin query string previo)
        const baseUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
        let finalUrl = url || `${baseUrl}?${params.toString()}`;

        // Para el fetch, añadimos siempre ajax=1 para forzar la respuesta parcial del controlador
        const fetchParams = new URLSearchParams(paramsObj);
        fetchParams.set('ajax', '1');

        let fetchUrl = finalUrl;
        if (url) {
            const tempUrl = new URL(url, window.location.origin);
            Object.keys(paramsObj).forEach(key => tempUrl.searchParams.set(key, paramsObj[key]));
            tempUrl.searchParams.set('ajax', '1');
            fetchUrl = tempUrl.toString();
        } else {
            fetchUrl = `${baseUrl}?${fetchParams.toString()}`;
        }

        // Actualizar URL en el navegador sin recargar (URL limpia sin ajax=1)
        if (!url) {
            history.pushState(null, '', `${baseUrl}?${params.toString()}`);
        } else {
            const historyUrl = new URL(url, window.location.origin);
            Object.keys(paramsObj).forEach(key => historyUrl.searchParams.set(key, paramsObj[key]));
            history.pushState(null, '', historyUrl.toString());
        }

        // Estado de carga
        if (tableContainer) tableContainer.style.opacity = '0.5';

        fetch(fetchUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
            .then(response => {
                if (!response.ok) throw new Error('Error en el servidor');
                return response.text();
            })
            .then(html => {
                if (tableContainer) {
                    tableContainer.innerHTML = html;
                    tableContainer.style.opacity = '1';
                    // Scroll suave hacia arriba si es paginación o filtros
                    if (url) {
                        tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            })
            .catch(error => {
                console.error('Error al filtrar incidencias:', error);
                if (tableContainer) tableContainer.style.opacity = '1';
            });
    }

    // Eventos (EventListener para mayor robustez)
    if (filterBuscar) {
        filterBuscar.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(fetchIncidencias, 100);
        });
        filterBuscar.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') e.preventDefault();
        });
    }

    [filterEstat, filterPrioritat, filterTecnic, filterOrden].filter(Boolean).forEach(filter => {
        filter.addEventListener('change', () => fetchIncidencias());
    });

    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function (e) {
            e.preventDefault();
            if (filterBuscar) filterBuscar.value = '';
            if (filterEstat) filterEstat.value = '';
            if (filterPrioritat) filterPrioritat.value = '';
            if (filterTecnic) filterTecnic.value = '';
            if (filterOrden) filterOrden.value = 'desc';
            fetchIncidencias();
        });
    }

    if (tableContainer) {
        tableContainer.addEventListener('click', function (e) {
            const link = e.target.closest('.pagination a');
            if (link) {
                e.preventDefault();
                fetchIncidencias(link.href);
            }
        });
    }

    // ----------------------------------------------------------------
    // Lógica de Categorías y Edición (Mantenida)
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
        };
    }
});
