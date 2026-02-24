<form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST" class="needs-validation" novalidate>
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label">Nombre completo:</label>
        <input type="text" class="form-control" name="edit_name" value="{{ old('edit_name', $usuario->name) }}">
        @error('edit_name')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Email Corporativo:</label>
        <input type="email" class="form-control" name="edit_email" value="{{ old('edit_email', $usuario->email) }}">
        @error('edit_email')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Contraseña (dejar en blanco para no cambiar):</label>
        <input type="password" class="form-control" name="edit_password">
        @error('edit_password')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Sede:</label>
        <select name="edit_sede_id" class="form-select">
            <option value="" disabled>Selecciona una sede</option>
            @foreach ($sedes as $sede)
                <option value="{{ $sede->id }}" {{ old('edit_sede_id', $usuario->sede_id) == $sede->id ? 'selected' : '' }}>
                    {{ $sede->nom }}
                </option>
            @endforeach
        </select>
        @error('edit_sede_id')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Rol:</label>
        <select name="edit_rol" class="form-select">
            <option value="" disabled>Selecciona un rol</option>
            @foreach ($roles as $value => $label)
                <option value="{{ $value }}" {{ old('edit_rol', $usuario->rol) == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('edit_rol')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </div>
    <button type="submit" class="btn btn-success">Actualizar Usuario</button>
</form>