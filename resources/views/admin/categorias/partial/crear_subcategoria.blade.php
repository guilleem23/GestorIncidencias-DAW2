<form method="POST" action="{{ route('admin.subcategorias.store') }}" class="needs-validation" novalidate>
    @csrf
    <div class="mb-3">
        <label class="form-label text-light">Categoría:</label>
        <select name="categoria_id" id="categoria-select" class="form-select form-control-dark">
            <option value="" disabled {{ old('categoria_id') ? '' : 'selected' }}>Selecciona una categoría</option>
            @foreach ($categorias as $cat)
                <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->nom }}
                </option>
            @endforeach
        </select>
        <div id="error-categoria" class="text-danger small">@error('categoria_id'){{ $message }}@enderror</div>
    </div>

    <div class="mb-3">
        <label class="form-label text-light">Nombre:</label>
        <input type="text" class="form-control form-control-dark" name="nom" id="nombre-subcategoria" value="{{ old('nom') }}" placeholder="Ej: Accés remot, Ratolí no funciona...">
        <div id="error-nombre-sub" class="text-danger small">@error('nom'){{ $message }}@enderror</div>
        <div id="disponibilidad-nombre-sub" class="small"></div>
    </div>

    <div class="mb-3">
        <label class="form-label text-light">Descripción:</label>
        <textarea class="form-control form-control-dark" name="descripcion" id="descripcion-subcategoria" rows="3" placeholder="Descripción de la subcategoría (opcional)">{{ old('descripcion') }}</textarea>
        <div id="error-descripcion-sub" class="text-danger small">@error('descripcion'){{ $message }}@enderror</div>
    </div>

    <button type="submit" class="btn-submit" id="btn-enviar-subcategoria">
        <i class="fa-solid fa-plus"></i> Crear Subcategoría
    </button>
</form>
