<form method="POST" action="{{ route('admin.categorias.store') }}" class="needs-validation" novalidate>
    @csrf
    <div class="mb-3">
        <label class="form-label text-light">Nombre:</label>
        <input type="text" class="form-control form-control-dark" name="nom" value="{{ old('nom') }}" placeholder="Ej: Software, Hardware...">
        <div class="text-danger small">@error('nom'){{ $message }}@enderror</div>
    </div>

    <div class="mb-3">
        <label class="form-label text-light">Descripción:</label>
        <textarea class="form-control form-control-dark" name="descripcion" rows="3" placeholder="Descripción de la categoría (opcional)">{{ old('descripcion') }}</textarea>
        <div class="text-danger small">@error('descripcion'){{ $message }}@enderror</div>
    </div>

    <button type="submit" class="btn-submit">
        <i class="fa-solid fa-plus"></i> Crear Categoría
    </button>
</form>
