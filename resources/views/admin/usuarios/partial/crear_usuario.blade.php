<form method="POST" action="{{ route('admin.usuarios.store') }}" class="needs-validation" novalidate>
    @csrf

    <div class="mb-3">
        <label class="form-label">Nombre completo:</label>
        <input type="text" class="form-control" name="name" value="{{ old('name') }}">
        <div id="error-nombre" class="text-danger small">@error('name'){{ $message }}@enderror</div>
    </div>

    <div class="mb-3">
        <label class="form-label">Email Corporativo:</label>
        <input type="email" class="form-control" name="email" value="{{ old('email') }}">
        <div id="error-email" class="text-danger small">@error('email'){{ $message }}@enderror</div>
    </div>

    <div class="mb-3">
        <label class="form-label">Contraseña:</label>
        <input type="password" class="form-control" name="password">
        <div id="error-password" class="text-danger small">@error('password'){{ $message }}@enderror</div>
    </div>

    <div class="mb-3">
        <label class="form-label">Confirmar Contraseña:</label>
        <input type="password" class="form-control" name="password_confirmation">
        <div id="error-password-confirmation" class="text-danger small"></div>
    </div>

    <div class="mb-3">
        <label class="form-label">Sede:</label>
        <select name="sede_id" class="form-select">
            <option value="" disabled {{ old('sede_id') ? '' : 'selected' }}>Selecciona una sede</option>
            @foreach ($sedes as $sede)
                <option value="{{ $sede->id }}" {{ old('sede_id') == $sede->id ? 'selected' : '' }}>
                    {{ $sede->nom }}
                </option>
            @endforeach
        </select>
        <div id="error-sede_id" class="text-danger small">@error('sede_id'){{ $message }}@enderror</div>
    </div>

    <div class="mb-3">
        <label class="form-label">Rol:</label>
        <select name="rol" class="form-select">
            <option value="" disabled {{ old('rol') ? '' : 'selected' }}>Selecciona un rol</option>
            @foreach ($roles as $value => $label)
                <option value="{{ $value }}" {{ old('rol') == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <div id="error-rol" class="text-danger small">@error('rol'){{ $message }}@enderror</div>
    </div>


    <button type="submit" id="boton-crear-usuario" class="btn btn-primary">Crear Usuario</button>
</form>