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

// Delegación de eventos usando addEventListener para evitar conflictos
document.addEventListener('click', function (e) {
    // BOTÓN EDITAR
    const btnEdit = e.target.closest('[name="editar_usuario"]');
    if (btnEdit) {
        closeAllModals();
        const id = btnEdit.value;
        fetch(`/admin/usuarios/${id}/edit`)
            .then(res => {
                if (!res.ok) throw new Error('Error al cargar');
                return res.text();
            })
            .then(html => {
                document.getElementById('modal-editar-content').innerHTML = html;
                const modalEl = document.getElementById('modalEditarUsuario');
                const modal = new bootstrap.Modal(modalEl);
                modal.show();

                if (typeof window.iniciarValidacionEditarUsuario === 'function') {
                    window.iniciarValidacionEditarUsuario();
                }
            })
            .catch(error => {
                console.error(error);
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo cargar el formulario para editar el usuario.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            });
    }

    // BOTÓN VER (NUEVO)
    const btnView = e.target.closest('[name="ver_usuario"]');
    if (btnView) {
        closeAllModals();
        const id = btnView.value;
        // Animación de carga opcional o estado visual
        fetch(`/admin/usuarios/${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(res => {
                if (!res.ok) throw new Error('Error al cargar detalle');
                return res.text();
            })
            .then(html => {
                document.getElementById('modal-ver-content').innerHTML = html;
                const modalEl = document.getElementById('modalVerUsuario');
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            })
            .catch(error => {
                console.error(error);
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo cargar el detalle del usuario.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            });
    }
});