// Abrir modal de creación si hay errores de validación
document.addEventListener('DOMContentLoaded', function () {
    if (window.modalCategoriaOpen) {
        var modalCrear = new bootstrap.Modal(document.getElementById('modalCrearCategoria'));
        modalCrear.show();
    }
    if (window.modalSubcategoriaOpen) {
        var modalCrearSub = new bootstrap.Modal(document.getElementById('modalCrearSubcategoria'));
        modalCrearSub.show();
    }
        // Event listener para cuando se abre el modal de crear categoría
        const modalCrearCategoria = document.getElementById('modalCrearCategoria');
        if (modalCrearCategoria) {
            modalCrearCategoria.addEventListener('shown.bs.modal', function () {
                if (typeof iniciarValidacionCrearCategoria === 'function') {
                    iniciarValidacionCrearCategoria();
                }
            });
        }

        // Event listener para cuando se abre el modal de crear subcategoría
        const modalCrearSubcategoria = document.getElementById('modalCrearSubcategoria');
        if (modalCrearSubcategoria) {
            modalCrearSubcategoria.addEventListener('shown.bs.modal', function () {
                if (typeof iniciarValidacionCrearSubcategoria === 'function') {
                    iniciarValidacionCrearSubcategoria();
                }
            });
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
    // MODAL EDITAR CATEGORÍA
    const btnEditCat = e.target.closest('.btn-editar-categoria');
    if (btnEditCat) {
        e.preventDefault();
        e.stopPropagation();
        
        closeAllModals();
        const id = btnEditCat.dataset.id;
        fetch(`/admin/categorias/${id}/edit`)
            .then(res => {
                if (!res.ok) throw new Error('Error al cargar');
                return res.text();
            })
            .then(html => {
                document.getElementById('modal-editar-categoria-content').innerHTML = html;
                const modalEl = document.getElementById('modalEditarCategoria');
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
                    // Iniciar validación después de mostrar el modal
                    modalEl.addEventListener('shown.bs.modal', function () {
                        if (typeof iniciarValidacionEditarCategoria === 'function') {
                            iniciarValidacionEditarCategoria();
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
        return;
    }

    // MODAL EDITAR SUBCATEGORÍA
    const btnEditSub = e.target.closest('.btn-editar-subcategoria');
    if (btnEditSub) {
        e.preventDefault();
        e.stopPropagation();
        
        closeAllModals();
        const id = btnEditSub.dataset.id;
        fetch(`/admin/subcategorias/${id}/edit`)
            .then(res => {
                if (!res.ok) throw new Error('Error al cargar');
                return res.text();
            })
            .then(html => {
                document.getElementById('modal-editar-subcategoria-content').innerHTML = html;
                const modalEl = document.getElementById('modalEditarSubcategoria');
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
                    // No hay validación específica para editar subcategoría aún
                    // Podrías crearla si es necesario
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
});
