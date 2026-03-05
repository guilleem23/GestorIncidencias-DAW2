function iniciarValidacionEditarIncidencia() {
    // Referencias a mensajes de error
    const eTitol = document.getElementById("error-titol");
    const eDescripcio = document.getElementById("error-descripcio");
    const eCategoria = document.getElementById("error-categoria");
    const eSubcategoria = document.getElementById("error-subcategoria");
    const eEstat = document.getElementById("error-estat");
    const ePrioritat = document.getElementById("error-prioritat");
    const eTecnicEstat = document.getElementById("error-tecnic-estat");

    // Referencias a inputs
    const titolInput = document.getElementById("titol");
    const descripcioInput = document.getElementById("descripcio");
    const categoriaInput = document.getElementById("categoria_id");
    const subcategoriaInput = document.getElementById("subcategoria_id");
    const estatInput = document.getElementById("estat");
    const prioritatInput = document.getElementById("prioritat");
    const tecnicInput = document.getElementById("tecnic_id");
    const botonGuardar = document.getElementById("btn-save-incidencia");

    // Si no existe el formulario, salir
    if (!titolInput || !botonGuardar) return;

    // Listeners
    titolInput.oninput = comprobarTitol;
    titolInput.onblur = comprobarTitol;

    descripcioInput.oninput = comprobarDescripcio;
    descripcioInput.onblur = comprobarDescripcio;

    categoriaInput.onchange = comprobarCategoria;
    categoriaInput.oninput = comprobarCategoria;
    categoriaInput.onblur = comprobarCategoria;

    subcategoriaInput.onchange = comprobarSubcategoria;
    subcategoriaInput.oninput = comprobarSubcategoria;
    subcategoriaInput.onblur = comprobarSubcategoria;

    estatInput.onchange = comprobarEstatYTecnico;
    estatInput.oninput = comprobarEstatYTecnico;
    estatInput.onblur = comprobarEstatYTecnico;

    prioritatInput.onchange = comprobarPrioritat;
    prioritatInput.oninput = comprobarPrioritat;
    prioritatInput.onblur = comprobarPrioritat;

    tecnicInput.onchange = comprobarEstatYTecnico;
    tecnicInput.oninput = comprobarEstatYTecnico;
    tecnicInput.onblur = comprobarEstatYTecnico;

    function comprobarBoton() {
        const titol = titolInput.value.trim();
        const descripcio = descripcioInput.value.trim();
        const categoria = categoriaInput.value;
        const subcategoria = subcategoriaInput.value;
        const estat = estatInput.value;
        const prioritat = prioritatInput.value;
        const tecnic = tecnicInput.value;

        let titolValido = titol !== "" && titol.length >= 3;
        let descripcioValida = descripcio !== "" && descripcio.length >= 10;
        let categoriaValida = categoria !== "";
        let subcategoriaValida = subcategoria !== "";
        let estatValido = estat !== "";
        let prioritatValida = prioritat !== "";

        // Validación lógica técnico-estado
        let tecnicEstatValido = true;
        if (tecnic !== "" && estat === "Sense assignar") {
            tecnicEstatValido = false;
        }
        if (tecnic === "" && estat !== "Sense assignar") {
            tecnicEstatValido = false;
        }

        if (titolValido && descripcioValida && categoriaValida && subcategoriaValida && estatValido && prioritatValida && tecnicEstatValido) {
            botonGuardar.disabled = false;
            botonGuardar.classList.remove("btn-disabled");
        } else {
            botonGuardar.disabled = true;
            botonGuardar.classList.add("btn-disabled");
        }
    }

    function comprobarTitol() {
        const valor = titolInput.value.trim();
        if (valor === "") {
            eTitol.innerText = "El título no puede estar vacío.";
            eTitol.style.display = "block";
            comprobarBoton();
            return;
        }
        if (valor.length < 3) {
            eTitol.innerText = "El título debe tener al menos 3 caracteres.";
            eTitol.style.display = "block";
            comprobarBoton();
            return;
        }

        eTitol.innerText = "";
        eTitol.style.display = "none";
        comprobarBoton();
    }

    function comprobarDescripcio() {
        const valor = descripcioInput.value.trim();
        if (valor === "") {
            eDescripcio.innerText = "La descripción no puede estar vacía.";
            eDescripcio.style.display = "block";
            comprobarBoton();
            return;
        }
        if (valor.length < 10) {
            eDescripcio.innerText = "La descripción debe tener al menos 10 caracteres.";
            eDescripcio.style.display = "block";
            comprobarBoton();
            return;
        }

        eDescripcio.innerText = "";
        eDescripcio.style.display = "none";
        comprobarBoton();
    }

    function comprobarCategoria() {
        if (categoriaInput.value === "") {
            eCategoria.innerText = "Debes seleccionar una categoría.";
            eCategoria.style.display = "block";
            comprobarBoton();
            return;
        }

        eCategoria.innerText = "";
        eCategoria.style.display = "none";
        comprobarBoton();
    }

    function comprobarSubcategoria() {
        if (subcategoriaInput.value === "") {
            eSubcategoria.innerText = "Debes seleccionar una subcategoría.";
            eSubcategoria.style.display = "block";
            comprobarBoton();
            return;
        }

        eSubcategoria.innerText = "";
        eSubcategoria.style.display = "none";
        comprobarBoton();
    }

    function comprobarEstatYTecnico() {
        const tecnic = tecnicInput.value;
        const estat = estatInput.value;

        // Validar estado
        if (estat === "") {
            eEstat.innerText = "Debes seleccionar un estado.";
            eEstat.style.display = "block";
            eTecnicEstat.innerText = "";
            eTecnicEstat.style.display = "none";
            comprobarBoton();
            return;
        }

        eEstat.innerText = "";
        eEstat.style.display = "none";

        // Validación lógica técnico-estado
        if (tecnic !== "" && estat === "Sense assignar") {
            eTecnicEstat.innerText = 'El estado no puede ser "Sin asignar" si hay un técnico asignado.';
            eTecnicEstat.style.display = "block";
            comprobarBoton();
            return;
        }

        if (tecnic === "" && estat !== "Sense assignar") {
            eTecnicEstat.innerText = 'Debe asignar un técnico si el estado no es "Sin asignar".';
            eTecnicEstat.style.display = "block";
            comprobarBoton();
            return;
        }

        eTecnicEstat.innerText = "";
        eTecnicEstat.style.display = "none";
        comprobarBoton();
    }

    function comprobarPrioritat() {
        if (prioritatInput.value === "") {
            ePrioritat.innerText = "Debes seleccionar una prioridad.";
            ePrioritat.style.display = "block";
            comprobarBoton();
            return;
        }

        ePrioritat.innerText = "";
        ePrioritat.style.display = "none";
        comprobarBoton();
    }

    // Validar inicialmente con valores del formulario
    if (titolInput.value !== "") comprobarTitol();
    if (descripcioInput.value !== "") comprobarDescripcio();
    if (categoriaInput.value !== "") comprobarCategoria();
    if (subcategoriaInput.value !== "") comprobarSubcategoria();
    if (estatInput.value !== "") comprobarEstatYTecnico();
    if (prioritatInput.value !== "") comprobarPrioritat();

    comprobarBoton();
}

// Exportar para uso cuando se carga dinámicamente el modal
window.iniciarValidacionEditarIncidencia = iniciarValidacionEditarIncidencia;
