function iniciarValidacionComentarios() {
    const MAX_FILE_SIZE = 4 * 1024 * 1024; // 4MB
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    // FORMULARIO CREAR
    const formCrear = document.getElementById('form-comentario');
    if (formCrear) {
        const inputMissatge = document.getElementById('missatge-comentario');
        const inputFile = document.getElementById('imatge-comentario');
        const btnSubmit = document.getElementById('btn-submit-comentario');

        const validar = () => {
            const hasText = !!inputMissatge && inputMissatge.value.trim().length > 0;
            const hasFile = !!inputFile && inputFile.files.length > 0;
            const isValid = hasText || hasFile;

            if (btnSubmit) {
                btnSubmit.disabled = !isValid;
                btnSubmit.style.opacity = isValid ? '1' : '0.5';
                btnSubmit.style.cursor = isValid ? 'pointer' : 'not-allowed';
            }
        };

        if (inputMissatge) inputMissatge.oninput = validar;
        if (inputFile) inputFile.onchange = validar;
        validar();
    }

    // FORMULARIO EDITAR (admin/gestor/client)
    const formEditar = document.getElementById('form-editar-comentario');
    const inputMissatgeEdit = document.getElementById('edit-missatge-comentario');
    const inputFileEdit = document.getElementById('edit-imatge-comentario');
    const fileNameDisplay = document.getElementById('edit-file-name-display');
    const btnSubmitEdit =
        document.getElementById('btn-submit-edit-comentario') ||
        document.getElementById('btn-save-edit-comment') ||
        (formEditar ? formEditar.querySelector('button[type="submit"]') : null);

    function ensureErrorElement(id, afterElement) {
        if (!afterElement) return null;

        let el = document.getElementById(id);
        if (el) return el;

        el = document.createElement('div');
        el.id = id;
        el.className = 'text-danger small mt-1';
        afterElement.insertAdjacentElement('afterend', el);
        return el;
    }

    const errorMissatge = ensureErrorElement('error-edit-missatge-comentario', inputMissatgeEdit);
    const errorImatge = ensureErrorElement('error-edit-imatge-comentario', inputFileEdit);

    function hasExistingImage() {
        if (!fileNameDisplay) return false;
        const text = fileNameDisplay.textContent.trim();
        return text.toLowerCase().includes('imagen actual');
    }

    function validarImatge() {
        if (!inputFileEdit || !errorImatge) return true;

        const file = inputFileEdit.files && inputFileEdit.files[0] ? inputFileEdit.files[0] : null;
        if (!file) {
            errorImatge.textContent = '';
            inputFileEdit.classList.remove('border-danger');
            return true;
        }

        if (!allowedTypes.includes(file.type)) {
            errorImatge.textContent = 'Formato no permitido. Usa JPG, PNG, GIF o WEBP.';
            inputFileEdit.classList.add('border-danger');
            return false;
        }

        if (file.size > MAX_FILE_SIZE) {
            errorImatge.textContent = 'La imagen no puede superar 4MB.';
            inputFileEdit.classList.add('border-danger');
            return false;
        }

        errorImatge.textContent = '';
        inputFileEdit.classList.remove('border-danger');
        return true;
    }

    function validarContenido() {
        if (!inputMissatgeEdit) return true;

        const hasText = inputMissatgeEdit.value.trim().length > 0;
        const hasNewFile = !!(inputFileEdit && inputFileEdit.files && inputFileEdit.files.length > 0);
        const isValid = hasText || hasNewFile || hasExistingImage();

        if (errorMissatge) {
            errorMissatge.textContent = isValid ? '' : 'Debes escribir un comentario o adjuntar una imagen.';
        }

        if (isValid) {
            inputMissatgeEdit.classList.remove('border-danger');
        } else {
            inputMissatgeEdit.classList.add('border-danger');
        }

        return isValid;
    }

    function actualizarEstadoBoton() {
        const contenidoValido = validarContenido();
        const imagenValida = validarImatge();
        const isValid = contenidoValido && imagenValida;

        if (btnSubmitEdit) {
            btnSubmitEdit.disabled = !isValid;
            btnSubmitEdit.style.opacity = isValid ? '1' : '0.5';
            btnSubmitEdit.style.cursor = isValid ? 'pointer' : 'not-allowed';
        }

        return isValid;
    }

    if (formEditar && inputMissatgeEdit) {
        inputMissatgeEdit.oninput = actualizarEstadoBoton;
        inputMissatgeEdit.onblur = actualizarEstadoBoton;
    }

    if (inputFileEdit) {
        inputFileEdit.onchange = function () {
            const file = this.files && this.files[0] ? this.files[0] : null;
            if (fileNameDisplay) {
                if (file) {
                    fileNameDisplay.textContent = file.name;
                } else if (!hasExistingImage()) {
                    fileNameDisplay.textContent = '';
                }
            }
            actualizarEstadoBoton();
        };
    }

    const modal = document.getElementById('modalEditarComentario');
    if (modal) {
        modal.addEventListener('shown.bs.modal', function () {
            setTimeout(actualizarEstadoBoton, 50);
        });
    }

    window.validarFormularioEditarComentario = function () {
        return actualizarEstadoBoton();
    };

    actualizarEstadoBoton();
}

document.addEventListener('DOMContentLoaded', iniciarValidacionComentarios);

window.iniciarValidacionComentarios = iniciarValidacionComentarios;
