function iniciarValidacionCrearSubcategoria() {
    console.log("Inicializando validación Crear Subcategoría...");
    const eCategoria = document.getElementById("error-categoria");
    const eNombre = document.getElementById("error-nombre-sub");
    const sNombre = document.getElementById("disponibilidad-nombre-sub");
    const eDescripcion = document.getElementById("error-descripcion-sub");

    const categoriaSelect = document.getElementById("categoria-select");
    const nombreInput = document.getElementById("nombre-subcategoria");
    const descripcionInput = document.getElementById("descripcion-subcategoria");
    const botonEnviar = document.getElementById("btn-enviar-subcategoria");

    let timeoutNombre = null;
    let nombreDisponible = false;

    if (!nombreInput || !botonEnviar || !categoriaSelect) return;

    // Listeners
    categoriaSelect.onchange = () => {
        comprobarCategoria();
        // Revalidar nombre cuando cambia la categoría
        if (nombreInput.value.trim() !== "") {
            clearTimeout(timeoutNombre);
            timeoutNombre = setTimeout(comprobarNombre, 300);
        }
        comprobarBoton();
    };

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
        const categoria = categoriaSelect.value;
        const nombre = nombreInput.value.trim();
        const descripcion = descripcionInput ? descripcionInput.value.trim() : "";

        let categoriaValida = categoria !== "";
        let nombreValido = nombre !== "" && nombre.length >= 2 && nombreDisponible;
        let descripcionValida = descripcion !== "";

        if (categoriaValida && nombreValido && descripcionValida) {
            botonEnviar.disabled = false;
            botonEnviar.classList.remove("btn-login-desabilitado");
        } else {
            botonEnviar.disabled = true;
            botonEnviar.classList.add("btn-login-desabilitado");
        }
    }

    function comprobarCategoria() {
        if (categoriaSelect.value === "") {
            eCategoria.innerText = "Debes seleccionar una categoría.";
        } else {
            eCategoria.innerText = "";
        }
    }

    function comprobarNombre() {
        const valor = nombreInput.value.trim();
        const categoriaId = categoriaSelect.value;

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

        if (!categoriaId) {
            eNombre.innerText = "Primero selecciona una categoría.";
            sNombre.innerText = "";
            nombreDisponible = false;
            comprobarBoton();
            return;
        }

        eNombre.innerText = "";

        fetch(`/admin/subcategorias/check-nom?nom=${encodeURIComponent(valor)}&categoria_id=${categoriaId}`)
            .then(r => r.json())
            .then(data => {
                if (data.disponible) {
                    eNombre.innerText = "";
                    sNombre.innerText = "Disponible en esta categoría.";
                    sNombre.style.color = "#10b981";
                    nombreDisponible = true;
                } else {
                    sNombre.innerText = "";
                    eNombre.innerText = "Ya existe en esta categoría.";
                    nombreDisponible = false;
                }
                comprobarBoton();
            })
            .catch(err => console.error("Error comprobando nombre subcategoría:", err));
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
    comprobarCategoria();
    if (nombreInput.value !== "") comprobarNombre();
    if (descripcionInput && descripcionInput.value !== "") comprobarDescripcion();
    comprobarBoton();
}

// Inicializar cuando el DOM esté listo
if (document.readyState === "complete" || document.readyState === "interactive") {
    console.log("DOM listo, iniciando validación crear subcategoría.");
    iniciarValidacionCrearSubcategoria();
} else {
    document.addEventListener('DOMContentLoaded', function () {
        console.log("Window loaded, iniciando validación crear subcategoría.");
        iniciarValidacionCrearSubcategoria();
    });
}

window.iniciarValidacionCrearSubcategoria = iniciarValidacionCrearSubcategoria;
