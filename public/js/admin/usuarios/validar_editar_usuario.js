function iniciarValidacionEditarUsuario() {
    // Referencias a mensajes de error/estado
    const eNombre = document.getElementById("error-edit-nombre");
    const eUsuario = document.getElementById("error-edit-username");
    const eEmail = document.getElementById("error-edit-email");
    const ePassword = document.getElementById("error-edit-password");
    const eConfirmPassword = document.getElementById("error-edit-password-confirmation");
    const eSede = document.getElementById("error-edit-sede_id");
    const eRol = document.getElementById("error-edit-rol");
    const eActivo = document.getElementById("error-edit-activo");
    const eGestorSede = document.getElementById("error-edit-gestor-sede");

    const sUsuario = document.getElementById("disponibilidad-edit-username");
    const sEmail = document.getElementById("disponibilidad-edit-email");

    // Referencias a inputs
    const userIdInput = document.getElementById("edit-user-id");
    const nombreInput = document.getElementById("edit-nombre-usuario");
    const usuarioInput = document.getElementById("edit-username-usuario");
    const emailInput = document.getElementById("edit-email-usuario");
    const passwordInput = document.getElementById("edit-password-usuario");
    const confirmPasswordInput = document.getElementById("edit-password-confirmation-usuario");
    const sedeInput = document.getElementById("edit-sede-usuario");
    const rolInput = document.getElementById("edit-rol-usuario");
    const activoInput = document.getElementById("edit-activo-usuario");
    const botonEnviar = document.getElementById("edit-boton-enviar");

    // Variables para debounce y disponibilidad
    let timeoutUsuario = null;
    let timeoutEmail = null;
    let usuarioDisponible = true; // Por defecto true al editar un usuario existente
    let emailDisponible = true;
    let sedeGestorDisponible = true;

    if (!nombreInput) return;

    // Listeners
    nombreInput.oninput = comprobarNombre;

    usuarioInput.oninput = () => {
        clearTimeout(timeoutUsuario);
        timeoutUsuario = setTimeout(comprobarUsuario, 100);
    };

    emailInput.oninput = () => {
        clearTimeout(timeoutEmail);
        timeoutEmail = setTimeout(comprobarEmail, 100);
    };

    passwordInput.oninput = () => {
        comprobarPassword();
        comprobarConfirmPassword();
    };

    confirmPasswordInput.oninput = comprobarConfirmPassword;

    sedeInput.onchange = comprobarSede;
    rolInput.onchange = comprobarRol;
    activoInput.onchange = comprobarActivo;

    function comprobarBoton() {
        const nombre = nombreInput.value.trim();
        const usuario = usuarioInput.value.trim();
        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();
        const sede = sedeInput.value;
        const rol = rolInput.value;
        const activo = activoInput.value;

        const emailFormato = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        let nombreValido = nombre !== "" && nombre.length >= 2;
        let usuarioValido = usuario !== "" && usuario.length >= 3 && usuarioDisponible;
        let emailValido = email !== "" && emailFormato.test(email) && emailDisponible;
        let passwordValido = password === "" || password.length >= 6;
        let confirmPasswordValido = true;

        if (password !== "") {
            confirmPasswordValido = password === confirmPasswordInput.value.trim();
        } else if (confirmPasswordInput.value.trim() !== "") {
            confirmPasswordValido = false;
        }

        let sedeValido = sede !== "";
        let rolValido = rol !== "";
        let activoValido = activo !== "";

        if (rol === "gestor" && sede !== "") {
            // La validación se hace asíncronamente
        } else {
            sedeGestorDisponible = true;
            eGestorSede.innerText = "";
        }

        if (nombreValido && usuarioValido && emailValido && passwordValido && confirmPasswordValido && sedeValido && rolValido && activoValido && sedeGestorDisponible) {
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
            eNombre.innerText = "El nombre no puede estar vacío.";
            comprobarBoton();
            return;
        }
        if (valor.length < 2) {
            eNombre.innerText = "Mínimo 2 caracteres.";
            comprobarBoton();
            return;
        }

        eNombre.innerText = "";
        comprobarBoton();
    }

    function comprobarUsuario() {
        const valor = usuarioInput.value.trim();
        if (valor === "" || valor.length < 3) {
            let msg = valor === "" ? "El usuario es obligatorio." : "Mínimo 3 caracteres.";
            eUsuario.innerText = msg;
            sUsuario.innerText = "";
            usuarioDisponible = false;
            comprobarBoton();
            return;
        }
        // Limpiamos el error de longitud antes de ir al servidor
        eUsuario.innerText = "";

        fetch(`/admin/usuarios/check-username?username=${encodeURIComponent(valor)}&exclude_id=${userIdInput.value}`)
            .then(r => r.json())
            .then(data => {
                if (data.disponible) {
                    eUsuario.innerText = "";
                    sUsuario.innerText = "Disponible.";
                    usuarioDisponible = true;
                } else {
                    sUsuario.innerText = "";
                    eUsuario.innerText = "Ya está en uso.";
                    usuarioDisponible = false;
                }
                comprobarBoton();
            })
            .catch(err => { });
    }

    function comprobarEmail() {
        const valor = emailInput.value.trim();
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (valor === "" || !regex.test(valor)) {
            let msg = valor === "" ? "El email es obligatorio." : "Email inválido.";
            eEmail.innerText = msg;
            sEmail.innerText = "";
            emailDisponible = false;
            comprobarBoton();
            return;
        }
        // Limpiamos el error de formato antes de ir al servidor
        eEmail.innerText = "";

        fetch(`/admin/usuarios/check-email?email=${encodeURIComponent(valor)}&exclude_id=${userIdInput.value}`)
            .then(r => r.json())
            .then(data => {
                if (data.disponible) {
                    eEmail.innerText = "";
                    sEmail.innerText = "Disponible.";
                    emailDisponible = true;
                } else {
                    sEmail.innerText = "";
                    eEmail.innerText = "Ya está en uso.";
                    emailDisponible = false;
                }
                comprobarBoton();
            })
            .catch(err => console.error("Error comprobando email (edit):", err));
    }

    function comprobarPassword() {
        const valor = passwordInput.value.trim();
        if (valor !== "" && valor.length < 6) {
            ePassword.innerText = "Mínimo 6 caracteres.";
            comprobarBoton();
            return;
        }

        ePassword.innerText = "";
        comprobarBoton();
    }

    function comprobarConfirmPassword() {
        const password = passwordInput.value.trim();
        const confirmacion = confirmPasswordInput.value.trim();

        if (password !== "" && confirmacion === "") {
            eConfirmPassword.innerText = "Repite la contraseña.";
        } else if (password !== "" && password !== confirmacion) {
            eConfirmPassword.innerText = "Las contraseñas no coinciden.";
        } else if (password === "" && confirmacion !== "") {
            eConfirmPassword.innerText = "La contraseña está vacía.";
        } else {
            eConfirmPassword.innerText = "";
        }
        comprobarBoton();
    }

    function comprobarSede() {
        if (sedeInput.value === "") {
            eSede.innerText = "Selecciona una sede.";
            comprobarBoton();
            return;
        }

        eSede.innerText = "";
        if (rolInput.value === "gestor") {
            comprobarGestorSede();
        } else {
            sedeGestorDisponible = true;
            eGestorSede.innerText = "";
            comprobarBoton();
        }
    }

    function comprobarRol() {
        if (rolInput.value === "") {
            eRol.innerText = "Selecciona un rol.";
            comprobarBoton();
            return;
        }

        eRol.innerText = "";
        if (rolInput.value === "gestor") {
            comprobarGestorSede();
        } else {
            sedeGestorDisponible = true;
            eGestorSede.innerText = "";
            comprobarBoton();
        }
    }

    function comprobarGestorSede() {
        const sedeId = sedeInput.value;
        const rol = rolInput.value;
        const userId = userIdInput.value;

        if (rol !== "gestor" || sedeId === "") {
            sedeGestorDisponible = true;
            eGestorSede.innerText = "";
            comprobarBoton();
            return;
        }

        fetch(`/admin/usuarios/check-gestor?sede_id=${sedeId}&exclude_user_id=${userId}`)
            .then(r => r.json())
            .then(data => {
                if (data.existe) {
                    eGestorSede.innerText = "Esta sede ya tiene un gestor asignado.";
                    sedeGestorDisponible = false;
                } else {
                    eGestorSede.innerText = "";
                    sedeGestorDisponible = true;
                }
                comprobarBoton();
            })
            .catch(err => console.error("Error comprobando gestor de sede (edit):", err));
    }

    function comprobarActivo() {
        if (activoInput.value === "") {
            eActivo.innerText = "Selecciona un estado.";
            comprobarBoton();
            return;
        }

        eActivo.innerText = "";
        comprobarBoton();
    }

    // Validar inicialmente si hay valores
    if (nombreInput.value !== "") comprobarNombre();
    if (usuarioInput.value !== "") comprobarUsuario();
    if (emailInput.value !== "") comprobarEmail();
    if (passwordInput.value !== "") comprobarPassword();
    if (sedeInput.value !== "") comprobarSede();
    if (rolInput.value !== "") comprobarRol();
    if (activoInput.value !== "") comprobarActivo();

    comprobarBoton();
}

window.iniciarValidacionEditarUsuario = iniciarValidacionEditarUsuario;
