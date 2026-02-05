<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexton - Portal d'Incidències</title>
    @vite(['resources/css/login.css'])
    <!-- Reutilizando la fuente de la variable CSS si está disponible, típicamente Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="contenedor-login">
        <!-- Columna Izquierda: Imagen de Marca -->
        <div class="marca-login">
            <div class="superposicion-marca">
                <div class="contenido-marca">
                    <h1>Bienvenido a Nexton</h1>
                    <p>Gestión inteligente de incidencias corporativas.</p>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Formulario -->
        <div class="envoltorio-formulario-login">
            <div class="caja-login">
                <!-- Marca / Título -->
                <div class="contenedor-logo">
                    <div class="logo-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 12.01L3.27 6.96V16L12 22.08V12.01Z" fill="#3b82f6" stroke="none"/>
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                            <line x1="12" x2="12" y1="22.08" y2="12"/>
                        </svg>
                    </div>
                    <div class="texto-logo">Nexton</div>
                </div>
                
                <h2>Portal d'Incidències</h2>
                <p class="subtitulo">Ingresa tus credenciales para acceder</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <!-- Entrada de Correo Electrónico -->
                    <div class="grupo-formulario">
                        <label for="email">Correo Electrónico</label>
                        <div class="envoltorio-input">
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                placeholder="ejemplo@nexton.com" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus
                                class="{{ $errors->has('email') ? 'input-error' : '' }}"
                            >
                            <!-- Icono de Correo SVG -->
                            <i class="icono-input">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            </i>
                        </div>
                        @error('email')
                            <span class="mensaje-error visible">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Entrada de Contraseña -->
                    <div class="grupo-formulario">
                        <label for="password">Contraseña</label>
                        <div class="envoltorio-input">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                placeholder="••••••••" 
                                required
                                class="{{ $errors->has('password') ? 'input-error' : '' }}"
                            >
                            <!-- Icono de Candado SVG -->
                            <i class="icono-input">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </i>
                        </div>
                        @error('password')
                            <span class="mensaje-error visible">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Se eliminaron "Recordarme" y "Olvidaste tu contraseña" según solicitud -->

                    <!-- Botón de Envío -->
                    <button type="submit" class="boton-login">
                        Iniciar Sesión
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" x2="3" y1="12" y2="12"/></svg>
                    </button>

                    <!-- Alerta General de Error -->
                    @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
                         <div class="error-general visible">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif
                </form>

                <div class="pie-login">
                    <p>&copy; 2026 Nexton Gestor de Incidencias. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>