// Script para la página de Resumen del Admin

// Cargar datos iniciales al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    sedeSelect = document.getElementById('sede-select');
    if (sedeSelect && sedeSelect.value) {
        cargarDatosSede(sedeSelect.value);
    }
});

function cambiarSede() {
    sedeSelect = document.getElementById('sede-select');
    sedeId = sedeSelect.value;
    cargarDatosSede(sedeId);
}

function cargarDatosSede(sedeId) {
    contadorResueltas = document.getElementById('contador-resueltas');
    contadorPendientes = document.getElementById('contador-pendientes');
    tablaBody = document.getElementById('tabla-resueltas-body');

    // Hacer petición AJAX para obtener los datos de la sede seleccionada
    fetch(`/admin/resum/sede/${sedeId}`)
        .then(response => response.json())
        .then(data => {
            // Actualizar los contadores
            contadorResueltas.textContent = data.resueltas;
            contadorPendientes.textContent = data.pendientes;
            
            // Actualizar la tabla de técnicos
            tablaBody.innerHTML = '';
            
            if (data.tecnicos && data.tecnicos.length > 0) {
                data.tecnicos.forEach(tecnico => {
                    fila = document.createElement('tr');
                    fila.innerHTML = `
                        <td>${tecnico.nombre}</td>
                        <td>${tecnico.software}</td>
                        <td>${tecnico.hardware}</td>
                    `;
                    tablaBody.appendChild(fila);
                });
            } else {
                fila = document.createElement('tr');
                fila.innerHTML = '<td colspan="3" class="sin-datos">No hay datos disponibles</td>';
                tablaBody.appendChild(fila);
            }
        })
        .catch(error => {
            console.error('Error al cargar datos:', error);
        });
}
