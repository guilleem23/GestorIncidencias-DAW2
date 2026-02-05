<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexton Admin - Dashboard Global</title>
    @vite(['resources/css/admin_dashboard.css'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Include Sidebar -->
    @include('admin.sidebar_admin')

    <div class="main-content">
        <div class="dashboard-header">
            <h1>Visión Global de Operaciones</h1>
            <p style="color: var(--text-secondary); margin-top: 0.5rem;">Estado del sistema en tiempo real y alertas críticas.</p>
        </div>

        <!-- Panel Superior: KPIs -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-info">
                    <h3>Total Usuarios</h3>
                    <div class="kpi-value">12,450</div>
                </div>
                <div class="kpi-icon kpi-purple">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-info">
                    <h3>Incidencias Activas</h3>
                    <div class="kpi-value">34</div>
                </div>
                <div class="kpi-icon kpi-orange">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-info">
                    <h3>Tiempo Medio Res.</h3>
                    <div class="kpi-value">2.4h</div>
                </div>
                <div class="kpi-icon kpi-blue">
                    <i class="fa-regular fa-clock"></i>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-info">
                    <h3>Satisfacción Global</h3>
                    <div class="kpi-value">4.8/5</div>
                </div>
                <div class="kpi-icon kpi-green">
                    <i class="fa-solid fa-star"></i>
                </div>
            </div>
        </div>

        <!-- Sección Central: Gráficos -->
        <div class="charts-grid">
            <div class="chart-card">
                <h3 class="card-title">Incidencias por Sede</h3>
                <div class="bar-chart">
                    <div class="bar-group">
                        <div class="bar bar-bcn"></div>
                        <span class="bar-label">BCN</span>
                    </div>
                    <div class="bar-group">
                        <div class="bar bar-berlin"></div>
                        <span class="bar-label">BER</span>
                    </div>
                    <div class="bar-group">
                        <div class="bar bar-montreal"></div>
                        <span class="bar-label">MTL</span>
                    </div>
                </div>
            </div>
            <div class="chart-card">
                <h3 class="card-title">Tipología de Problemas</h3>
                <div class="donut-chart-container">
                    <div class="donut"></div>
                    <div class="donut-legend">
                        <ul>
                            <li><span class="dot dot-orange"></span> Hardware (33%)</li>
                            <li><span class="dot dot-blue"></span> Software (33%)</li>
                            <li><span class="dot dot-purple"></span> Redes (33%)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección Inferior: Tabla de Gestión -->
        <div class="table-panel">
            <h3 class="card-title">Incidencias Pendientes de Asignación (Global)</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Sede</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#INC-2024-001</td>
                        <td>Fallo en servidor principal de archivos</td>
                        <td>Barcelona</td>
                        <td>Hace 10 min</td>
                        <td><span class="status-badge">Sense assignar</span></td>
                        <td><button class="btn-action">Asignar Técnico</button></td>
                    </tr>
                    <tr>
                        <td>#INC-2024-002</td>
                        <td>Error de conexión en planta 3</td>
                        <td>Berlín</td>
                        <td>Hace 25 min</td>
                        <td><span class="status-badge">Sense assignar</span></td>
                        <td><button class="btn-action">Asignar Técnico</button></td>
                    </tr>
                    <tr>
                        <td>#INC-2024-003</td>
                        <td>Pantallas sala de reuniones no encienden</td>
                        <td>Montreal</td>
                        <td>Hace 1 hora</td>
                        <td><span class="status-badge">Sense assignar</span></td>
                        <td><button class="btn-action">Asignar Técnico</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
