// Filtros con AJAX para técnicos
document.addEventListener('DOMContentLoaded', function() {
    
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
                        document.getElementById('stat-assignades').textContent = data.stats.assignades;
                        document.getElementById('stat-entreball').textContent = data.stats.enTreball;
                        document.getElementById('stat-resoltes').textContent = data.stats.resoltes;
                        document.getElementById('stat-tancades').textContent = data.stats.tancades;
                        document.getElementById('stat-total').textContent = data.count;
                    }
                    
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
                            document.getElementById('stat-assignades').textContent = data.stats.assignades;
                            document.getElementById('stat-entreball').textContent = data.stats.enTreball;
                            document.getElementById('stat-resoltes').textContent = data.stats.resoltes;
                            document.getElementById('stat-tancades').textContent = data.stats.tancades;
                            document.getElementById('stat-total').textContent = data.count;
                        }
                        
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
