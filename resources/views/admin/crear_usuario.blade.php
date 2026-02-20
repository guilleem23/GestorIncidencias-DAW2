<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de Usuario - Nexton</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo-header">
            <i class="fas fa-cube logo-icon"></i>
            <span class="logo-text">Nexton</span>
        </div>
        <div class="user-info">
            <span class="user-name">
                <i class="fas fa-user-shield"></i> {{ auth()->user()->name }}
            </span>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </header>

    <!-- Contenido Principal -->
    <div class="container">
        <a href="{{ route('admin.usuarios.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Volver al Panel
        </a>

        <h1 class="page-title">Alta de Usuario</h1>
        <p class="page-subtitle">Registrar nuevo empleado en el sistema</p>

        <div class="form-container">
            @if(session('success'))
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.usuarios.store') }}">
                @csrf
                
                <!-- Nombre Completo -->
                <div class="form-group">
                    <label for="name">
                        <i class="fas fa-user"></i> Nombre Completo
                    </label>
                    <input 
                        type="text" 
                        id="name"
                        name="name" 
                        class="form-input {{ $errors->has('name') ? 'error' : '' }}"
                        value="{{ old('name') }}"
                        placeholder="Ej: Juan Pérez García"
                        autofocus
                    >
                    @error('name')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Email Corporativo -->
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email Corporativo
                    </label>
                    <input 
                        type="email" 
                        id="email"
                        name="email" 
                        class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                        value="{{ old('email') }}"
                        placeholder="usuario@nexton.com"
                    >
                    @error('email')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Contraseña -->
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Contraseña
                    </label>
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                        placeholder="Mínimo 6 caracteres"
                    >
                    @error('password')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Sede -->
                <div class="form-group">
                    <label for="sede_id">
                        <i class="fas fa-building"></i> Sede
                    </label>
                    <select 
                        name="sede_id" 
                        id="sede_id"
                        class="form-select {{ $errors->has('sede_id') ? 'error' : '' }}"
                    >
                        <option value="" disabled selected>Selecciona una sede</option>
                        @foreach ($sedes as $sede)
                            <option value="{{ $sede->id }}" {{ old('sede_id') == $sede->id ? 'selected' : '' }}>
                                {{ $sede->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('sede_id')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Rol -->
                <div class="form-group">
                    <label for="rol">
                        <i class="fas fa-id-badge"></i> Rol
                    </label>
                    <select 
                        name="rol" 
                        id="rol"
                        class="form-select {{ $errors->has('rol') ? 'error' : '' }}"
                    >
                        <option value="" disabled selected>Selecciona un rol</option>
                        @foreach ($roles as $value => $label)
                            <option value="{{ $value }}" {{ old('rol') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('rol')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Botones de Acción -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i>
                        Crear Usuario
                    </button>
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
