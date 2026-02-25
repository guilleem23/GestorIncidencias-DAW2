// JavaScript para cargar subcategorías dinámicamente
document.getElementById('categoria_id').addEventListener('change', function() {
    const subcategoriaSelect = document.getElementById('subcategoria_id');
    const selectedOption = this.options[this.selectedIndex];
    
    // Limpiar subcategorías
    subcategoriaSelect.innerHTML = '<option value="">Selecciona una subcategoria...</option>';
    
    if (selectedOption.value) {
        const subcategorias = JSON.parse(selectedOption.getAttribute('data-subcategorias'));
        
        subcategorias.forEach(function(subcategoria) {
            const option = document.createElement('option');
            option.value = subcategoria.id;
            option.textContent = subcategoria.nom;
            
            // Mantener selección anterior si existe (para casos de validación)
            const oldSubcategoria = subcategoriaSelect.getAttribute('data-old-value');
            if (oldSubcategoria == subcategoria.id) {
                option.selected = true;
            }
            
            subcategoriaSelect.appendChild(option);
        });
        
        subcategoriaSelect.disabled = false;
    } else {
        subcategoriaSelect.disabled = true;
    }
});

// Si hay un valor anterior (por validación), cargar las subcategorías
window.addEventListener('DOMContentLoaded', function() {
    const categoriaSelect = document.getElementById('categoria_id');
    if (categoriaSelect.value) {
        categoriaSelect.dispatchEvent(new Event('change'));
    }
});
