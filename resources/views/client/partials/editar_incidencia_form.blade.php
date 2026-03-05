<!-- Formulario de editar incidencia -->
<form id="form-editar-incidencia-client" method="POST">
    @csrf
    @method('PUT')
    
    <div class="form-group mb-3">
        <label for="edit-titol-client" class="form-label">
            <i class="fa-solid fa-heading"></i> Título *
        </label>
        <input type="text" 
               id="edit-titol-client" 
               name="titol" 
               class="form-control bg-secondary text-white border-secondary"
               placeholder="Título de la incidencia...">
        <small id="error-edit-titol-client" class="form-text text-danger" style="display: none;"></small>
    </div>

    <div class="form-group mb-3">
        <label for="edit-descripcio-client" class="form-label">
            <i class="fa-solid fa-file-alt"></i> Descripción *
        </label>
        <textarea id="edit-descripcio-client" 
                  name="descripcio" 
                  class="form-control bg-secondary text-white border-secondary"
                  rows="4"
                  placeholder="Describe el problema..."></textarea>
        <small id="error-edit-descripcio-client" class="form-text text-danger" style="display: none;"></small>
    </div>

    <div class="form-group mb-3">
        <label for="edit-categoria-client" class="form-label">
            <i class="fa-solid fa-tag"></i> Categoría *
        </label>
        <select id="edit-categoria-client" 
                name="categoria_id" 
                class="form-select bg-secondary text-white border-secondary">
            <option value="">Selecciona una categoría...</option>
        </select>
        <small id="error-edit-categoria-client" class="form-text text-danger" style="display: none;"></small>
    </div>

    <div class="form-group mb-3">
        <label for="edit-subcategoria-client" class="form-label">
            <i class="fa-solid fa-tags"></i> Subcategoría *
        </label>
        <select id="edit-subcategoria-client" 
                name="subcategoria_id" 
                class="form-select bg-secondary text-white border-secondary">
            <option value="">Primero selecciona una categoría...</option>
        </select>
        <small id="error-edit-subcategoria-client" class="form-text text-danger" style="display: none;"></small>
    </div>

    <div class="modal-footer border-secondary">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa-solid fa-times"></i> Cancelar
        </button>
        <button type="submit" id="btn-submit-edit-incidencia-client" class="btn btn-primary">
            <i class="fa-solid fa-save"></i> Guardar Cambios
        </button>
    </div>
</form>
