<form method="POST" action="{{ route('admin.usuarios.store') }}">
    @csrf
    @method('PUT')
    <div>
        <label>Nombre completo:</label>
        <input type="text" name="name" value="{{ old('name') }}">
        @error('name')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </div>
    <div>
        <label>Email Corporativo:</label>
        <input type="email" name="email" value="{{ old('email') }}">
        @error('email')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </div>
    <div>
        <label>Contraseña:</label>
        <input type="password" name="password">
        @error('password')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </div>
    <div>
        <label>Sede:</label>
        <select name="sede_id">
            <option value="" disabled selected>Selecciona una sede</option>
            @foreach ($sedes as $sede)
                <option value="{{ $sede->id }}" {{ old('sede_id') == $sede->id ? 'selected' : '' }}>
                    {{ $sede->nom }}
                </option>
            @endforeach
        </select>
        @error('sede_id')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </div>
    <div>
        <label>Rol:</label>
        <select name="rol">
            <option value="" disabled selected>Selecciona un rol</option>
            @foreach ($roles as $value => $label)
                <option value="{{ $value }}" {{ old('rol') == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('rol')
            <div style="color:red;">{{ $message }}</div>
        @enderror
    </div>
    <button type="submit">Crear Usuario</button>

</form>
