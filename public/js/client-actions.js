// Confirmación con SweetAlert2 para cerrar incidencias
document.addEventListener('DOMContentLoaded', function() {
    // ===================================================
    // FUNCIONALIDAD: Cerrar incidencias con confirmación
    // ===================================================
    
    // Función para inicializar los formularios de cierre (necesaria para recargar después de AJAX)
    function initCloseForms() {
        const closeForms = document.querySelectorAll('.form-close-incidencia');
        
        closeForms.forEach(form => {
            // Evitar agregar múltiples listeners
            if (form.dataset.listenerAdded) return;
            form.dataset.listenerAdded = 'true';
            
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevenir envío inmediato
                
                Swal.fire({
                    title: 'Tancar incidència?',
                    text: 'Confirmes que vols tancar aquesta incidència?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#22c55e',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-check"></i> Sí, tancar-la',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancel·lar',
                    background: '#111111',
                    color: '#f8fafc',
                    customClass: {
                        popup: 'swal-dark-popup',
                        confirmButton: 'swal-confirm-btn',
                        cancelButton: 'swal-cancel-btn'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Si confirma, enviar el formulario
                        form.submit();
                    }
                });
            });
        });
    }
    
    // Inicializar al cargar la página
    initCloseForms();

    // ===================================================
    // FUNCIONALIDAD: Filtros con AJAX
    // ===================================================
    
    const filtrosForm = document.getElementById('filtros-form');
    const incidenciasContainer = document.getElementById('incidencias-container');
    const loadingOverlay = document.getElementById('loading-overlay');
    
    if (filtrosForm && incidenciasContainer && loadingOverlay) {
        
        // Interceptar el envío del formulario
        filtrosForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevenir recarga de página
            
            // Mostrar loader
            loadingOverlay.style.display = 'flex';
            
            // Obtener los datos del formulario
            const formData = new FormData(filtrosForm);
            const params = new URLSearchParams(formData);
            
            // Hacer la petición AJAX
            fetch(`${filtrosForm.action}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Actualizar el contenedor de incidencias con el nuevo HTML
                    incidenciasContainer.innerHTML = data.html;
                    
                    // Actualizar las estadísticas
                    if (data.stats) {
                        document.getElementById('stat-senseassignar').textContent = data.stats.senseAssignar;
                        document.getElementById('stat-enproces').textContent = data.stats.enProces;
                        document.getElementById('stat-resoltes').textContent = data.stats.resoltes;
                        document.getElementById('stat-tancades').textContent = data.stats.tancades;
                    }
                    
                    // Reinicializar los formularios de cierre
                    initCloseForms();
                    
                    // Actualizar la URL sin recargar la página (para que los filtros se mantengan al recargar)
                    const newUrl = `${window.location.pathname}?${params.toString()}`;
                    window.history.pushState({}, '', newUrl);
                    
                    // Scroll suave al contenedor de incidencias
                    incidenciasContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            })
            .catch(error => {
                console.error('Error al filtrar:', error);
                
                // Mostrar mensaje de error
                Swal.fire({
                    title: 'Error',
                    text: 'Hubo un error al aplicar los filtros. Por favor, intenta de nuevo.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Aceptar',
                    background: '#111111',
                    color: '#f8fafc'
                });
            })
            .finally(() => {
                // Ocultar loader
                loadingOverlay.style.display = 'none';
            });
        });
        
        // También aplicar filtros cuando cambien los selects (opcional, más interactivo)
        const filterSelects = filtrosForm.querySelectorAll('.filter-select');
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                // Auto-submit al cambiar (opcional, comentar si no se desea)
                // filtrosForm.dispatchEvent(new Event('submit'));
            });
        });
        
        // Manejar el botón "Limpiar" también con AJAX
        const btnClear = filtrosForm.querySelector('.btn-clear');
        if (btnClear) {
            btnClear.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Limpiar todos los campos del formulario
                filtrosForm.reset();
                
                // Mostrar loader
                loadingOverlay.style.display = 'flex';
                
                // Hacer petición sin parámetros
                fetch(filtrosForm.action, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        incidenciasContainer.innerHTML = data.html;
                        
                        if (data.stats) {
                            document.getElementById('stat-senseassignar').textContent = data.stats.senseAssignar;
                            document.getElementById('stat-enproces').textContent = data.stats.enProces;
                            document.getElementById('stat-resoltes').textContent = data.stats.resoltes;
                            document.getElementById('stat-tancades').textContent = data.stats.tancades;
                        }
                        
                        initCloseForms();
                        
                        // Actualizar URL a la ruta base
                        window.history.pushState({}, '', filtrosForm.action);
                        
                        incidenciasContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                })
                .catch(error => {
                    console.error('Error al limpiar filtros:', error);
                })
                .finally(() => {
                    loadingOverlay.style.display = 'none';
                });
            });
        }
    }
});
