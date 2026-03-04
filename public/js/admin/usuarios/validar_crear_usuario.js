function iniciarValidacionCrearUsuario() {
    // Referencias a mensajes de error/estado
    const eNombre = document.getElementById("error-nombre");
    const eUsuario = document.getElementById("error-username");
    const eEmail = document.getElementById("error-email");
    const ePassword = document.getElementById("error-password");
    const ePasswordConfirmation = document.getElementById("error-password-confirmation");
    const eSede = document.getElementById("error-sede_id");
    const eRol = document.getElementById("error-rol");
    const eGestorSede = document.getElementById("error-gestor-sede");

    const sUsuario = document.getElementById("disponibilidad-username");
    const sEmail = document.getElementById("disponibilidad-email");

    // Referencias a inputs
    const nombreInput = document.getElementById("nombre-usuario");
    const usuarioInput = document.getElementById("username");
    const emailInput = document.getElementById("email-usuario");
    const passwordInput = document.getElementById("password-usuario");
    const passwordConfirmationInput = document.getElementById("password-confirmation-usuario");
    const sedeInput = document.getElementById("sede-usuario");
    const rolInput = document.getElementById("rol-usuario");
    const botonEnviar = document.getElementById("boton-enviar");

    // Variables para debounce y disponibilidad
    let timeoutUsuario = null;
    let timeoutEmail = null;
    let usuarioDisponible = false;
    let emailDisponible = false;
    let sedeGestorDisponible = true;

    if (!nombreInput) return;

    // Listeners
    nombreInput.oninput = comprobarNombre;
    nombreInput.onblur = comprobarNombre;

    usuarioInput.oninput = () => {
        clearTimeout(timeoutUsuario);
        timeoutUsuario = setTimeout(comprobarUsuario, 100);
    };
    usuarioInput.onblur = comprobarUsuario;

    emailInput.oninput = () => {
        clearTimeout(timeoutEmail);
        timeoutEmail = setTimeout(comprobarEmail, 100);
    };
    emailInput.onblur = comprobarEmail;

    passwordInput.oninput = comprobarPassword;
    passwordInput.onblur = comprobarPassword;

    passwordConfirmationInput.oninput = comprobarPasswordConfirmation;
    passwordConfirmationInput.onblur = comprobarPasswordConfirmation;

    sedeInput.onchange = comprobarSede;
    sedeInput.oninput = comprobarSede;
    sedeInput.onblur = comprobarSede;

    rolInput.onchange = comprobarRol;
    rolInput.oninput = comprobarRol;
    rolInput.onblur = comprobarRol;

    function comprobarBoton() {
        const nombre = nombreInput.value.trim();
        const usuario = usuarioInput.value.trim();
        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();
        const passwordConfirmation = passwordConfirmationInput.value.trim();
        const sede = sedeInput.value;
        const rol = rolInput.value;

        const emailFormato = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        let nombreValido = nombre !== "" && nombre.length >= 2;
        let usuarioValido = usuario !== "" && usuario.length >= 3 && usuarioDisponible;
        let emailValido = email !== "" && emailFormato.test(email) && emailDisponible;
        let passwordValido = password !== "" && password.length >= 6;
        let passwordConfirmationValido = passwordConfirmation !== "" && password === passwordConfirmation;
        let sedeValido = sede !== "";
        let rolValido = rol !== "";

        if (rol === "gestor" && sede !== "") {
            // La validación de gestor_sede se hace asíncronamente, usamos la variable de estado
        } else {
            sedeGestorDisponible = true;
            eGestorSede.innerText = "";
        }

        if (nombreValido && usuarioValido && emailValido && passwordValido && passwordConfirmationValido && sedeValido && rolValido && sedeGestorDisponible) {
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
        if (valor.length < 3) {
            eNombre.innerText = "El nombre tiene que tener minimo 3 caracteres.";
            comprobarBoton();
            return;
        }

        eNombre.innerText = "";
        comprobarBoton();
    }

    function comprobarUsuario() {
        const valor = usuarioInput.value.trim();
        if (valor === "") {
            eUsuario.innerText = "El nombre de usuario es obligatorio.";
            sUsuario.innerText = "";
            comprobarBoton();
            return;
        }
        if (valor.length < 3) {
            eUsuario.innerText = "El nombre de usuario tiene que tener minimo 3 caracteres.";
            sUsuario.innerText = "";
            usuarioDisponible = false;
            comprobarBoton();
            return;
        }
        // Limpiamos el error de longitud antes de ir al servidor
        eUsuario.innerText = "";

        fetch(`/admin/usuarios/check-username?username=${encodeURIComponent(valor)}`)
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
            .catch(err => console.error("Error comprobando usuario:", err));
    }

    function comprobarEmail() {
        const valor = emailInput.value.trim();
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (valor === "") {
            eEmail.innerText = "El correo electrónico es obligatorio.";
            sEmail.innerText = "";
            comprobarBoton();
            return;
        }
        if (!regex.test(valor)) {
            eEmail.innerText = "Introduce un correo válido.";
            sEmail.innerText = "";
            emailDisponible = false;
            comprobarBoton();
            return;
        }
        // Limpiamos el error de formato antes de ir al servidor
        eEmail.innerText = "";

        fetch(`/admin/usuarios/check-email?email=${encodeURIComponent(valor)}`)
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
            .catch(err => console.error("Error comprobando email:", err));
    }

    function comprobarPassword() {
        const valor = passwordInput.value.trim();
        if (valor === "") {
            ePassword.innerText = "La contraseña es obligatoria.";
            if (passwordConfirmationInput.value.trim() !== "") comprobarPasswordConfirmation();
            comprobarBoton();
            return;
        }
        if (valor.length < 6) {
            ePassword.innerText = "Mínimo 6 caracteres.";
            if (passwordConfirmationInput.value.trim() !== "") comprobarPasswordConfirmation();
            comprobarBoton();
            return;
        }

        ePassword.innerText = "";
        if (passwordConfirmationInput.value.trim() !== "") comprobarPasswordConfirmation();
        comprobarBoton();
    }

    function comprobarPasswordConfirmation() {
        const p1 = passwordInput.value.trim();
        const p2 = passwordConfirmationInput.value.trim();
        if (p2 === "") {
            ePasswordConfirmation.innerText = "Debes confirmar la contraseña.";
            comprobarBoton();
            return;
        }
        if (p1 !== p2) {
            ePasswordConfirmation.innerText = "Las contraseñas no coinciden.";
            comprobarBoton();
            return;
        }

        ePasswordConfirmation.innerText = "";
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

        if (rol !== "gestor" || sedeId === "") {
            sedeGestorDisponible = true;
            eGestorSede.innerText = "";
            comprobarBoton();
            return;
        }

        fetch(`/admin/usuarios/check-gestor?sede_id=${sedeId}`)
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
            .catch(err => console.error("Error comprobando gestor de sede:", err));
    }

    // Validar inicialmente si hay valores (por ejemplo, al volver de un error de Laravel)
    if (nombreInput.value !== "") comprobarNombre();
    if (usuarioInput.value !== "") comprobarUsuario();
    if (emailInput.value !== "") comprobarEmail();
    if (passwordInput.value !== "") comprobarPassword();
    if (sedeInput.value !== "") comprobarSede();
    if (rolInput.value !== "") comprobarRol();

    comprobarBoton();
}

document.onreadystatechange = function () {
    if (document.readyState === "complete") {
        iniciarValidacionCrearUsuario();
    }
};

window.iniciarValidacionCrearUsuario = iniciarValidacionCrearUsuario;
