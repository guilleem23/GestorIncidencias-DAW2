// Abrir modal de creación si hay errores de validación
document.addEventListener('DOMContentLoaded', function () {
    if (window.modalSedeOpen) {
        var modalCrear = new bootstrap.Modal(document.getElementById('modalCrearSede'));
        modalCrear.show();
    }
});

// Cerrar todos los modales abiertos
function closeAllModals() {
    document.querySelectorAll('.modal.show').forEach(modalEl => {
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) {
            modalInstance.hide();
        }
    });
}

// Delegación de eventos para botones de edición
document.addEventListener('click', function (e) {
    const btnEdit = e.target.closest('.btn-editar-sede');
    if (btnEdit) {
        e.preventDefault();
        e.stopPropagation();
        
        closeAllModals();
        const id = btnEdit.dataset.id;
        fetch(`/admin/sedes/${id}/edit`)
            .then(res => {
                if (!res.ok) throw new Error('Error al cargar');
                return res.text();
            })
            .then(html => {
                document.getElementById('modal-editar-sede-content').innerHTML = html;
                const modalEl = document.getElementById('modalEditarSede');
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
                // Iniciar validación después de mostrar el modal
                modalEl.addEventListener('shown.bs.modal', function () {
                    if (typeof iniciarValidacionEditarSede === 'function') {
                        iniciarValidacionEditarSede();
                    }
                }, { once: true });
            })
            .catch(error => {
                console.error(error);
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo cargar el formulario de edición.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    background: '#1e1e1e',
                    color: '#f3f4f6'
                });
            });
    }

    // Event listener para cuando se abre el modal de crear sede
    const modalCrearSede = document.getElementById('modalCrearSede');
    if (modalCrearSede) {
        modalCrearSede.addEventListener('shown.bs.modal', function () {
            if (typeof iniciarValidacionCrearSede === 'function') {
                iniciarValidacionCrearSede();
            }
        });
    }
});
