document.addEventListener('DOMContentLoaded', function () {
    const sedesList = document.getElementById('sedes-list');
    const sedeDetail = document.getElementById('sede-detail');

    if (!sedesList) return;

    const apiSedesUrl = sedesList.dataset.apiSedes;
    const apiResumUrl = sedesList.dataset.apiResum;

    fetch(apiSedesUrl)
        .then(r => r.json())
        .then(sedes => {
            sedes.forEach(sede => {
                const card = document.createElement('div');
                card.className = 'kpi-card kpi-card--users';
                card.setAttribute('data-sede-id', sede.id);
                card.style.cursor = 'pointer';
                card.innerHTML = `
                    <div><i></i></div>
                    <div><span>${sede.nom}</span></div>
                `;
                card.addEventListener('click', () => selectSede(sede.id, card));
                sedesList.appendChild(card);
            });
        });

    function selectSede(id, card) {
        document.querySelectorAll('#sedes-list .kpi-card').forEach(c => {
            c.style.borderColor = '';
            c.style.boxShadow = '';
        });


        fetch(`${apiResumUrl}/${id}`)
            .then(r => r.json())
            .then(data => {
                document.getElementById('count-resueltas').textContent = data.resueltas;
                document.getElementById('count-pendientes').textContent = data.pendientes;

                const thead = document.getElementById('tabla-head');
                const tbody = document.getElementById('tabla-body');

                if (data.tabla.length === 0) {
                    thead.innerHTML = '<tr><th>Técnico</th><th>Total</th></tr>';
                    tbody.innerHTML = '<tr><td colspan="2" class="empty-row"><i class="fa-solid fa-check-circle"></i> No hay incidencias resueltas en esta sede</td></tr>';
                    sedeDetail.style.display = 'block';
                    return;
                }

                let headerRow = '<tr><th>Técnico</th>';
                data.categorias.forEach(cat => headerRow += `<th>${cat}</th>`);
                headerRow += '<th>Total</th></tr>';
                thead.innerHTML = headerRow;

                tbody.innerHTML = data.tabla.map(fila => {
                    let row = `<tr><td><span class="id-badge">${fila.tecnico}</span></td>`;
                    data.categorias.forEach(cat => row += `<td>${fila.categorias[cat] || 0}</td>`);
                    row += `<td><strong>${fila.total}</strong></td></tr>`;
                    return row;
                }).join('');

                sedeDetail.style.display = 'block';
            });
    }
});