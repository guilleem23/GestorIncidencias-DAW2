document.addEventListener('DOMContentLoaded', function () {
    // ===================================================
    // ELEMENTOS COMUNES
    // ===================================================
    const filtrosForm = document.getElementById('form-filters') || document.getElementById('filtros-form');
    const incidenciasContainer = document.getElementById('incidencias-list-container') || document.getElementById('incidencias-container');
    const loadingOverlay = document.getElementById('loading-overlay');

    // ===================================================
    // FUNCIONALIDAD: Inicializar formularios de cierre
    // ===================================================
    function initCloseForms() {
        const closeForms = document.querySelectorAll('.form-close-incidencia');
        closeForms.forEach(form => {
            if (form.dataset.listenerAdded) return;
            form.dataset.listenerAdded = 'true';

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                confirmarCierre(form);
            });
        });
    }

    function confirmarCierre(form) {
        Swal.fire({
            title: 'Tancar incidència?',
            text: 'Confirmes que vols tancar aquesta incidència?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#22c55e',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fas fa-check"></i> Sí, tancar-la',
            cancelButtonText: '<i class="fas fa-times"></i> Cancel·lar',
            background: '#111111',
            color: '#f8fafc'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tancada',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false,
                                background: '#111111',
                                color: '#f8fafc'
                            }).then(() => {
                                const card = form.closest('.incidencia-card');
                                if (card) {
                                    const badgeContainer = card.querySelector('.incidencia-badges');
                                    if (badgeContainer) badgeContainer.innerHTML = '<span class="badge badge-active">Cerrada</span>';

                                    const actionDiv = form.closest('.incidencia-actions');
                                    if (actionDiv) {
                                        const viewUrl = `/client/incidencias/${form.action.split('/').pop()}`;
                                        actionDiv.innerHTML = `<a href="${viewUrl}" class="btn btn-primary"><i class="fas fa-eye"></i> Ver Detalles</a><span class="ms-2 text-secondary small"><i class="fas fa-check-circle"></i> Cerrada</span>`;
                                    }
                                } else {
                                    window.location.reload();
                                }
                            });
                        } else {
                            throw new Error(data.error || 'Error al tancar');
                        }
                    })
                    .catch(error => {
                        Swal.fire({ icon: 'error', title: 'Error', text: error.message, background: '#111111', color: '#f8fafc' });
                    });
            }
        });
    }

    initCloseForms();

    // ===================================================
    // FUNCIONALIDAD: Filtros con AJAX
    // ===================================================
    if (filtrosForm && incidenciasContainer) {
        filtrosForm.onsubmit = function (e) {
            e.preventDefault();
            if (loadingOverlay) loadingOverlay.style.display = 'flex';

            const formData = new FormData(filtrosForm);
            const params = new URLSearchParams(formData);

            fetch(`${filtrosForm.action}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        incidenciasContainer.innerHTML = data.html;
                        if (data.stats) {
                            const updateStat = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
                            updateStat('stat-senseassignar', data.stats.senseAssignar);
                            updateStat('stat-enproces', data.stats.enProces);
                            updateStat('stat-resoltes', data.stats.resoltes);
                            updateStat('stat-tancades', data.stats.tancades);
                        }
                        initCloseForms();
                        window.history.pushState({}, '', `${window.location.pathname}?${params.toString()}`);
                        incidenciasContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                })
                .catch(error => {
                    console.error('Error filtering:', error);
                    Swal.fire({ title: 'Error', text: 'Error al aplicar filtros', icon: 'error', background: '#111111', color: '#f8fafc' });
                })
                .finally(() => { if (loadingOverlay) loadingOverlay.style.display = 'none'; });
        };

        const btnClear = document.getElementById('btn-clear-filters');
        if (btnClear) {
            btnClear.onclick = function (e) {
                e.preventDefault();
                filtrosForm.reset();
                filtrosForm.dispatchEvent(new Event('submit'));
            };
        }
    }

    // ===================================================
    // ELEMENTOS DELEGADOS (onclick en document)
    // ===================================================
    function closeAllModals() {
        document.querySelectorAll('.modal.show').forEach(modalEl => {
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) {
                modalInstance.hide();
            }
        });
    }

    document.addEventListener('click', function (e) {
        // Editar Incidencia
        const btnEdit = e.target.closest('.btn-editar-incidencia-client');
        if (btnEdit) {
            e.preventDefault();
            closeAllModals();
            const incidenciaId = btnEdit.dataset.id;

            fetch(`/client/incidencias/${incidenciaId}/editar`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
                .then(response => {
                    if (!response.ok) throw new Error('Error al cargar');
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.incidencia) {
                        const inc = data.incidencia;
                        document.getElementById('edit-titol-client').value = inc.titol || '';
                        document.getElementById('edit-descripcio-client').value = inc.descripcio || '';

                        const catSelect = document.getElementById('edit-categoria-client');
                        catSelect.innerHTML = '<option value="">Selecciona...</option>';
                        if (window.categoriasData) {
                            window.categoriasData.forEach(c => {
                                const opt = document.createElement('option');
                                opt.value = c.id;
                                opt.textContent = c.nom;
                                opt.dataset.subcategorias = JSON.stringify(c.subcategorias || []);
                                if (c.id == inc.categoria_id) opt.selected = true;
                                catSelect.appendChild(opt);
                            });
                            if (inc.categoria_id) {
                                catSelect.dispatchEvent(new Event('change'));
                                setTimeout(() => {
                                    document.getElementById('edit-subcategoria-client').value = inc.subcategoria_id || '';
                                    validarFormularioIncidencia();
                                }, 100);
                            }
                        }

                        const form = document.getElementById('form-editar-incidencia-client');
                        if (form) form.action = `/client/incidencias/${incidenciaId}`;

                        const modalEl = document.getElementById('modalEditarIncidencia');
                        if (modalEl) {
                            const modal = new bootstrap.Modal(modalEl);
                            modal.show();
                        }
                    }
                })
                .catch(error => {
                    console.error(error);
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo cargar el formulario de edición.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar',
                        background: '#111111',
                        color: '#f8fafc'
                    });
                });
            return;
        }

        // Eliminar Incidencia
        const btnDelete = e.target.closest('.btn-eliminar-incidencia-client');
        if (btnDelete) {
            e.preventDefault();
            const incidenciaId = btnDelete.dataset.id;

            Swal.fire({
                title: '¿Eliminar incidencia?',
                text: 'Esta acción borrará también todos los comentarios.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Eliminar',
                background: '#111111',
                color: '#f8fafc'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/client/incidencias/${incidenciaId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({ icon: 'success', title: 'Eliminada', text: data.message, timer: 1500, showConfirmButton: false, background: '#111111', color: '#f8fafc' })
                                    .then(() => {
                                        if (window.location.pathname.includes('incidencias/')) {
                                            window.location.href = '/client/mis-incidencias';
                                        } else if (filtrosForm) {
                                            filtrosForm.dispatchEvent(new Event('submit'));
                                        } else {
                                            window.location.reload();
                                        }
                                    });
                            }
                        })
                        .catch(err => Swal.fire({ icon: 'error', title: 'Error', text: err.message, background: '#111111', color: '#f8fafc' }));
                }
            });
            return;
        }
    });

    const editCatSelect = document.getElementById('edit-categoria-client');
    const editSubSelect = document.getElementById('edit-subcategoria-client');
    if (editCatSelect && editSubSelect) {
        editCatSelect.onchange = function () {
            const subcats = this.options[this.selectedIndex] ? JSON.parse(this.options[this.selectedIndex].dataset.subcategorias || '[]') : [];
            editSubSelect.innerHTML = '<option value="">Selecciona...</option>';
            subcats.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id; opt.textContent = s.nom;
                editSubSelect.appendChild(opt);
            });
            editSubSelect.disabled = subcats.length === 0;
            validarFormularioIncidencia();
        };
        editSubSelect.onchange = validarFormularioIncidencia;
        document.getElementById('edit-titol-client').oninput = validarFormularioIncidencia;
        document.getElementById('edit-descripcio-client').oninput = validarFormularioIncidencia;
    }

    function validarFormularioIncidencia() {
        const t = document.getElementById('edit-titol-client').value.trim();
        const d = document.getElementById('edit-descripcio-client').value.trim();
        const c = document.getElementById('edit-categoria-client').value;
        const s = document.getElementById('edit-subcategoria-client').value;
        const submit = document.getElementById('btn-submit-edit-incidencia-client');
        if (submit) {
            const isValid = t.length >= 3 && d.length >= 10 && c && s;
            submit.disabled = !isValid;
            submit.style.opacity = isValid ? '1' : '0.6';
        }
    }

    const formEdit = document.getElementById('form-editar-incidencia-client');
    if (formEdit) {
        formEdit.onsubmit = function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Actualizada', text: data.message, timer: 1500, showConfirmButton: false, background: '#111111', color: '#f8fafc' })
                            .then(() => window.location.reload());
                    } else {
                        throw new Error(data.error || 'Error');
                    }
                })
                .catch(err => Swal.fire({ icon: 'error', title: 'Error', text: err.message, background: '#111111', color: '#f8fafc' }));
        };
    }

    // ===================================================
    // FUNCIONALIDAD: Toggle Resoltes/Cerradas
    // ===================================================
    const btnToggle = document.getElementById('btn-toggle-closed');
    if (btnToggle) {
        btnToggle.onclick = function () {
            const isOcullt = this.classList.contains('active');
            if (isOcullt) {
                this.classList.remove('active');
                this.innerHTML = '<i class="fa-solid fa-eye-slash"></i> Ocultar resueltas/cerradas';
            } else {
                this.classList.add('active');
                this.innerHTML = '<i class="fa-solid fa-eye"></i> Mostrar resueltas/cerradas';
            }

            const inputName = 'ocultar_resoltes';
            let hidden = filtrosForm.querySelector(`input[name="${inputName}"]`);
            if (!hidden) {
                hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = inputName;
                filtrosForm.appendChild(hidden);
            }
            hidden.value = this.classList.contains('active') ? '1' : '0';
            filtrosForm.dispatchEvent(new Event('submit'));
        };
    }
});
