// Abrir modal de creación si hay errores de validación
window.addEventListener('DOMContentLoaded', function () {
    if (window.modalUsuarioOpen) {
        var modalCrear = new bootstrap.Modal(document.getElementById('modalCrearUsuario'));
        modalCrear.show();
        // Lanzar validación JS de crear usuario si existe la función global
        if (typeof window.iniciarValidacionCrearUsuario === 'function') {
            window.iniciarValidacionCrearUsuario();
        }
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

// Delegación de eventos para el botón editar (soporta actualización AJAX)
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[name="editar_usuario"]');
    if (btn) {
        closeAllModals();
        const id = btn.value;
        fetch(`/admin/usuarios/${id}/edit`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('modal-editar-content').innerHTML = html;
                const modalEl = document.getElementById('modalEditarUsuario');
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            })
            .catch(error => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo cargar el formulario de edición.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                } else {
                    alert('No se pudo cargar el formulario de edición.');
                }
            });
    }
});

