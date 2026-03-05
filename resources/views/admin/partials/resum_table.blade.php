<div class="table-panel">
    <h3 class="card-title"><i class="fa-solid fa-table"></i> Incidencias resueltas por técnico y categoría</h3>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Técnico</th>
                    @foreach($categorias as $categoria)
                        <th>{{ $categoria->nom }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($tecnicos as $tecnico)
                    <tr>
                        <td>{{ $tecnico->name }}</td>
                        @foreach($categorias as $categoria)
                            @php
                                // Si no hay dato para esa combinación, mostramos 0
                                $totalCelda = $resueltasPorTecnicoCategoria[(int) $tecnico->id][(int) $categoria->id] ?? 0;
                            @endphp
                            <td>{{ $totalCelda }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 1 + $categorias->count() }}" class="empty-row">
                            <i class="fa-solid fa-circle-info"></i> No hay incidencias resueltas para esta sede
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
