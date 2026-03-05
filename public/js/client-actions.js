// Confirmación con SweetAlert2 para cerrar incidencias
document.addEventListener('DOMContentLoaded', function() {
    // Obtener todos los botones de cerrar incidencia
    const closeForms = document.querySelectorAll('.form-close-incidencia');
    
    closeForms.forEach(form => {
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

    // ========== ELIMINAR INCIDENCIA ==========
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-eliminar-incidencia-client')) {
            const btn = e.target.closest('.btn-eliminar-incidencia-client');
            const incidenciaId = btn.dataset.id;
            const isDetailPage = /^\/client\/incidencias\/\d+$/.test(window.location.pathname);
            const clientIndexUrl = '/client/mis-incidencias';
            
            Swal.fire({
                title: '¿Eliminar incidencia?',
                text: 'Esta acción no se puede deshacer. Se eliminarán todos los comentarios asociados.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-trash"></i> Eliminar',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                background: '#111111',
                color: '#f8fafc'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Realizar petición DELETE
                    fetch(`/client/incidencias/${incidenciaId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminada',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false,
                                background: '#111111',
                                color: '#f8fafc'
                            }).then(() => {
                                if (isDetailPage) {
                                    window.location.href = clientIndexUrl;
                                } else {
                                    window.location.reload();
                                }
                            });
                        } else {
                            throw new Error(data.error || 'Error al eliminar la incidencia');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'No se pudo eliminar la incidencia',
                            background: '#111111',
                            color: '#f8fafc'
                        });
                    });
                }
            });
        }
    });
});
