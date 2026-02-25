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
    const btn = e.target.closest('[name="editar_usuario"]');
    if (btn) {
        closeAllModals();
        const id = btn.value;
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

                console.log("Formulario de edición inyectado. Buscando función de validación...");
                // Lanzar validación JS de editar usuario
                if (typeof window.iniciarValidacionEditarUsuario === 'function') {
                    console.log("Llamando a iniciarValidacionEditarUsuario()");
                    window.iniciarValidacionEditarUsuario();
                } else {
                    console.warn("La función iniciarValidacionEditarUsuario no existe globalmente.");
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
});
