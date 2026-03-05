document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-editar-comentario');
    const missatgeInput = document.getElementById('edit-missatge-comentario');
    const imatgeInput = document.getElementById('edit-imatge-comentario');
    const submitBtn = document.getElementById('btn-submit-edit-comentario');

    if (!form || !missatgeInput || !submitBtn) return;

    missatgeInput.oninput = comprobarFormulario;
    missatgeInput.onblur = validarMissatge;

    imatgeInput.onchange = function () {
        validarImatge();
        comprobarFormulario();
    };

    // Validación al abrir el modal
    const modal = document.getElementById('modalEditarComentario');
    if (modal) {
        modal.addEventListener('shown.bs.modal', function () {
            errorMissatge.style.display = 'none';
            errorImatge.style.display = 'none';
            missatgeInput.classList.remove('border-danger');
            imatgeInput.classList.remove('border-danger');

            setTimeout(() => {
                comprobarFormulario();
            }, 100);
        });
    }

    form.onsubmit = function (e) {
        if (!validarMissatge() || !validarImatge()) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: 'Por favor, corrige los errores antes de guardar.',
                background: '#111111',
                color: '#f8fafc'
            });
        }
    };

    comprobarFormulario();
});
