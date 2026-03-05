function iniciarValidacionLogin() {
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const botonEnviar = document.getElementById("boton-enviar-login");

    const eEmail = document.getElementById("error-email");
    const ePassword = document.getElementById("error-password");

    if (!emailInput || !passwordInput || !botonEnviar) return;

    // Listeners
    emailInput.oninput = comprobarEmail;
    emailInput.onblur = comprobarEmail;

    passwordInput.oninput = comprobarPassword;
    passwordInput.onblur = comprobarPassword;

    function comprobarBoton() {
        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();
        const emailFormato = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        const emailValido = email !== "" && emailFormato.test(email);
        const passwordValido = password !== "" && password.length >= 6;

        if (emailValido && passwordValido) {
            botonEnviar.disabled = false;
            botonEnviar.classList.remove("btn-login-disabled");
        } else {
            botonEnviar.disabled = true;
            botonEnviar.classList.add("btn-login-disabled");
        }
    }

    function comprobarEmail() {
        const valor = emailInput.value.trim();
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (valor === "") {
            eEmail.innerText = "El correo electrónico es obligatorio.";
            eEmail.classList.add("visible");
            emailInput.classList.add("input-error");
        } else if (!regex.test(valor)) {
            eEmail.innerText = "Introduce un correo válido.";
            eEmail.classList.add("visible");
            emailInput.classList.add("input-error");
        } else {
            eEmail.innerText = "";
            eEmail.classList.remove("visible");
            emailInput.classList.remove("input-error");
        }
        comprobarBoton();
    }

    function comprobarPassword() {
        const valor = passwordInput.value.trim();

        if (valor === "") {
            ePassword.innerText = "La contraseña es obligatoria.";
            ePassword.classList.add("visible");
            passwordInput.classList.add("input-error");
        } else if (valor.length < 6) {
            ePassword.innerText = "Mínimo 6 caracteres.";
            ePassword.classList.add("visible");
            passwordInput.classList.add("input-error");
        } else {
            ePassword.innerText = "";
            ePassword.classList.remove("visible");
            passwordInput.classList.remove("input-error");
        }
        comprobarBoton();
    }

    // Inicializar estado
    comprobarBoton();
}

document.addEventListener('DOMContentLoaded', iniciarValidacionLogin);
