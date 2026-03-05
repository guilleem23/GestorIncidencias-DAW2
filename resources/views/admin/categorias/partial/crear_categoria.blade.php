<form method="POST" action="{{ route('admin.categorias.store') }}" class="needs-validation" novalidate>
    @csrf
    <div class="mb-3">
        <label class="form-label text-light">Nombre:</label>
        <input type="text" class="form-control form-control-dark" name="nom" id="nombre-categoria" value="{{ old('nom') }}" placeholder="Ej: Software, Hardware...">
        <div id="error-nombre" class="text-danger small">@error('nom'){{ $message }}@enderror</div>
        <div id="disponibilidad-nombre" class="small"></div>
    </div>

    <div class="mb-3">
        <label class="form-label text-light">Descripción:</label>
        <textarea class="form-control form-control-dark" name="descripcion" id="descripcion-categoria" rows="3" placeholder="Descripción de la categoría (opcional)">{{ old('descripcion') }}</textarea>
        <div id="error-descripcion" class="text-danger small">@error('descripcion'){{ $message }}@enderror</div>
    </div>

    <button type="submit" class="btn-submit" id="btn-enviar-categoria">
        <i class="fa-solid fa-plus"></i> Crear Categoría
    </button>
</form>
