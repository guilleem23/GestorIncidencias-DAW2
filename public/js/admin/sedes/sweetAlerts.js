// ========================================
// SWEET ALERTS - CRUD SEDES
// Confirmaciones de eliminación y mensajes
// ========================================

// Confirmación para eliminar SEDE
document.querySelectorAll('.btn-eliminar-sede').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        const form = this.closest('form');
        const nombre = this.dataset.nombre;

        let textoAdvertencia = `Se eliminará la sede "${nombre}". ¡Esta acción no se puede deshacer!`;

        Swal.fire({
            title: '¿Estás seguro?',
            text: textoAdvertencia,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fa-solid fa-trash"></i> Sí, eliminar',
            cancelButtonText: 'Cancelar',
            background: '#1e1e1e',
            color: '#f3f4f6',
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

// Mostrar descripción completa de la sede
document.querySelectorAll('.btn-info-desc').forEach(btn => {
    btn.addEventListener('click', function () {
        const nombre = this.dataset.nombre;
        const descripcion = this.dataset.descripcion;

        Swal.fire({
            title: nombre,
            text: descripcion,
            icon: 'info',
            confirmButtonColor: '#3b82f6',
            confirmButtonText: 'Cerrar',
            background: '#1e1e1e',
            color: '#f3f4f6',
            customClass: {
                popup: 'swal-dark-popup'
            }
        });
    });
});
