// MODAL EDITAR INCIDENCIA (GESTOR)
function closeAllModals() {
    document.querySelectorAll('.modal.show').forEach(modalEl => {
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) {
            modalInstance.hide();
        }
    });
}

// Delegación de eventos para el botón editar
document.addEventListener('click', function (e) {
    const btnEdit = e.target.closest('[name="editar_incidencia"]');
    if (btnEdit) {
        e.preventDefault();
        closeAllModals();
        const id = btnEdit.dataset.id;

        // Mostrar estado de carga (opcional)
        document.getElementById('modal-editar-content').innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-white">Cargando formulario...</p></div>';

        const modalEl = document.getElementById('modalEditarIncidencia');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        fetch(`/gestor/incidencias/${id}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(res => {
                if (!res.ok) throw new Error('Error al cargar');
                return res.text();
            })
            .then(html => {
                document.getElementById('modal-editar-content').innerHTML = html;

                // Inicializar la lógica de categorías/subcategorias tras cargar el HTML
                inicializarLogicaCategorias();
            })
            .catch(error => {
                console.error(error);
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo cargar el formulario de edición.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    background: '#1e293b',
                    color: '#f8fafc'
                });
            });
    }
});

// Lógica de Categorías y Subcategorías para el Modal
function inicializarLogicaCategorias() {
    const categoriaSelect = document.getElementById('categoria_id');
    const subcategoriaSelect = document.getElementById('subcategoria_id');
    const formEditar = document.getElementById('form-editar-incidencia');

    if (categoriaSelect && subcategoriaSelect) {
        categoriaSelect.addEventListener('change', function () {
            const categoriaId = this.value;
            const categorias = window.categoriasData || [];
            const categoriaSeleccionada = categorias.find(c => c.id == categoriaId);

            // Limpiar subcategorías
            subcategoriaSelect.innerHTML = '<option value="" disabled selected>Selecciona una subcategoría</option>';

            if (categoriaSeleccionada && categoriaSeleccionada.subcategorias) {
                categoriaSeleccionada.subcategorias.forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub.id;
                    option.textContent = sub.nom;
                    subcategoriaSelect.appendChild(option);
                });
            }
        });
    }

    if (formEditar) {
        formEditar.addEventListener('submit', function (e) {
            e.preventDefault();

            // Validaciones de negocio (Técnico vs Estado) - Reutilizamos la lógica si fuera necesario
            // Pero aquí vamos directo al Fetch y dejamos que el server valide o el JS nativo

            if (!this.checkValidity()) {
                this.reportValidity();
                return;
            }

            Swal.fire({
                title: '¿Guardar cambios?',
                text: "La incidencia se actualizará asíncronamente.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar',
                background: '#1e293b',
                color: '#f8fafc'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(formEditar);
                    const url = formEditar.action;

                    fetch(url, {
                        method: 'POST', // Laravel usa _method PUT en el FormData
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                const modalEl = document.getElementById('modalEditarIncidencia');
                                bootstrap.Modal.getInstance(modalEl).hide();

                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Actualizado!',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                    background: '#1e293b',
                                    color: '#f8fafc'
                                });

                                // Refrescar la UI
                                if (window.fetchIncidencias) {
                                    // Estamos en el historial
                                    window.fetchIncidencias();
                                } else {
                                    // Estamos en ver detalle, recargamos para ver los nuevos datos
                                    // (O podríamos actualizar campos específicos, pero reload es más seguro para asegurar consistencia total)
                                    setTimeout(() => window.location.reload(), 2000);
                                }
                            } else {
                                // Errores de validación
                                let errorMsg = 'Error en los datos:\n';
                                if (data.errors) {
                                    Object.values(data.errors).forEach(err => errorMsg += `- ${err}\n`);
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: errorMsg || data.message,
                                    background: '#1e293b',
                                    color: '#f8fafc'
                                });
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            Swal.fire('Error', 'Ocurrió un error inesperado.', 'error');
                        });
                }
            });
        });
    }
}
