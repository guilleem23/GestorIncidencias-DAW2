<form action="{{ route('admin.categorias.update', $categoria->id) }}" method="POST" class="needs-validation" novalidate>
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label text-light">Nombre:</label>
        <input type="text" class="form-control form-control-dark" name="nom" value="{{ old('nom', $categoria->nom) }}" required>
        @error('nom')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label text-light">Descripción:</label>
        <textarea class="form-control form-control-dark" name="descripcion" rows="3">{{ old('descripcion', $categoria->descripcion) }}</textarea>
        @error('descripcion')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn-submit btn-submit-update">
        <i class="fa-solid fa-save"></i> Actualizar Categoría
    </button>
</form>
