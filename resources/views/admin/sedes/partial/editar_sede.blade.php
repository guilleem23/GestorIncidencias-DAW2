<form action="{{ route('admin.sedes.update', $sede->id) }}" method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label text-light">Nombre de la Sede:</label>
        <input type="text" class="form-control form-control-dark" name="nom" value="{{ old('nom', $sede->nom) }}" required>
        @error('nom')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label text-light">Responsable:</label>
        <input type="text" class="form-control form-control-dark" name="responsable" value="{{ old('responsable', $sede->responsable) }}">
        @error('responsable')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label text-light">Descripción:</label>
        <textarea class="form-control form-control-dark" name="descripcion" rows="3">{{ old('descripcion', $sede->descripcion) }}</textarea>
        @error('descripcion')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label text-light">Imagen:</label>
        @if($sede->imagen)
            <div class="mb-2">
                <img src="{{ asset('storage/' . $sede->imagen) }}" alt="Imagen actual" style="max-height: 100px; border-radius: 5px;">
            </div>
        @endif
        <input type="file" class="form-control form-control-dark" name="imagen" accept="image/*">
        @error('imagen')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn-submit btn-submit-update">
        <i class="fa-solid fa-save"></i> Actualizar Sede
    </button>
</form>
