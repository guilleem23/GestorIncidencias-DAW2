(function () {
    "use strict";

    // 1. Manejo dinámico de las Subcategorías (Edición)
    const categoriaSelect = document.getElementById('categoria_id');
    const subcategoriaSelect = document.getElementById('subcategoria_id');

    if (categoriaSelect && subcategoriaSelect && typeof categoriasData !== 'undefined') {
        categoriaSelect.onchange = function () {
            const categoriaId = parseInt(this.value);
            const categoriaObj = categoriasData.find(c => c.id === categoriaId);
            subcategoriaSelect.innerHTML = '';
            if (categoriaObj && categoriaObj.subcategorias) {
                categoriaObj.subcategorias.forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub.id;
                    option.textContent = sub.nom;
                    subcategoriaSelect.appendChild(option);
                });
            }
        };
    }

    // 2. Confirmación y Validaciones al Editar Incidencia
    const btnSubmitEdit = document.getElementById('btn-submit-edit');
    const formEditar = document.getElementById('form-editar-incidencia');

    if (btnSubmitEdit && formEditar) {
        formEditar.onsubmit = function (e) {
            e.preventDefault();

            // Validación HTML5 nativa primero
            if (!formEditar.checkValidity()) {
                formEditar.reportValidity();
                return;
            }

            // Validaciones de negocio (Técnico vs Estado)
            const selectTecnic = document.getElementById('tecnic_id');
            const selectEstat = document.getElementById('estat');

            if (selectTecnic && selectEstat) {
                const isTecnicAssigned = selectTecnic.value !== "";
                const isStatusSenseAssignar = selectEstat.value === "Sense assignar";

                if (isTecnicAssigned && isStatusSenseAssignar) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de Validación',
                            text: 'El estado no puede ser "Sin asignar" si hay un técnico asignado.',
                            background: '#1e293b',
                            color: '#f8fafc'
                        });
                    } else {
                        alert('El estado no puede ser "Sin asignar" si hay un técnico asignado.');
                    }
                    return;
                }

                if (!isTecnicAssigned && !isStatusSenseAssignar) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de Validación',
                            text: 'Debe asignar un técnico si el estado no es "Sin asignar".',
                            background: '#1e293b',
                            color: '#f8fafc'
                        });
                    } else {
                        alert('Debe asignar un técnico si el estado no es "Sin asignar".');
                    }
                    return;
                }
            }

            // Confirmación final con SweetAlert
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¿Guardar los cambios?',
                    text: "Los datos de la incidencia serán actualizados en el sistema.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'No, cancelar',
                    background: '#1e293b',
                    color: '#f8fafc'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Guardando...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); },
                            background: '#1e293b',
                            color: '#f8fafc'
                        });

                        // Enviar formulario vía AJAX
                        const formData = new FormData(formEditar);

                        fetch(formEditar.action, {
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
                                        icon: 'success',
                                        title: '¡Éxito!',
                                        text: data.message,
                                        background: '#1e293b',
                                        color: '#f8fafc'
                                    }).then(() => {
                                        // Redirigir a la vista de la incidencia
                                        if (data.redirect) {
                                            window.location.href = data.redirect;
                                        } else {
                                            window.location.reload();
                                        }
                                    });
                                } else {
                                    let errorMsg = data.message || 'Error al actualizar la incidencia.';
                                    if (data.errors) {
                                        const firstError = Object.values(data.errors)[0];
                                        if (Array.isArray(firstError)) {
                                            errorMsg = firstError[0];
                                        }
                                    }
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: errorMsg,
                                        background: '#1e293b',
                                        color: '#f8fafc'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error al actualizar incidencia:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No se pudo actualizar la incidencia.',
                                    background: '#1e293b',
                                    color: '#f8fafc'
                                });
                            });
                    }
                });
            } else {
                formEditar.submit();
            }
        };
    }
})();
