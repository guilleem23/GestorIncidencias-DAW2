// Confirmación con SweetAlert2 para cerrar incidencias
document.addEventListener('DOMContentLoaded', function() {
    
    // Función para inicializar formularios de cierre (se usa después de actualizar vía AJAX)
    function initCloseForms() {
        const closeForms = document.querySelectorAll('.form-close-incidencia');
    
        closeForms.forEach(form => {
            if (form.dataset.listenerAdded) return;
            form.dataset.listenerAdded = 'true';
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
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
                        form.submit();
                    }
                });
            });
        });
    }
    
    // Inicializar al cargar
    initCloseForms();
    
    // ===== FILTROS CON AJAX =====
    const filtrosForm = document.getElementById('filtros-form');
    const incidenciasContainer = document.getElementById('incidencias-container');
    const loadingOverlay = document.getElementById('loading-overlay');
    
    if (filtrosForm && incidenciasContainer && loadingOverlay) {
        
        // Interceptar el submit del formulario
        filtrosForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            loadingOverlay.style.display = 'flex';
            
            const formData = new FormData(filtrosForm);
            const params = new URLSearchParams(formData);
            
            fetch(`${filtrosForm.action}?${params.toString()}`, {
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
                    
                    const newUrl = `${window.location.pathname}?${params.toString()}`;
                    window.history.pushState({}, '', newUrl);
                    
                    incidenciasContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Hubo un error al aplicar los filtros.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444',
                    background: '#111111',
                    color: '#f8fafc'
                });
            })
            .finally(() => {
                loadingOverlay.style.display = 'none';
            });
        });
        
        // Manejar botón Limpiar
        const btnClear = filtrosForm.querySelector('.btn-clear');
        if (btnClear) {
            btnClear.addEventListener('click', function(e) {
                e.preventDefault();
                filtrosForm.reset();
                loadingOverlay.style.display = 'flex';
                
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
                        window.history.pushState({}, '', filtrosForm.action);
                        incidenciasContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                })
                .catch(error => console.error('Error:', error))
                .finally(() => {
                    loadingOverlay.style.display = 'none';
                });
            });
        }
    }
});
