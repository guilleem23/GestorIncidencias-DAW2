// ========================================
// SWEET ALERTS - CRUD SEDES
// Confirmaciones de eliminación y mensajes
// ========================================

// Confirmación para eliminar SEDE
const oldSedesClick = document.onclick;
document.onclick = function (e) {
    if (oldSedesClick) oldSedesClick(e);

    // Confirmación para eliminar SEDE
    const btnEliminar = e.target.closest('.btn-eliminar-sede');
    if (btnEliminar) {
        e.preventDefault();
        const form = btnEliminar.closest('form');
        const nombre = btnEliminar.dataset.nombre;

        let textoAdvertencia = `Se eliminará la sede "${nombre}". ¡Esta acción no se puede deshacer!`;

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

    // Mostrar descripción completa de la sede
    const btnInfo = e.target.closest('.btn-info-desc');
    if (btnInfo) {
        const nombre = btnInfo.dataset.nombre;
        const descripcion = btnInfo.dataset.descripcion;
        const gestor = btnInfo.dataset.gestor;

        Swal.fire({
            title: nombre,
            html: '<p style="margin-bottom:0.75rem;">' + descripcion + '</p>' +
                '<p style="opacity:0.7; font-size:0.9rem;"><i class="fa-solid fa-user-tie"></i> Gestor: <strong>' + gestor + '</strong></p>',
            icon: 'info',
            confirmButtonColor: '#3b82f6',
            confirmButtonText: 'Cerrar',
            background: '#1e1e1e',
            color: '#f3f4f6'
        });
        return;
    }
};
