<form id="form-editar-incidencia" action="{{ route('gestor.incidencias.update', $incidencia->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label text-white" for="titol">Título de la Incidencia</label>
            <input type="text" id="titol" name="titol" class="form-control bg-dark text-white border-secondary" value="{{ old('titol', $incidencia->titol) }}" required>
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label text-white" for="tecnic_id">Técnico Asignado</label>
            <select id="tecnic_id" name="tecnic_id" class="form-select bg-dark text-white border-secondary">
                <option value="">-- Sin Signar --</option>
                @foreach($tecnicos as $tecnico)
                    <option value="{{ $tecnico->id }}" {{ (old('tecnic_id', $incidencia->tecnic_id) == $tecnico->id) ? 'selected' : '' }}>
                        {{ $tecnico->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label text-white" for="categoria_id">Categoría</label>
            <select id="categoria_id" name="categoria_id" class="form-select bg-dark text-white border-secondary" required>
                @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}" {{ (old('categoria_id', $incidencia->categoria_id) == $cat->id) ? 'selected' : '' }}>
                        {{ $cat->nom }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label text-white" for="subcategoria_id">Subcategoría</label>
            <select id="subcategoria_id" name="subcategoria_id" class="form-select bg-dark text-white border-secondary" required>
                @foreach($incidencia->categoria->subcategorias as $sub)
                    <option value="{{ $sub->id }}" {{ (old('subcategoria_id', $incidencia->subcategoria_id) == $sub->id) ? 'selected' : '' }}>
                        {{ $sub->nom }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label text-white" for="estat">Estado</label>
            <select id="estat" name="estat" class="form-select bg-dark text-white border-secondary" required>
                <option value="Sense assignar" {{ (old('estat', $incidencia->estat) === 'Sense assignar') ? 'selected' : '' }}>Sense assignar</option>
                <option value="Assignada" {{ (old('estat', $incidencia->estat) === 'Assignada') ? 'selected' : '' }}>Assignada</option>
                <option value="En treball" {{ (old('estat', $incidencia->estat) === 'En treball') ? 'selected' : '' }}>En treball</option>
                <option value="Resolta" {{ (old('estat', $incidencia->estat) === 'Resolta') ? 'selected' : '' }}>Resolta</option>
                <option value="Tancada" {{ (old('estat', $incidencia->estat) === 'Tancada') ? 'selected' : '' }}>Tancada</option>
            </select>
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label text-white" for="prioritat">Prioridad</label>
            <select id="prioritat" name="prioritat" class="form-select bg-dark text-white border-secondary" required>
                <option value="alta" {{ (old('prioritat', $incidencia->prioritat) === 'alta') ? 'selected' : '' }}>Alta</option>
                <option value="mitjana" {{ (old('prioritat', $incidencia->prioritat) === 'mitjana') ? 'selected' : '' }}>Media</option>
                <option value="baixa" {{ (old('prioritat', $incidencia->prioritat) === 'baixa') ? 'selected' : '' }}>Baja</option>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label text-white" for="descripcio">Descripción de la Incidencia</label>
        <textarea id="descripcio" name="descripcio" class="form-control bg-dark text-white border-secondary" rows="3" required>{{ old('descripcio', $incidencia->descripcio) }}</textarea>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="btn-save-incidencia">
            <i class="fa-solid fa-save"></i> Guardar Cambios
        </button>
    </div>
</form>
