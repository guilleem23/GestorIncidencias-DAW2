<form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST" class="needs-validation" novalidate>
    @csrf
    @method('PUT')
    <input type="hidden" id="edit-user-id" value="{{ $usuario->id }}">
    
    <div class="usuarios-form-grid">
        <div class="mb-1">
            <label class="form-label" for="edit-nombre-usuario">Nombre completo:</label>
            <input type="text" class="form-control" id="edit-nombre-usuario" name="edit_name" value="{{ old('edit_name', $usuario->name) }}">
            <p id="error-edit-nombre" class="text-danger small">
                @error('edit_name') {{ $message }} @enderror
            </p>
        </div>

        <div class="mb-1">
            <label class="form-label" for="edit-username-usuario">Nombre de usuario:</label>
            <input type="text" class="form-control" id="edit-username-usuario" name="edit_username" value="{{ old('edit_username', $usuario->username) }}">
            <div class="usuarios-feedback-container">
                <p id="error-edit-username" class="text-danger small">
                    @error('edit_username') {{ $message }} @enderror
                </p>
                <p id="disponibilidad-edit-username" class="text-success small"></p>
            </div>
        </div>

        <div class="mb-1">
            <label class="form-label" for="edit-email-usuario">Email Corporativo:</label>
            <input type="email" class="form-control" id="edit-email-usuario" name="edit_email" value="{{ old('edit_email', $usuario->email) }}">
            <div class="usuarios-feedback-container">
                <p id="error-edit-email" class="text-danger small">
                    @error('edit_email') {{ $message }} @enderror
                </p>
                <p id="disponibilidad-edit-email" class="text-success small"></p>
            </div>
        </div>

        <div class="mb-1">
            <label class="form-label" for="edit-password-usuario">Contraseña:</label>
            <input type="password" class="form-control" id="edit-password-usuario" name="edit_password" placeholder="Dejar en blanco para no cambiar">
            <p id="error-edit-password" class="text-danger small">
                @error('edit_password') {{ $message }} @enderror
            </p>
        </div>

        <div class="mb-1">
            <label class="form-label" for="edit-password-confirmation-usuario">Confirmar Contraseña:</label>
            <input type="password" class="form-control" id="edit-password-confirmation-usuario" name="edit_password_confirmation" placeholder="Repite la contraseña">
            <p id="error-edit-password-confirmation" class="text-danger small"></p>
        </div>

                <div class="mb-1">
            <label class="form-label" for="edit-rol-usuario">Rol:</label>
            <select name="edit_rol" id="edit-rol-usuario" class="form-select">
                <option value="" disabled>Selecciona un rol</option>
                @foreach ($roles as $value => $label)
                    <option value="{{ $value }}" {{ old('edit_rol', $usuario->rol) == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <p id="error-edit-rol" class="text-danger small"></p>
            @error('edit_rol') {{ $message }} @enderror
        </div> 
        
        <div class="mb-1">
            <label class="form-label" for="edit-sede-usuario">Sede:</label>
            <select name="edit_sede_id" id="edit-sede-usuario" class="form-select">
                <option value="" disabled>Selecciona una sede</option>
                @foreach ($sedes as $sede)
                    <option value="{{ $sede->id }}" {{ old('edit_sede_id', $usuario->sede_id) == $sede->id ? 'selected' : '' }}>
                        {{ $sede->nom }}
                    </option>
                @endforeach
            </select>
            <p id="error-edit-sede_id" class="text-danger small">
                @error('edit_sede_id') {{ $message }} @enderror
            </p>
            <p id="error-edit-gestor-sede" class="text-danger small"></p>
        </div>

        <div class="mb-1">
            <label class="form-label" for="edit-activo-usuario">Estado de la cuenta:</label>
            <select name="edit_activo" id="edit-activo-usuario" class="form-select">
                <option value="1" {{ old('edit_activo', $usuario->actiu) == 1 ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ old('edit_activo', $usuario->actiu) == 0 ? 'selected' : '' }}>Inactivo</option>
            </select>
            <p id="error-edit-activo" class="text-danger small">
                @error('edit_activo') {{ $message }} @enderror
            </p>
        </div>
    </div>

    <button type="submit" id="edit-boton-enviar" class="btn btn-success mt-3">Actualizar Usuario</button>
</form>