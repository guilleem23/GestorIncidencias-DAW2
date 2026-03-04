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
    const btnDelegado = e.target.closest('.btn-editar-incidencia');
    if (btnDelegado) {
        e.preventDefault();
        closeAllModals();
        const id = btnDelegado.dataset.id;
        // Obtenemos el botón exacto por ID como pide el usuario
        const btnEdit = document.getElementById(`btn-edit-incidencia-${id}`);

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

            if (!this.checkValidity()) {
                this.reportValidity();
                return;
            }

            const btnSave = document.getElementById('btn-save-incidencia');
            const originalContent = btnSave ? btnSave.innerHTML : '';

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

                    if (btnSave) {
                        btnSave.disabled = true;
                    }

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
                                const modalEl = document.getElementById('modalEditarIncidencia');
                                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                                if (modalInstance) modalInstance.hide();

                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Actualizado!',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                    background: '#1e293b',
                                    color: '#f8fafc'
                                });

                                if (window.fetchIncidencias) {
                                    window.fetchIncidencias();
                                } else {
                                    setTimeout(() => window.location.reload(), 2000);
                                }
                            } else {
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
                        })
                        .finally(() => {
                            if (btnSave) {
                                btnSave.disabled = false;
                                btnSave.innerHTML = originalContent;
                            }
                        });
                }
            });
        });
    }
}
