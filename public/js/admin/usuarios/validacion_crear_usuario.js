window.onload = function () {
	// Elementos de error
	const eNombre = document.getElementById("error-nombre");
	const eEmail = document.getElementById("error-email");
	const ePassword = document.getElementById("error-password");
	const ePasswordConfirmation = document.getElementById("error-password-confirmation");
	const eSede = document.getElementById("error-sede_id");
	const eRol = document.getElementById("error-rol");

	// Inputs
	const nombreInput = document.querySelector('input[name="name"]');
	const emailInput = document.querySelector('input[name="email"]');
	const passwordInput = document.querySelector('input[name="password"]');
	const passwordConfirmationInput = document.querySelector('input[name="password_confirmation"]');
	const sedeInput = document.querySelector('select[name="sede_id"]');
	const rolInput = document.querySelector('select[name="rol"]');
	const botonEnviar = document.querySelector('button[type="submit"]');

	comprobarBoton();

	nombreInput.onblur = comprobarNombre;
	emailInput.onblur = comprobarEmail;
	passwordInput.onblur = comprobarPassword;
	passwordConfirmationInput.onblur = comprobarPasswordConfirmation;
	sedeInput.onblur = comprobarSede;
	rolInput.onblur = comprobarRol;

	nombreInput.oninput = comprobarNombre;
	emailInput.oninput = comprobarEmail;
	passwordInput.oninput = function() {
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

		if (nombreValido && emailValido && passwordValido && passwordConfirmationValido && sedeValida && rolValido && actiuValido) {
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
			} else if (!emailFormato.test(email)) {
				eEmail.innerText = 'Por favor, introduce un correo electrónico válido.';
			} else {
				eEmail.innerText = '';
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
