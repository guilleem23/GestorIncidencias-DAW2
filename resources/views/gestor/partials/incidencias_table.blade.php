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
                <th>Técnico</th>
                <th>Prioridad</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incidencies as $incidencia)
            <tr class="{{ $incidencia->estat === 'Tancada' ? 'row-closed' : '' }}">
                <td class="cell-truncate">{{ Str::limit($incidencia->titol, 40) }}</td>
                <td>
                    @if($incidencia->cliente)
                        <span class="username-tag">{{ '@' . $incidencia->cliente->username }}</span>
                    @else
                        <span class="text-secondary">-</span>
                    @endif
                </td>
                <td>
                    @if($incidencia->tecnico)
                        <span class="username-tag">{{ '@' . $incidencia->tecnico->username }}</span>
                    @else
                        <div style="text-align: center;"><span class="text-secondary" title="Sin asignar"><i class="fa-solid fa-user-minus"></i></span></div>
                    @endif
                </td>
                <td>
                    @if($incidencia->prioritat === 'alta')
                        <span class="priority-badge priority-alta"><i class="fa-solid fa-arrow-up"></i> Alta</span>
                    @elseif($incidencia->prioritat === 'mitjana')
                        <span class="priority-badge priority-mitjana"><i class="fa-solid fa-minus"></i> Media</span>
                    @elseif($incidencia->prioritat === 'baixa')
                        <span class="priority-badge priority-baixa"><i class="fa-solid fa-arrow-down"></i> Baja</span>
                    @else
                        <span class="text-secondary">-</span>
                    @endif
                </td>
                <td>
                    @if($incidencia->estat === 'Sense assignar')
                        <span class="status-badge badge-inactive">Sin asignar</span>
                    @elseif($incidencia->estat === 'Assignada')
                        <span class="status-badge status-assignada">Asignada</span>
                    @elseif($incidencia->estat === 'En treball')
                        <span class="status-badge status-treball">En trabajo</span>
                    @elseif($incidencia->estat === 'Resolta')
                        <span class="status-badge status-resolta">Resuelta</span>
                    @elseif($incidencia->estat === 'Tancada')
                        <span class="status-badge badge-active">Cerrada</span>
                    @else
                        <span class="status-badge badge-active">{{ $incidencia->estat }}</span>
                    @endif
                </td>
                <td class="date-cell">{{ $incidencia->created_at->format('d/m/Y') }}</td>
                <td>
                    <div class="actions-cell">
                        <a href="{{ route('gestor.incidencias.show', $incidencia->id) }}" class="btn-icon btn-view" title="Ver Detalles">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <button type="button" class="btn-icon btn-edit" title="Editar" name="editar_incidencia" data-id="{{ $incidencia->id }}">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button type="button" class="btn-icon btn-delete" title="Eliminar" data-id="{{ $incidencia->id }}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($incidencies->hasPages())
        <div class="pagination-wrapper" style="margin-top: 2rem;">
            {{ $incidencies->links('pagination::bootstrap-5') }}
        </div>
    @endif
@endif
