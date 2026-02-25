window.onload = function () {
	// Elementos de error
	const eNombre = document.getElementById("error-nombre");
	const eEmail = document.getElementById("error-email");
	const disponibilidadEmail = document.getElementById("disponibilidad-email");
	const ePassword = document.getElementById("error-password");
	const ePasswordConfirmation = document.getElementById("error-password-confirmation");
	const eSede = document.getElementById("error-sede_id");
	const eRol = document.getElementById("error-rol");

	// Inputs
	const nombreInput = document.getElementById("nombre-usuario");
	const emailInput = document.getElementById("email-usuario");
	const passwordInput = document.getElementById("password-usuario");
	const passwordConfirmationInput = document.getElementById("password-confirmation-usuario");
	const sedeInput = document.getElementById("sede-usuario");
	const rolInput = document.getElementById("rol-usuario");
	const botonEnviar = document.getElementById("boton-enviar");

	comprobarBoton();

	nombreInput.onblur = comprobarNombre;
	emailInput.onblur = comprobarEmail;
	passwordInput.onblur = comprobarPassword;
	passwordConfirmationInput.onblur = comprobarPasswordConfirmation;
	sedeInput.onblur = comprobarSede;
	rolInput.onblur = comprobarRol;

	nombreInput.oninput = comprobarNombre;
	emailInput.oninput = comprobarEmail;
	passwordInput.oninput = function () {
		comprobarPassword();
		if (passwordConfirmationInput.value.trim() !== '') {
			comprobarPasswordConfirmation();
		}
	};
	passwordConfirmationInput.oninput = comprobarPasswordConfirmation;
	sedeInput.oninput = comprobarSede;
	rolInput.oninput = comprobarRol;

	function comprobarBoton() {
		const nombre = nombreInput.value.trim();
		const email = emailInput.value.trim();
		const password = passwordInput.value.trim();
		const passwordConfirmation = passwordConfirmationInput.value.trim();
		const sede = sedeInput.value;
		const rol = rolInput.value;
		const emailFormato = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

		let nombreValido = nombre !== '' && nombre.length >= 2;
		let emailValido = email !== '' && emailFormato.test(email);
		let passwordValido = password !== '' && password.length >= 6;
		let passwordConfirmationValido = passwordConfirmation !== '' && password === passwordConfirmation;
		let sedeValida = sede !== '' && sede !== null;
		let rolValido = rol !== '' && rol !== null;

		if (nombreValido && emailValido && passwordValido && passwordConfirmationValido && sedeValida && rolValido) {
			botonEnviar.disabled = false;
			botonEnviar.classList.remove("btn-login-desabilitado");
		} else {
			botonEnviar.disabled = true;
			botonEnviar.classList.add("btn-login-desabilitado");
		}
	}

	function comprobarNombre() {
		const nombre = nombreInput.value.trim();
		if (eNombre) {
			if (nombre === '') {
				eNombre.innerText = 'El nombre no puede estar vacío.';
			} else if (nombre.length < 2) {
				eNombre.innerText = 'El nombre debe tener al menos 2 caracteres.';
			} else {
				eNombre.innerText = '';
			}
		}
		comprobarBoton();
	}

	function comprobarEmail() {
		const email = emailInput.value.trim();
		const emailFormato = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

		if (eEmail) {
			if (email === '') {
				eEmail.innerText = 'El correo electrónico no puede estar vacío.';
				disponibilidadEmail.textContent = '';
			} else if (!emailFormato.test(email)) {
				eEmail.innerText = 'Por favor, introduce un correo electrónico válido.';
				disponibilidadEmail.textContent = '';
			} else {
				eEmail.innerText = '';
				disponibilidadEmail.innerText = '';
				fetch('/admin/usuarios/check-email?email=' + encodeURIComponent(email))
					.then(respuesta => respuesta.json())
					.then(data => {
						if (data.disponible) {
							$disponibilida = true;
							$errorcomprobacion = false;
							disponibilidadEmail.innerText = 'El correo electrónico está disponible.';
						} else {
							$disponibilida = false;
							$errorcomprobacion = false;
							eEmail.innerText = 'El correo electrónico ya está en uso.';
							disponibilidadEmail.innerText = '';
						}
					})
					.catch(error => {
						$errorcomprobacion = true;
						eEmail.innerText = 'Error al comprobar la disponibilidad del correo electrónico.';
						disponibilidadEmail.innerText = '';
						console.log('Error AJAX email:', error);
					});
			}
		}
		comprobarBoton();
	}

	function comprobarPassword() {
		const password = passwordInput.value.trim();
		if (ePassword) {
			if (password === '') {
				ePassword.innerText = 'La contraseña no puede estar vacía.';
			} else if (password.length < 6) {
				ePassword.innerText = 'La contraseña debe tener al menos 6 caracteres.';
			} else {
				ePassword.innerText = '';
			}
		}
		comprobarBoton();
	}

	function comprobarPasswordConfirmation() {
		const password = passwordInput.value.trim();
		const passwordConfirmation = passwordConfirmationInput.value.trim();
		if (ePasswordConfirmation) {
			if (passwordConfirmation === '') {
				ePasswordConfirmation.innerText = 'Debes confirmar la contraseña.';
			} else if (password !== passwordConfirmation) {
				ePasswordConfirmation.innerText = 'Las contraseñas no coinciden.';
			} else {
				ePasswordConfirmation.innerText = '';
			}
		}
		comprobarBoton();
	}

	function comprobarSede() {
		const sede = sedeInput.value;
		if (eSede) {
			if (!sede) {
				eSede.innerText = 'Debes seleccionar una sede.';
			} else {
				eSede.innerText = '';
			}
		}
		comprobarBoton();
	}

	function comprobarRol() {
		const rol = rolInput.value;
		if (eRol) {
			if (!rol) {
				eRol.innerText = 'Debes seleccionar un rol.';
			} else {
				eRol.innerText = '';
			}
		}
		comprobarBoton();
	}

};
