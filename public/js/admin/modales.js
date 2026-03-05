// Abrir modal de creación si hay errores de validación (opcional, si se implementa en el futuro)
document.addEventListener('DOMContentLoaded', function () {
    if (window.modalIncidenciaOpen) {
        var modalCrear = new bootstrap.Modal(document.getElementById('modalCrearIncidencia'));
        modalCrear.show();
    }
});

// MODAL EDITAR INCIDENCIA (ADMIN)
function closeAllModals() {
    document.querySelectorAll('.modal.show').forEach(modalEl => {
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) {
            modalInstance.hide();
        }
    });
}

// Delegación de eventos para botones de edición (Admin/Gestor)
document.addEventListener('click', function (e) {
    const btnEdit = e.target.closest('.btn-editar-incidencia');
    if (btnEdit) {
        e.preventDefault();
        e.stopPropagation();
        
        closeAllModals();
        const id = btnEdit.dataset.id;
        fetch(`/admin/incidencias/${id}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
            .then(res => {
                if (!res.ok) throw new Error('Error al cargar');
                return res.text();
            })
            .then(html => {
                const modalContent =
                    document.getElementById('modal-editar-incidencia-content') ||
                    document.getElementById('modal-editar-content');

                if (!modalContent) {
                    throw new Error('No se encontró el contenedor del modal de edición');
                }

                modalContent.innerHTML = html;
                const modalEl = document.getElementById('modalEditarIncidencia');
                const modal = new bootstrap.Modal(modalEl);

                // Inicializar validación si la función existe
                if (typeof window.iniciarValidacionEditarIncidencia === 'function') {
                    window.iniciarValidacionEditarIncidencia();
                }

                modal.show();
            })
            .catch(error => {
                console.error(error);
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo cargar el formulario de edición.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    background: '#111111',
                    color: '#f8fafc'
                });
            });
    }
});
