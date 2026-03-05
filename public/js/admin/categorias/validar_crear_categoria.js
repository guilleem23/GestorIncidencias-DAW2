function iniciarValidacionCrearCategoria() {
    console.log("Inicializando validación Crear Categoría...");
    const eNombre = document.getElementById("error-nombre");
    const sNombre = document.getElementById("disponibilidad-nombre");
    const eDescripcion = document.getElementById("error-descripcion");

    const nombreInput = document.getElementById("nombre-categoria");
    const descripcionInput = document.getElementById("descripcion-categoria");
    const botonEnviar = document.getElementById("btn-enviar-categoria");

    let timeoutNombre = null;
    let nombreDisponible = false;

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

        fetch(`/admin/categorias/check-nom?nom=${encodeURIComponent(valor)}`)
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
    if (descripcionInput && descripcionInput.value !== "") comprobarDescripcion();
    comprobarBoton();
}

// Inicializar cuando el DOM esté listo
if (document.readyState === "complete" || document.readyState === "interactive") {
    console.log("DOM listo, iniciando validación crear categoría.");
    iniciarValidacionCrearCategoria();
} else {
    document.addEventListener('DOMContentLoaded', function () {
        console.log("Window loaded, iniciando validación crear categoría.");
        iniciarValidacionCrearCategoria();
    });
}

window.iniciarValidacionCrearCategoria = iniciarValidacionCrearCategoria;
