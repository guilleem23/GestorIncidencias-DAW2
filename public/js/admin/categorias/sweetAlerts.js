// ========================================
// SWEET ALERTS - CRUD CATEGORÍAS
// Confirmaciones de eliminación y mensajes
// ========================================

// Confirmación para eliminar CATEGORÍA
const oldCategoriasClick = document.onclick;
document.onclick = function (e) {
    if (oldCategoriasClick) oldCategoriasClick(e);

    // Confirmación para eliminar CATEGORÍA
    const btnEliminarCat = e.target.closest('.btn-eliminar-categoria');
    if (btnEliminarCat) {
        e.preventDefault();
        const form = btnEliminarCat.closest('form');
        const nombre = btnEliminarCat.dataset.nombre;
        const numSubcategorias = parseInt(btnEliminarCat.dataset.subcategorias) || 0;

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
            color: '#f3f4f6'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
        return;
    }

    // Confirmación para eliminar SUBCATEGORÍA
    const btnEliminarSub = e.target.closest('.btn-eliminar-subcategoria');
    if (btnEliminarSub) {
        e.preventDefault();
        const form = btnEliminarSub.closest('form');
        const nombre = btnEliminarSub.dataset.nombre;

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
            color: '#f3f4f6'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
        return;
    }

    // Mostrar descripción en SweetAlert
    const btnShowDesc = e.target.closest('.btn-show-desc');
    if (btnShowDesc) {
        e.preventDefault();
        e.stopPropagation();
        const nombre = btnShowDesc.dataset.nombre;
        const descripcion = btnShowDesc.dataset.descripcion;

        Swal.fire({
            title: `<span style="color: var(--neon-blue)">${nombre}</span>`,
            html: `<div style="text-align: center; padding: 10px; line-height: 1.6;">${descripcion}</div>`,
            icon: 'info',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#3b82f6',
            background: '#1e1e1e',
            color: '#f3f4f6'
        });
        return;
    }
};
