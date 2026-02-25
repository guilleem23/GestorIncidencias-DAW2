// ===============================================
// VALIDACIÓN DE FORMULARIO CREAR INCIDENCIA
// ===============================================

window.onload = function() {
    
    // ===== FUNCIONES AUXILIARES =====
    
    // Mostrar error debajo del campo
    function mostrarError(campo, mensaje) {
        // Quitar error anterior si existe
        quitarError(campo);
        
        // Marcar campo con clase error
        campo.classList.add('error');
        
        // Crear span de error
        var errorSpan = document.createElement('span');
        errorSpan.className = 'error-texto-js';
        errorSpan.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + mensaje;
        
        // Insertar después del campo
        campo.parentElement.appendChild(errorSpan);
    }
    
    // Quitar error del campo
    function quitarError(campo) {
        campo.classList.remove('error');
        
        // Buscar y eliminar span de error si existe
        var errorSpan = campo.parentElement.querySelector('.error-texto-js');
        if (errorSpan) {
            errorSpan.remove();
        }
    }
    
    // ===== VALIDACIÓN EN TIEMPO REAL (onblur) =====
    
    // Validar TÍTULO al cambiar de campo
    var titulo = document.getElementById('titol');
    if (titulo) {
        titulo.onblur = function() {
            var valor = this.value.trim();
            if (!valor) {
                mostrarError(this, 'El título es obligatorio');
            } else if (valor.length < 3) {
                mostrarError(this, 'El título debe tener al menos 3 caracteres');
            } else {
                quitarError(this);
            }
        };
        
        // Limpiar error mientras escribe (en tiempo real)
        titulo.oninput = function() {
            var valor = this.value.trim();
            if (valor && valor.length >= 3) {
                quitarError(this);
            }
        };
    }

    // Validar DESCRIPCIÓN al cambiar de campo
    var descripcion = document.getElementById('descripcio');
    if (descripcion) {
        descripcion.onblur = function() {
            var valor = this.value.trim();
            if (!valor) {
                mostrarError(this, 'La descripción es obligatoria');
            } else if (valor.length < 10) {
                mostrarError(this, 'La descripción debe tener al menos 10 caracteres');
            } else {
                quitarError(this);
            }
        };
        
        // Limpiar error mientras escribe (en tiempo real)
        descripcion.oninput = function() {
            var valor = this.value.trim();
            if (valor && valor.length >= 10) {
                quitarError(this);
            }
        };
    }

    // Validar SEDE al cambiar de campo
    var sedeSelect = document.getElementById('sede_id');
    if (sedeSelect) {
        sedeSelect.onblur = function() {
            if (!this.value) {
                mostrarError(this, 'Debes seleccionar una sede');
            } else {
                quitarError(this);
            }
        };
        
        // Limpiar error al seleccionar (en tiempo real)
        sedeSelect.onchange = function() {
            if (this.value) {
                quitarError(this);
            }
        };
    }

    // Validar CATEGORÍA al cambiar de campo
    var categoriaSelect = document.getElementById('categoria_id');
    if (categoriaSelect) {
        categoriaSelect.onblur = function() {
            if (!this.value) {
                mostrarError(this, 'Debes seleccionar una categoría');
            } else {
                quitarError(this);
            }
        };
    }

    // Validar SUBCATEGORÍA al cambiar de campo
    var subcategoriaSelect = document.getElementById('subcategoria_id');
    if (subcategoriaSelect) {
        subcategoriaSelect.onblur = function() {
            if (!this.value && !this.disabled) {
                mostrarError(this, 'Debes seleccionar una subcategoría');
            } else if (this.value) {
                quitarError(this);
            }
        };
        
        // Limpiar error al seleccionar (en tiempo real)
        subcategoriaSelect.onchange = function() {
            if (this.value) {
                quitarError(this);
            }
        };
    }

    // ===== CARGA DINÁMICA DE SUBCATEGORÍAS =====
    
    if (categoriaSelect) {
        categoriaSelect.onchange = function() {
            var subcategoriaSelect = document.getElementById('subcategoria_id');
            var selectedOption = this.options[this.selectedIndex];
            
            // Limpiar error de categoría si selecciona una
            if (this.value) {
                quitarError(this);
            }
            
            subcategoriaSelect.innerHTML = '<option value="">Selecciona una subcategoria...</option>';
            
            if (selectedOption.value) {
                var subcategorias = JSON.parse(selectedOption.getAttribute('data-subcategorias'));
                
                for (var i = 0; i < subcategorias.length; i++) {
                    var subcategoria = subcategorias[i];
                    var option = document.createElement('option');
                    option.value = subcategoria.id;
                    option.textContent = subcategoria.nom;
                    
                    var oldValue = subcategoriaSelect.getAttribute('data-old-value');
                    if (oldValue == subcategoria.id) {
                        option.selected = true;
                    }
                    
                    subcategoriaSelect.appendChild(option);
                }
                
                subcategoriaSelect.disabled = false;
            } else {
                subcategoriaSelect.disabled = true;
            }
        };
        
        // Cargar subcategorías al inicio si hay categoría seleccionada
        if (categoriaSelect.value) {
            categoriaSelect.onchange();
        }
    }
};

