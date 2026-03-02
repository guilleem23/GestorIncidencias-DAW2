<form action="{{ route('admin.subcategorias.update', $subcategoria->id) }}" method="POST" class="needs-validation" novalidate>
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label text-light">Categoría:</label>
        <select name="categoria_id" class="form-select form-control-dark">
            <option value="" disabled>Selecciona una categoría</option>
            @foreach ($categorias as $cat)
                <option value="{{ $cat->id }}" {{ old('categoria_id', $subcategoria->categoria_id) == $cat->id ? 'selected' : '' }}>
                    {{ $cat->nom }}
                </option>
            @endforeach
        </select>
        @error('categoria_id')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label text-light">Nombre:</label>
        <input type="text" class="form-control form-control-dark" name="nom" value="{{ old('nom', $subcategoria->nom) }}">
        @error('nom')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label text-light">Descripción:</label>
        <textarea class="form-control form-control-dark" name="descripcion" rows="3">{{ old('descripcion', $subcategoria->descripcion) }}</textarea>
        @error('descripcion')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn-submit btn-submit-update">
        <i class="fa-solid fa-save"></i> Actualizar Subcategoría
    </button>
</form>
