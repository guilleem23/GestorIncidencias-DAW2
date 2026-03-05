// Abrir modal de creación si hay errores de validación (opcional, si se implementa en el futuro)
window.addEventListener('DOMContentLoaded', function () {
    if (window.modalIncidenciaOpen) {
        var modalCrear = new bootstrap.Modal(document.getElementById('modalCrearIncidencia'));
        modalCrear.show();
    }
});

// MODAL EDITAR INCIDENCIA (ADMIN)
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

        // Mostrar estado de carga (vaciamos el contenido previo)
        const modalContent = document.getElementById('modal-editar-content');
        if (modalContent) {
            modalContent.innerHTML = '';
        }

        fetch(`/admin/incidencias/${id}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(res => {
                if (!res.ok) throw new Error('Error al cargar: ' + res.status);
                return res.text();
            })
            .then(html => {
                document.getElementById('modal-editar-content').innerHTML = html;
                const modalEl = document.getElementById('modalEditarIncidencia');
                const modal = new bootstrap.Modal(modalEl);
                modal.show();

                // Inicializar la lógica de categorías/subcategorias tras cargar el HTML
                inicializarLogicaCategorias();

                // Inicializar validación del formulario
                if (typeof window.iniciarValidacionEditarIncidencia === 'function') {
                    window.iniciarValidacionEditarIncidencia();
                }
            })
            .catch(error => {
                console.error('Error en fetch:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo cargar el formulario de edición. ' + error.message,
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
    console.log('🔄 Inicializando lógica de categorías...');

    const categoriaSelect = document.getElementById('categoria_id');
    const subcategoriaSelect = document.getElementById('subcategoria_id');
    const formEditar = document.getElementById('form-editar-incidencia');

    console.log({ categoriaSelect, subcategoriaSelect, formEditar });

    if (categoriaSelect && subcategoriaSelect) {
        // Remover evento anterior clonando el elemento
        const newCategoriaSelect = categoriaSelect.cloneNode(true);
        categoriaSelect.parentNode.replaceChild(newCategoriaSelect, categoriaSelect);

        newCategoriaSelect.addEventListener('change', function () {
            const categoriaId = this.value;
            const categorias = window.categoriasData || [];
            const categoriaSeleccionada = categorias.find(c => c.id == categoriaId);

            console.log('📁 Categoría seleccionada:', categoriaSeleccionada);

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
        // Remover evento anterior clonando el elemento
        const newFormEditar = formEditar.cloneNode(true);
        formEditar.parentNode.replaceChild(newFormEditar, formEditar);

        newFormEditar.addEventListener('submit', function (e) {
            e.preventDefault();
            console.log('📝 Formulario al enviar');

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
                    const formData = new FormData(newFormEditar);
                    const url = newFormEditar.action;

                    console.log('🚀 Enviando fetch a:', url);
                    console.log('Headers con CSRF token');

                    if (btnSave) {
                        btnSave.disabled = true;
                        btnSave.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';
                    }

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(res => {
                            console.log('📦 Respuesta recibida:', res.status);
                            return res.json();
                        })
                        .then(data => {
                            console.log('✅ Datos JSON:', data);

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

                                // Actualizar tabla mediante fetch o recargar página como fallback
                                if (window.fetchIncidencias) {
                                    console.log('🔄 Actualizando tabla...');
                                    window.fetchIncidencias();
                                } else {
                                    setTimeout(() => {
                                        console.log('🔄 Recargando página...');
                                        window.location.reload();
                                    }, 2000);
                                }
                            } else {
                                let errorMsg = 'Error en los datos:\n';
                                if (data.errors) {
                                    Object.values(data.errors).forEach(err => {
                                        if (Array.isArray(err)) {
                                            errorMsg += `- ${err[0]}\n`;
                                        } else {
                                            errorMsg += `- ${err}\n`;
                                        }
                                    });
                                }
                                console.warn('❌ Error:', errorMsg);
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
                            console.error('❌ Error en fetch:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error inesperado: ' + error.message,
                                background: '#1e293b',
                                color: '#f8fafc'
                            });
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
