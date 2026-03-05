@if(!empty($sedeSeleccionada))
    <div class="kpi-grid" style="margin-bottom: 0;">
        <div class="kpi-card kpi-card--active">
            <div class="kpi-icon-wrap">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="kpi-body">
                <span class="kpi-label">Resueltas ({{ $sedeSeleccionada->nom }})</span>
                <span class="kpi-value">{{ $totalIncidenciasResueltas ?? 0 }}</span>
            </div>
        </div>

        <div class="kpi-card kpi-card--pending">
            <div class="kpi-icon-wrap">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div class="kpi-body">
                <span class="kpi-label">Pendientes ({{ $sedeSeleccionada->nom }})</span>
                <span class="kpi-value">{{ $totalIncidenciasPendientes ?? 0 }}</span>
            </div>
        </div>
    </div>
@else
    <div class="empty-state">
        <i class="fa-regular fa-circle-question"></i>
        <p>Selecciona una sede para ver el volumen de incidencias.</p>
    </div>
@endif
