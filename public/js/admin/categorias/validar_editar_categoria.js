function iniciarValidacionEditarCategoria() {
    console.log("Inicializando validación Editar Categoría...");
    const eNombre = document.getElementById("error-edit-nombre");
    const sNombre = document.getElementById("disponibilidad-edit-nombre");
    const eDescripcion = document.getElementById("error-edit-descripcion");

    const categoriaIdInput = document.getElementById("edit-categoria-id");
    const nombreInput = document.getElementById("edit-nombre-categoria");
    const descripcionInput = document.getElementById("edit-descripcion-categoria");
    const botonEnviar = document.getElementById("edit-btn-enviar-categoria");

    let timeoutNombre = null;
    let nombreDisponible = true; // Por defecto true al editar existente

    if (!nombreInput || !botonEnviar) return;

    // Listeners
    nombreInput.oninput = () => {
        clearTimeout(timeoutNombre);
        timeoutNombre = setTimeout(comprobarNombre, 300);
    };
    nombreInput.onblur = comprobarNombre;

    if (descripcionInput) {
        descripcionInput.oninput = () => {
            comprobarDescripcion();
            comprobarBoton();
        };
        descripcionInput.onblur = comprobarDescripcion;
    }

    function comprobarBoton() {
        const nombre = nombreInput.value.trim();
        const descripcion = descripcionInput ? descripcionInput.value.trim() : "";

        let nombreValido = nombre !== "" && nombre.length >= 2 && nombreDisponible;
        let descripcionValida = descripcion !== "";

        if (nombreValido && descripcionValida) {
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

        const excludeId = categoriaIdInput ? categoriaIdInput.value : '';

        fetch(`/admin/categorias/check-nom?nom=${encodeURIComponent(valor)}&exclude_id=${excludeId}`)
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
    if (descripcionInput && descripcionInput.value !== "") comprobarDescripcion();
    comprobarBoton();
}

// Exponer globalmente
window.iniciarValidacionEditarCategoria = iniciarValidacionEditarCategoria;
