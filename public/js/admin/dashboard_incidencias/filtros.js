document.addEventListener('DOMContentLoaded', function () {
    const sedeFilter = document.getElementById('filter-sede');
    const dataContainer = document.getElementById('dashboard-data-container');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!sedeFilter || !dataContainer) {
        return;
    }

    function fetchDashboardData() {
        const formData = new URLSearchParams();
        const headers = {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest'
        };

        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken;
        }

        if (sedeFilter.value) {
            formData.set('sede_id', sedeFilter.value);
        }

        dataContainer.style.opacity = '0.5';

        fetch(window.location.pathname, {
            method: 'POST',
            headers,
            body: formData.toString()
        })
            .then(response => response.text())
            .then(html => {
                dataContainer.innerHTML = html;
            })
            .catch(error => {
                console.error('Error al cargar datos del dashboard:', error);
            })
            .finally(() => {
                dataContainer.style.opacity = '1';
            });
    }

    sedeFilter.onchange = fetchDashboardData;
});
