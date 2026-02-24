// ========================================
// MODAL CATEGORÍAS - Carga dinámica
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

// MODAL EDITAR CATEGORÍA
document.querySelectorAll('.btn-editar-categoria').forEach(btn => {
    btn.onclick = function () {
        closeAllModals();
        const id = this.dataset.id;
        fetch(`/admin/categorias/${id}/edit`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('modal-editar-categoria-content').innerHTML = html;
                const modal = new bootstrap.Modal(document.getElementById('modalEditarCategoria'));
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

// MODAL EDITAR SUBCATEGORÍA
document.querySelectorAll('.btn-editar-subcategoria').forEach(btn => {
    btn.onclick = function () {
        closeAllModals();
        const id = this.dataset.id;
        fetch(`/admin/subcategorias/${id}/edit`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('modal-editar-subcategoria-content').innerHTML = html;
                const modal = new bootstrap.Modal(document.getElementById('modalEditarSubcategoria'));
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
