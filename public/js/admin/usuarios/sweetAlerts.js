
document.addEventListener('DOMContentLoaded', function () {
    const successDiv = document.getElementById('swal-success');

    if (successDiv) {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: successDiv.dataset.message,
            theme: 'dark',
            background: '#181818',
            color: '#fff',
            confirmButtonColor: '#2563eb'
        });
    }
});

document.addEventListener('click', function (e) {
    if (e.target.name === 'boton_eliminar' || e.target.closest('[name="boton_eliminar"]')) {
        e.preventDefault();
        e.stopPropagation();
        
        const btn = e.target.name === 'boton_eliminar' ? e.target : e.target.closest('[name="boton_eliminar"]');
        const form = btn.closest('form');

        Swal.fire({
            title: '¿Estás seguro?',
            text: '¡Esta acción no se puede deshacer!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            background: '#181818',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
});
