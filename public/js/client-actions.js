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
});
