/**
 * Gestión de filtros AJAX para la tabla de usuarios
 */
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-input');
    const rolFilter = document.getElementById('rol-filter');
    const sedeFilter = document.getElementById('sede-filter');
    const activoFilter = document.getElementById('activo-filter');
    const perPageFilter = document.getElementById('per-page-filter');
    const tableContainer = document.getElementById('usuarios-table-container');

    let timeout = null;

    /**
     * Realiza la petición AJAX al servidor
     */
    function fetchUsuarios(url = null) {
        const searchVal = searchInput.value.trim();
        const rolVal = rolFilter.value;
        const sedeVal = sedeFilter.value;
        const activoVal = activoFilter.value;
        const perPageVal = perPageFilter ? perPageFilter.value : 10;

        const paramsObj = {};
        if (searchVal) paramsObj.search = searchVal;
        if (rolVal) paramsObj.rol = rolVal;
        if (sedeVal) paramsObj.sede = sedeVal;
        if (activoVal !== '') paramsObj.activo = activoVal;
        if (perPageVal) paramsObj.per_page = perPageVal;

        const params = new URLSearchParams(paramsObj);
        let finalUrl = url || `${window.location.pathname}?${params.toString()}`;

        // Si usamos una URL de paginación preexistente, le inyectamos los filtros actuales
        if (url) {
            const tempUrl = new URL(url, window.location.origin);
            Object.keys(paramsObj).forEach(key => {
                tempUrl.searchParams.set(key, paramsObj[key]);
            });
            finalUrl = tempUrl.toString();
        }

        // Mostrar un estado de carga
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
                // Scroll suave hacia arriba si es paginación
                if (url) {
                    tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            })
            .catch(error => {
                console.error('Error al filtrar usuarios:', error);
                tableContainer.style.opacity = '1';
            });
    }

    /**
     * Escuchadores de eventos
     */

    // Búsqueda con debounce (300ms)
    searchInput.addEventListener('input', function () {
        clearTimeout(timeout);
        timeout = setTimeout(() => fetchUsuarios(), 300);
    });

    // Selectores (cambio instantáneo)
    [rolFilter, sedeFilter, activoFilter, perPageFilter].filter(Boolean).forEach(filter => {
        filter.addEventListener('change', () => fetchUsuarios());
    });

    // Botón de limpiar filtros
    const btnLimpiar = document.getElementById('btn-limpiar-filtros');
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function () {
            searchInput.value = '';
            rolFilter.value = '';
            sedeFilter.value = '';
            activoFilter.value = '';
            // Resetear a 5 por página por defecto
            if (perPageFilter) perPageFilter.value = '5';

            fetchUsuarios();
        });
    }

    // Delegación de eventos para la paginación
    tableContainer.addEventListener('click', function (e) {
        const link = e.target.closest('.pagination a');
        if (link) {
            e.preventDefault();
            fetchUsuarios(link.href);
        }
    });
});
