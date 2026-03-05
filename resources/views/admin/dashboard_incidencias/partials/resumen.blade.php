@if ($hasSedeSelected)
    <div class="filters-container">
        <div class="filters-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
            <div class="filter-group">
                <label class="filter-label"><i class="fa-solid fa-check-circle"></i> Totales resueltas</label>
                <div class="filter-input">
                    {{ $totalResueltas }} incidencias
                </div>
            </div>
            <div class="filter-group">
                <label class="filter-label"><i class="fa-solid fa-clock"></i> Totales pendientes</label>
                <div class="filter-input" >
                    {{ $totalPendientes }} incidencias
                </div>
            </div>
        </div>
    </div>

    <div class="filters-container">
        <h3 style="margin-bottom: 1rem;">
            <i class="fa-solid fa-user-gear"></i>
            Incidencias totales por tecnico separadas por categoria
        </h3>

        <div id="incidencias-table-container" style="overflow-x:auto;">
            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th>Tecnico</th>
                        @foreach ($categoriasDesglose as $categoria)
                            <th>{{ $categoria }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($desgloseTecnicoCategoria as $tecnico => $categorias)
                        <tr>
                            <td><strong>{{ $tecnico }}</strong></td>
                            @foreach ($categoriasDesglose as $categoria)
                                <td>{{ $categorias->get($categoria, 0) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
