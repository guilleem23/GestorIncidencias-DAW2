function iniciarValidacionEditarSede() {
    console.log("Inicializando validación Editar Sede...");
    const eNombre = document.getElementById("error-edit-nombre");
    const sNombre = document.getElementById("disponibilidad-edit-nombre");
    const eResponsable = document.getElementById("error-edit-responsable");
    const eDescripcion = document.getElementById("error-edit-descripcion");

    const sedeIdInput = document.getElementById("edit-sede-id");
    const nombreInput = document.getElementById("edit-nombre-sede");
    const responsableInput = document.getElementById("edit-responsable-sede");
    const descripcionInput = document.getElementById("edit-descripcion-sede");
    const botonEnviar = document.getElementById("edit-btn-enviar-sede");

    let timeoutNombre = null;
    let nombreDisponible = true; // Por defecto true al editar existente

    if (!nombreInput || !botonEnviar) return;

    // Listeners
    nombreInput.oninput = () => {
        clearTimeout(timeoutNombre);
        timeoutNombre = setTimeout(comprobarNombre, 300);
    };
    nombreInput.onblur = comprobarNombre;

    if (responsableInput) {
        responsableInput.oninput = () => {
            comprobarResponsable();
            comprobarBoton();
        };
        responsableInput.onblur = comprobarResponsable;
    }

    if (descripcionInput) {
        descripcionInput.oninput = () => {
            comprobarDescripcion();
            comprobarBoton();
        };
        descripcionInput.onblur = comprobarDescripcion;
    }

    function comprobarBoton() {
        const nombre = nombreInput.value.trim();
        const responsable = responsableInput ? responsableInput.value.trim() : "";
        const descripcion = descripcionInput ? descripcionInput.value.trim() : "";

        let nombreValido = nombre !== "" && nombre.length >= 2 && nombreDisponible;
        let responsableValido = responsable !== "";
        let descripcionValido = descripcion !== "";

        if (nombreValido && responsableValido && descripcionValido) {
            botonEnviar.disabled = false;
            botonEnviar.classList.remove("btn-login-desabilitado");
        } else {
            botonEnviar.disabled = true;
            botonEnviar.classList.add("btn-login-desabilitado");
        }
    }

    function comprobarNombre() {
        const valor = nombreInput.value.trim();
        if (valor === "") {
            eNombre.innerText = "El nombre es obligatorio.";
            sNombre.innerText = "";
            nombreDisponible = false;
            comprobarBoton();
            return;
        }
        if (valor.length < 2) {
            eNombre.innerText = "Mínimo 2 caracteres.";
            sNombre.innerText = "";
            nombreDisponible = false;
            comprobarBoton();
            return;
        }

        eNombre.innerText = "";

        const excludeId = sedeIdInput ? sedeIdInput.value : '';

        fetch(`/admin/sedes/check-nombre?nom=${encodeURIComponent(valor)}&exclude_id=${excludeId}`)
            .then(r => r.json())
            .then(data => {
                if (data.disponible) {
                    eNombre.innerText = "";
                    sNombre.innerText = "Disponible.";
                    sNombre.style.color = "#10b981";
                    nombreDisponible = true;
                } else {
                    sNombre.innerText = "";
                    eNombre.innerText = "Ya está en uso.";
                    nombreDisponible = false;
                }
                comprobarBoton();
            })
            .catch(err => console.error("Error comprobando nombre (edit):", err));
    }

    function comprobarResponsable() {
        if (!responsableInput || !eResponsable) return;
        if (responsableInput.value.trim() === "") {
            eResponsable.innerText = "El responsable es obligatorio.";
        } else {
            eResponsable.innerText = "";
        }
    }

    function comprobarDescripcion() {
        if (!descripcionInput || !eDescripcion) return;
        if (descripcionInput.value.trim() === "") {
            eDescripcion.innerText = "La descripción es obligatoria.";
        } else {
            eDescripcion.innerText = "";
        }
    }

    // Validar inicialmente
    if (nombreInput.value !== "") comprobarNombre();
    if (responsableInput && responsableInput.value !== "") comprobarResponsable();
    if (descripcionInput && descripcionInput.value !== "") comprobarDescripcion();
    comprobarBoton();
}

// Exponer globalmente
window.iniciarValidacionEditarSede = iniciarValidacionEditarSede;
