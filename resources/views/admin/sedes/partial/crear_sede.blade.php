<form method="POST" action="{{ route('admin.sedes.store') }}" class="needs-validation" enctype="multipart/form-data" novalidate>
    @csrf
    <div class="mb-3">
        <label class="form-label text-light">Nombre de la Sede:</label>
        <input type="text" class="form-control form-control-dark" name="nom" id="nombre-sede" value="{{ old('nom') }}" placeholder="Ej: Barcelona, Madrid...">
        <div id="error-nombre" class="text-danger small">@error('nom'){{ $message }}@enderror</div>
        <div id="disponibilidad-nombre" class="small"></div>
    </div>

    <div class="mb-3">
        <label class="form-label text-light">Responsable:</label>
        <input type="text" class="form-control form-control-dark" name="responsable" id="responsable-sede" value="{{ old('responsable') }}" placeholder="Nombre del responsable">
        <div id="error-responsable" class="text-danger small">@error('responsable'){{ $message }}@enderror</div>
    </div>

    <div class="mb-3">
        <label class="form-label text-light">Descripción:</label>
        <textarea class="form-control form-control-dark" name="descripcion" id="descripcion-sede" rows="3" placeholder="Breve descripción de la sede">{{ old('descripcion') }}</textarea>
        <div id="error-descripcion" class="text-danger small">@error('descripcion'){{ $message }}@enderror</div>
    </div>

    <div class="mb-3">
        <label class="form-label text-light">Imagen:</label>
        <input type="file" class="form-control form-control-dark" name="imagen" accept="image/*">
        <div class="text-danger small">@error('imagen'){{ $message }}@enderror</div>
    </div>

    <button type="submit" class="btn-submit" id="btn-enviar-sede">
        <i class="fa-solid fa-plus"></i> Crear Sede
    </button>
</form>
