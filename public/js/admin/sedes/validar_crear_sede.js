function iniciarValidacionCrearSede() {
    console.log("Inicializando validación Crear Sede...");
    const eNombre = document.getElementById("error-nombre");
    const sNombre = document.getElementById("disponibilidad-nombre");
    const eResponsable = document.getElementById("error-responsable");
    const eDescripcion = document.getElementById("error-descripcion");

    const nombreInput = document.getElementById("nombre-sede");
    const responsableInput = document.getElementById("responsable-sede");
    const descripcionInput = document.getElementById("descripcion-sede");
    const botonEnviar = document.getElementById("btn-enviar-sede");

    let timeoutNombre = null;
    let nombreDisponible = false;

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

        fetch(`/admin/sedes/check-nombre?nom=${encodeURIComponent(valor)}`)
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
            .catch(err => console.error("Error comprobando nombre:", err));
    }

    function comprobarResponsable() {
        if (!responsableInput) return;
        if (responsableInput.value.trim() === "") {
            eResponsable.innerText = "El responsable es obligatorio.";
        } else {
            eResponsable.innerText = "";
        }
    }

    function comprobarDescripcion() {
        if (!descripcionInput) return;
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

// Inicializar cuando el DOM esté listo
if (document.readyState === "complete" || document.readyState === "interactive") {
    console.log("DOM listo, iniciando validación crear sede.");
    iniciarValidacionCrearSede();
} else {
    document.addEventListener("DOMContentLoaded", () => {
        console.log("DOMContentLoaded, iniciando validación crear sede.");
        iniciarValidacionCrearSede();
    });
}

window.iniciarValidacionCrearSede = iniciarValidacionCrearSede;
