<form method="POST" action="{{ route('admin.usuarios.store') }}" class="needs-validation" novalidate>
    @csrf
    <div class="usuarios-form-grid">
        <div class="mb-1">
            <label class="form-label" for="nombre-usuario">Nombre completo:</label>
            <input type="text" class="form-control" id="nombre-usuario" name="name" value="{{ old('name') }}">
            <p id="error-nombre" class="text-danger small">
                @error('name')
                    {{ $message }}
                @enderror
            </p>
        </div>

        <div class="mb-1">
            <label class="form-label" for="username">Nombre de usuario:</label>
            <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}">
            <div class="usuarios-feedback-container">
                <p id="error-username" class="text-danger small">
                    @error('username')
                        {{ $message }}
                    @enderror
                </p>
                <p id="disponibilidad-username" class="text-success small"></p>
            </div>
        </div>

        <div class="mb-1">
            <label class="form-label" for="email-usuario">Email Corporativo:</label>
            <input type="email" class="form-control" id="email-usuario" name="email" value="{{ old('email') }}">
            <div class="usuarios-feedback-container">
                <p id="error-email" class="text-danger small">
                    @error('email')
                        {{ $message }}
                    @enderror
                </p>
                <p id="disponibilidad-email" class="text-success small"></p>
            </div>
        </div>

        <div class="mb-1">
            <label class="form-label" for="password-usuario">Contraseña:</label>
            <input type="password" class="form-control" id="password-usuario" name="password">
            <p id="error-password" class="text-danger small">
                @error('password')
                    {{ $message }}
                @enderror
            </p>
        </div>

        <div class="mb-1">
            <label class="form-label" for="password-confirmation-usuario">Confirmar Contraseña:</label>
            <input type="password" class="form-control" id="password-confirmation-usuario" name="password_confirmation">
            <p id="error-password-confirmation" class="text-danger small"></p>
        </div>

        <div class="mb-1">
            <label class="form-label" for="sede-usuario">Sede:</label>
            <select name="sede_id" class="form-select" id="sede-usuario">
                <option value="" disabled {{ old('sede_id') ? '' : 'selected' }}>Selecciona una sede</option>
                @foreach ($sedes as $sede)
                    <option value="{{ $sede->id }}" {{ old('sede_id') == $sede->id ? 'selected' : '' }}>
                        {{ $sede->nom }}
                    </option>
                @endforeach
            </select>
            <p id="error-sede_id" class="text-danger small">
                @error('sede_id')
                    {{ $message }}
                @enderror
            </p>
        </div>

        <div class="mb-1">
            <label class="form-label" for="rol-usuario">Rol:</label>
            <select name="rol" class="form-select" id="rol-usuario">
                <option value="" disabled {{ old('rol') ? '' : 'selected' }}>Selecciona un rol</option>
                @foreach ($roles as $value => $label)
                    <option value="{{ $value }}" {{ old('rol') == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <p id="error-rol" class="text-danger small">
                @error('rol')
                    {{ $message }}
                @enderror
            </p>
        </div>
    </div>
    <button type="submit" id="boton-enviar" class="btn btn-primary mt-3">Crear Usuario</button>
</form>
