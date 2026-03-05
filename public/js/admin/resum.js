
window.onload = function () {
    const selectorSede = document.getElementById('sede_id');
    const contenedorKpis = document.getElementById('resum-kpis');
    const contenedorTabla = document.getElementById('resum-table');

    if (!selectorSede || !contenedorKpis || !contenedorTabla) {
        return;
    }

    const urlDatosResum = selectorSede.dataset.resumUrl;

    function renderEstadoVacio(contenedorHtml) {
        contenedorHtml.innerHTML = `
            <div class="empty-state">
                <i class="fa-regular fa-circle-question"></i>
                <p>Selecciona una sede para ver el volumen de incidencias.</p>
            </div>
        `;
    }

    function renderCargando(contenedorHtml) {
        contenedorHtml.innerHTML = `
            <div class="empty-state">
                <i class="fa-solid fa-spinner"></i>
                <p>Cargando...</p>
            </div>
        `;
    }

    async function cargarDatosResum(idSede) {
        if (!idSede) {
            renderEstadoVacio(contenedorKpis);
            contenedorTabla.innerHTML = '';
            return;
        }

        if (!urlDatosResum) {
            contenedorKpis.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <p>No se ha podido cargar el resumen.</p>
                </div>
            `;
            contenedorTabla.innerHTML = '';
            return;
        }

        renderCargando(contenedorKpis);
        contenedorTabla.innerHTML = '';

        try {
            const urlPeticion = `${urlDatosResum}?sede_id=${encodeURIComponent(idSede)}`;
            const respuesta = await fetch(urlPeticion, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!respuesta.ok) {
                throw new Error('Respuesta no válida');
            }

            const datos = await respuesta.json();
            contenedorKpis.innerHTML = datos.htmlKpis || '';
            contenedorTabla.innerHTML = datos.htmlTabla || '';
        } catch (error) {
            contenedorKpis.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <p>No se ha podido cargar el resumen.</p>
                </div>
            `;
            contenedorTabla.innerHTML = '';
        }
    }

    selectorSede.addEventListener('change', () => {
        cargarDatosResum(selectorSede.value);
    });
};