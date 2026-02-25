// Confirmación SweetAlert para cerrar sesión
// Confirmación SweetAlert para cerrar sesión con enlace
document.addEventListener('DOMContentLoaded', function () {
    const logoutLink = document.getElementById('enlace-cerrar-sesion');
    if (logoutLink) {
        logoutLink.addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Cerrar sesión?',
                theme: 'dark',
                text: '¿Seguro que quieres cerrar la sesión?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Hacer logout vía POST usando fetch
                    fetch('/logout', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.getElementById('csrf-token').getAttribute('content'),
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin'
                    }).then(() => {
                        localStorage.setItem('logout_success', '1');
                        window.location.href = '/';
                    });
                }
            });
        });
    }
});
// SweetAlert para cierre de sesión exitoso
document.addEventListener('DOMContentLoaded', function () {
    if (localStorage.getItem('logout_success')) {
        Swal.fire({
            icon: 'success',
            title: 'Sesión cerrada',
            text: 'Has cerrado sesión correctamente.',
            confirmButtonColor: '#3085d6',
        });
        localStorage.removeItem('logout_success');
    }
});


// Delegación de eventos para el botón eliminar (soporta actualización AJAX)
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[name="boton_eliminar"]');
    if (btn) {
        e.preventDefault();
        const form = btn.closest('form');
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¡Esta acción no se puede deshacer!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
});
