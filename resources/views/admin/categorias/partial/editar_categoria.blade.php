<form action="{{ route('admin.categorias.update', $categoria->id) }}" method="POST" class="needs-validation" novalidate>
    @csrf
    @method('PUT')
    <input type="hidden" id="edit-categoria-id" value="{{ $categoria->id }}">
    <div class="mb-3">
        <label class="form-label text-light">Nombre:</label>
        <input type="text" class="form-control form-control-dark" name="nom" id="edit-nombre-categoria" value="{{ old('nom', $categoria->nom) }}">
        <div id="error-edit-nombre" class="text-danger small">@error('nom'){{ $message }}@enderror</div>
        <div id="disponibilidad-edit-nombre" class="small"></div>
    </div>

    <div class="mb-3">
        <label class="form-label text-light">Descripción:</label>
        <textarea class="form-control form-control-dark" name="descripcion" id="edit-descripcion-categoria" rows="3">{{ old('descripcion', $categoria->descripcion) }}</textarea>
        <div id="error-edit-descripcion" class="text-danger small">@error('descripcion'){{ $message }}@enderror</div>
    </div>

    <button type="submit" class="btn-submit btn-submit-update" id="edit-btn-enviar-categoria">
        <i class="fa-solid fa-save"></i> Actualizar Categoría
    </button>
</form>
