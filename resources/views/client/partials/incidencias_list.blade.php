<div class="filter-count">
    <i class="fas fa-list"></i>
    <span>{{ $incidencies->count() }} incidencias encontradas</span>
</div>

@if($incidencies->count() > 0)
    @foreach($incidencies as $incidencia)
        <div class="incidencia-card">
            <div class="incidencia-header">
                <div style="flex: 1;">
                    <h3 class="incidencia-title">{{ $incidencia->titol }}</h3>
                    <div class="incidencia-meta">
                        <div class="meta-item">
                            <i class="fas fa-hashtag"></i>
                            <span>#{{ $incidencia->id }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-tag"></i>
                            <span>{{ $incidencia->categoria->nom }} / {{ $incidencia->subcategoria->nom }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <span>{{ $incidencia->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($incidencia->tecnico)
                            <div class="meta-item">
                                <i class="fas fa-user-cog"></i>
                                <span>Técnico: {{ $incidencia->tecnico->name }}</span>
                            </div>
                        @endif
                        @if($incidencia->comentarios && $incidencia->comentarios->count())
                            <div class="meta-item">
                                <i class="fas fa-comments"></i>
                                <span>{{ $incidencia->comentarios->count() }} comentarios</span>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="incidencia-badges">
                    @if($incidencia->prioritat)
                        @if($incidencia->prioritat === 'alta')
                            <span class="priority-badge priority-alta">
                                <i class="fas fa-exclamation-circle"></i> Alta
                            </span>
                        @elseif($incidencia->prioritat === 'mitjana')
                            <span class="priority-badge priority-mitjana">
                                <i class="fas fa-minus-circle"></i> Media
                            </span>
                        @else
                            <span class="priority-badge priority-baixa">
                                <i class="fas fa-check-circle"></i> Baja
                            </span>
                        @endif
                    @endif

                    @if($incidencia->estat === 'Sense assignar')
                        <span class="badge badge-inactive">Sin asignar</span>
                    @elseif($incidencia->estat === 'Assignada')
                        <span class="status-badge status-assignada">Asignada</span>
                    @elseif($incidencia->estat === 'En treball')
                        <span class="status-badge status-treball">En trabajo</span>
                    @elseif($incidencia->estat === 'Resolta')
                        <span class="status-badge status-resolta">Resuelta</span>
                    @else
                        <span class="badge badge-active">Cerrada</span>
                    @endif
                </div>
            </div>

            <div class="incidencia-description">
                {{ Str::limit($incidencia->descripcio, 150) }}
            </div>

            <div class="incidencia-actions">
                <a href="{{ route('client.incidencias.show', $incidencia->id) }}" class="btn btn-primary">
                    <i class="fas fa-eye"></i> Ver Detalles
                </a>

                @if($incidencia->estat === 'Resolta')
                    <form method="POST" action="{{ route('client.tancar', $incidencia->id) }}" class="form-close-incidencia" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check-double"></i> Cerrar
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endforeach
@else
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <p>No tienes incidencias con los filtros seleccionados</p>
    </div>
@endif
