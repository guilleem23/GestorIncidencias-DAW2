// Validación para el modal de editar comentario del cliente
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-editar-comentario');
    const missatgeInput = document.getElementById('edit-missatge-comentario');
    const imatgeInput = document.getElementById('edit-imatge-comentario');
    const submitBtn = document.getElementById('btn-submit-edit-comentario');

    if (!form || !missatgeInput || !submitBtn) return;

    // Variables para los mensajes de error
    let errorMissatge = null;
    let errorImatge = null;

    // Crear contenedores de error si no existen
    function crearMensajeError(inputElement, id) {
        let errorElement = document.getElementById(id);
        if (!errorElement) {
            errorElement = document.createElement('small');
            errorElement.id = id;
            errorElement.className = 'form-text';
            errorElement.style.color = '#ef4444';
            errorElement.style.fontSize = '0.875rem';
            errorElement.style.marginTop = '0.25rem';
            errorElement.style.display = 'none';
            inputElement.parentElement.appendChild(errorElement);
        }
        return errorElement;
    }

    errorMissatge = crearMensajeError(missatgeInput, 'error-edit-missatge');
    errorImatge = crearMensajeError(imatgeInput, 'error-edit-imatge');

    // Validación del mensaje
    function validarMissatge() {
        const valor = missatgeInput.value.trim();
        
        if (!valor || valor.length === 0) {
            // Permitir vacío si hay imagen
            if (imatgeInput.files.length > 0) {
                errorMissatge.style.display = 'none';
                missatgeInput.classList.remove('border-danger');
                return true;
            }
            errorMissatge.textContent = 'Debes escribir un comentario o adjuntar una imagen.';
            errorMissatge.style.display = 'block';
            missatgeInput.classList.add('border-danger');
            return false;
        }
        
        if (valor.length < 2) {
            errorMissatge.textContent = 'El comentario debe tener al menos 2 caracteres.';
            errorMissatge.style.display = 'block';
            missatgeInput.classList.add('border-danger');
            return false;
        }
        
        if (valor.length > 2000) {
            errorMissatge.textContent = 'El comentario no puede superar 2000 caracteres.';
            errorMissatge.style.display = 'block';
            missatgeInput.classList.add('border-danger');
            return false;
        }
        
        errorMissatge.style.display = 'none';
        missatgeInput.classList.remove('border-danger');
        return true;
    }

    // Validación de la imagen
    function validarImatge() {
        if (imatgeInput.files.length === 0) {
            errorImatge.style.display = 'none';
            imatgeInput.classList.remove('border-danger');
            return true;
        }

        const file = imatgeInput.files[0];
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        const maxSize = 4 * 1024 * 1024; // 4MB

        if (!allowedTypes.includes(file.type)) {
            errorImatge.textContent = 'La imagen debe ser JPG, JPEG, PNG, GIF o WEBP.';
            errorImatge.style.display = 'block';
            imatgeInput.classList.add('border-danger');
            return false;
        }

        if (file.size > maxSize) {
            errorImatge.textContent = 'La imagen no puede superar 4MB.';
            errorImatge.style.display = 'block';
            imatgeInput.classList.add('border-danger');
            return false;
        }

        errorImatge.style.display = 'none';
        imatgeInput.classList.remove('border-danger');
        return true;
    }

    // Comprobar todos los campos y habilitar/deshabilitar el botón
    function comprobarFormulario() {
        const missatgeValido = validarMissatge();
        const imatgeValido = validarImatge();
        
        // El formulario es válido si tiene mensaje o imagen (o ambos) y todos son válidos
        const tieneContenido = missatgeInput.value.trim().length > 0 || imatgeInput.files.length > 0;
        const formularioValido = tieneContenido && missatgeValido && imatgeValido;
        
        submitBtn.disabled = !formularioValido;
        
        if (formularioValido) {
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
        } else {
            submitBtn.style.opacity = '0.6';
            submitBtn.style.cursor = 'not-allowed';
        }
    }

    // Event listeners
    missatgeInput.addEventListener('input', comprobarFormulario);
    missatgeInput.addEventListener('blur', validarMissatge);
    
    imatgeInput.addEventListener('change', function() {
        validarImatge();
        comprobarFormulario();
    });

    // Validación al abrir el modal
    const modal = document.getElementById('modalEditarComentario');
    if (modal) {
        modal.addEventListener('shown.bs.modal', function() {
            // Resetear errores
            errorMissatge.style.display = 'none';
            errorImatge.style.display = 'none';
            missatgeInput.classList.remove('border-danger');
            imatgeInput.classList.remove('border-danger');
            
            // Comprobar estado inicial
            setTimeout(() => {
                comprobarFormulario();
            }, 100);
        });
    }

    // Validación al enviar
    form.addEventListener('submit', function(e) {
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
    });

    // Comprobar estado inicial
    comprobarFormulario();
});
