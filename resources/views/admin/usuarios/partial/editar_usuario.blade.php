<form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST" class="needs-validation" novalidate>
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label">Nombre completo:</label>
        <input type="text" class="form-control" name="name" value="{{ old('name', $usuario->name) }}">
        @error('name')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Email Corporativo:</label>
        <input type="email" class="form-control" name="email" value="{{ old('email', $usuario->email) }}">
        @error('email')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Contraseña (dejar en blanco para no cambiar):</label>
        <input type="password" class="form-control" name="password">
        @error('password')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Sede:</label>
        <select name="sede_id" class="form-select">
            <option value="" disabled>Selecciona una sede</option>
            @foreach ($sedes as $sede)
                <option value="{{ $sede->id }}" {{ old('sede_id', $usuario->sede_id) == $sede->id ? 'selected' : '' }}>
                    {{ $sede->nom }}
                </option>
            @endforeach
        </select>
        @error('sede_id')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Rol:</label>
        <select name="rol" class="form-select">
            <option value="" disabled>Selecciona un rol</option>
            @foreach ($roles as $value => $label)
                <option value="{{ $value }}" {{ old('rol', $usuario->rol) == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('rol')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </div>
    <button type="submit" class="btn btn-success">Actualizar Usuario</button>
</form>