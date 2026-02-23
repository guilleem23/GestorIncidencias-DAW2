// ========================================
// SWEET ALERTS - CRUD CATEGORÍAS
// Confirmaciones de eliminación y mensajes
// ========================================

// Confirmación para eliminar CATEGORÍA
document.querySelectorAll('.btn-eliminar-categoria').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        const form = this.closest('form');
        const nombre = this.dataset.nombre;
        const numSubcategorias = parseInt(this.dataset.subcategorias) || 0;

        let textoAdvertencia = `Se eliminará la categoría "${nombre}"`;
        if (numSubcategorias > 0) {
            textoAdvertencia += ` y sus ${numSubcategorias} subcategoría(s) asociada(s)`;
        }
        textoAdvertencia += '. ¡Esta acción no se puede deshacer!';

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

// Confirmación para eliminar SUBCATEGORÍA
document.querySelectorAll('.btn-eliminar-subcategoria').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        const form = this.closest('form');
        const nombre = this.dataset.nombre;

        Swal.fire({
            title: '¿Eliminar subcategoría?',
            text: `Se eliminará la subcategoría "${nombre}". ¡Esta acción no se puede deshacer!`,
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
