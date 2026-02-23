// Abrir modal de creación si hay errores de validación
window.addEventListener('DOMContentLoaded', function () {
    if (window.modalUsuarioOpen) {
        var modalCrear = new bootstrap.Modal(document.getElementById('modalCrearUsuario'));
        modalCrear.show();
    }
});

// MODAL EDITAR USUARIO
function closeAllModals() {
    document.querySelectorAll('.modal.show').forEach(modalEl => {
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) {
            modalInstance.hide();
        }
    });
}

document.querySelectorAll('.btn-editar-usuario').forEach(btn => {
    btn.onclick = function () {
        closeAllModals();
        const id = this.dataset.id;
        fetch(`/admin/usuarios/${id}/edit`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('modal-editar-content').innerHTML = html;
                const modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
                modal.show();
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo cargar el formulario de edición.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            });
    };
});

