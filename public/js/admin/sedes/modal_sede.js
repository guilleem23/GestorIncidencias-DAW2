// ========================================
// MODAL SEDES - Carga dinámica
// ========================================

// Cerrar todos los modales abiertos
function closeAllModals() {
    document.querySelectorAll('.modal.show').forEach(modalEl => {
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) {
            modalInstance.hide();
        }
    });
}

// MODAL EDITAR SEDE
document.querySelectorAll('.btn-editar-sede').forEach(btn => {
    btn.onclick = function () {
        closeAllModals();
        const id = this.dataset.id;
        fetch(`/admin/sedes/${id}/edit`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('modal-editar-sede-content').innerHTML = html;
                const modal = new bootstrap.Modal(document.getElementById('modalEditarSede'));

                // Inicializar validación si la función existe
                if (typeof window.iniciarValidacionEditarSede === 'function') {
                    window.iniciarValidacionEditarSede();
                }

                modal.show();
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo cargar el formulario de edición.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    background: '#1e1e1e',
                    color: '#f3f4f6'
                });
            });
    };
});
