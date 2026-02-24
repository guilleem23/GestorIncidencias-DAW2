@if($incidencies->isEmpty())
    <div class="empty-state-box">
        <i class="fa-solid fa-folder-open fa-3x"></i>
        <p>No se encontraron incidencias con los filtros seleccionados.</p>
    </div>
@else
    <table class="historial-table">
        <thead>
            <tr>
                <th>Incidencia</th>
                <th>Cliente</th>
                <th>Técnico Asignado</th>
                <th>Prioridad</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incidencies as $incidencia)
            <tr>
                <td>
                    <span class="info-title">{{ $incidencia->titol }}</span>
                </td>
                <td>
                    @if($incidencia->cliente)
                        <span class="info-title">{{ $incidencia->cliente->name }}</span>
                    @else
                        <span class="text-secondary">-</span>
                    @endif
                </td>
                <td>
                    @if($incidencia->tecnico)
                        <div class="info-text">
                            <span class="info-title">{{ $incidencia->tecnico->name }}</span>
                            <span class="info-sub">{{ $incidencia->tecnico->email }}</span>
                        </div>
                    @else
                        <span class="text-secondary"><i class="fa-solid fa-user-minus"></i> Sin asignar</span>
                    @endif
                </td>
                <td>
                    @if($incidencia->prioritat)
                        @if($incidencia->prioritat === 'alta')
                            <span class="priority-badge priority-alta">
                                <i class="fa-solid fa-arrow-up"></i> Alta
                            </span>
                        @elseif($incidencia->prioritat === 'mitjana')
                            <span class="priority-badge priority-mitjana">
                                <i class="fa-solid fa-minus"></i> Media
                            </span>
                        @else
                            <span class="priority-badge priority-baixa">
                                <i class="fa-solid fa-arrow-down"></i> Baja
                            </span>
                        @endif
                    @else
                        <span class="text-secondary">-</span>
                    @endif
                </td>
                <td>
                    @if($incidencia->estat === 'Sense assignar')
                        <span class="status-badge badge-inactive">Sense assignar</span>
                    @elseif($incidencia->estat === 'Assignada')
                        <span class="status-badge status-assignada">Assignada</span>
                    @elseif($incidencia->estat === 'En treball')
                        <span class="status-badge status-treball">En treball</span>
                    @elseif($incidencia->estat === 'Resolta')
                        <span class="status-badge status-resolta">Resolta</span>
                    @elseif($incidencia->estat === 'Tancada')
                        <span class="status-badge badge-active">Tancada</span>
                    @else
                        <span class="status-badge badge-active">{{ $incidencia->estat }}</span>
                    @endif
                </td>
                <td class="date-cell">
                    {{ $incidencia->created_at->format('d/m/Y H:i') }}
                </td>
                <td>
                    <div class="actions-cell">
                        <a href="{{ route('gestor.incidencias.show', $incidencia->id) }}" class="btn-icon btn-view" title="Ver Detalles">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="{{ route('gestor.incidencias.edit', $incidencia->id) }}" class="btn-icon btn-edit" title="Editar Incidencia">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($incidencies->hasPages())
        <div class="pagination-wrapper">
            {{ $incidencies->links() }}
        </div>
    @endif
@endif
