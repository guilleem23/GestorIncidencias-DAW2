document.addEventListener('DOMContentLoaded', function () {
    // Manejo de Asignación de Técnico vía AJAX
    document.addEventListener('click', function (e) {
        // Usamos una clase para detectar el clic en botones de asignar
        const btnDelegado = e.target.closest('.btn-assign-tecnic');
        if (btnDelegado) {
            const id = btnDelegado.dataset.id;
            // Obtenemos el botón exacto por su ID como pide el usuario
            const btnAssign = document.getElementById(`btn-assign-${id}`);
            const form = document.getElementById(`form-assign-${id}`);
            const select = document.getElementById(`select-tecnic-${id}`);

            if (!select || !select.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selecciona un técnico',
                    text: 'Debes elegir un técnico antes de asignar.',
                    background: '#1e293b',
                    color: '#f8fafc',
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }

            const tecnicoName = select.options[select.selectedIndex].text;

            Swal.fire({
                title: '¿Asignar técnico?',
                html: `Se asignará a <strong>${tecnicoName}</strong> a esta incidencia.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#4b5563',
                confirmButtonText: 'Sí, asignar',
                cancelButtonText: 'Cancelar',
                background: '#1e293b',
                color: '#f8fafc'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(form);
                    const url = form.action;

                    // Estado de carga (CSS + Texto)
                    btnAssign.classList.add('btn-loading');
                    const originalHTML = btnAssign.innerHTML;
                    btnAssign.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: data.message,
                                    showConfirmButton: false,
                                    timer: 2000,
                                    background: '#1e293b',
                                    color: '#f8fafc'
                                });

                                // Animación de salida vía CSS
                                const row = btnAssign.closest('tr');
                                if (row) {
                                    row.classList.add('fade-out-left');

                                    setTimeout(() => {
                                        row.remove();

                                        // Comprobar si la tabla ha quedado vacía
                                        const tbody = document.getElementById('assign-table-body');
                                        if (tbody && tbody.getElementsByTagName('tr').length === 0) {
                                            window.location.reload();
                                        }
                                    }, 500);
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message,
                                    background: '#1e293b',
                                    color: '#f8fafc'
                                });
                                btnAssign.classList.remove('btn-loading');
                                btnAssign.innerHTML = originalHTML;
                            }
                        })
                        .catch(error => {
                            console.error('Error en asignación:', error);
                            Swal.fire('Error', 'No se pudo completar la asignación.', 'error');
                            btnAssign.classList.remove('btn-loading');
                            btnAssign.innerHTML = originalHTML;
                        });
                }
            });
        }
    });
});
