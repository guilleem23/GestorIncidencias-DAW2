<div class="table-container">
    @if($incidencias->isEmpty())
        <div class="empty-state-box">
            <i class="fa-solid fa-clipboard-list fa-3x"></i>
            <p>No se encontraron incidencias con los filtros aplicados.</p>
        </div>
    @else
        <table class="historial-table">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Cliente</th>
                    <th style="text-align: center;">Técnico Asignado</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Fecha Inicio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($incidencias as $incidencia)
                <tr class="{{ $incidencia->estat === 'Tancada' ? 'row-closed' : '' }}">
                    <td class="cell-truncate" style="max-width:200px">{{ $incidencia->titol }}</td>
                    <td>
                        @if($incidencia->cliente)
                            <span class="username-tag">{{ '@' . $incidencia->cliente->username }}</span>
                        @else
                            <span class="text-secondary">-</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if ($incidencia->tecnico)
                            <span class="username-tag">{{ '@' . $incidencia->tecnico->username }}</span>
                        @else
                            <span class="text-secondary" title="Sin asignar"><i class="fa-solid fa-user-minus"></i></span>
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
                    <td class="date-cell">{{ $incidencia->created_at?->format('d/m/Y') }}</td>
                    <td>
                        <div class="actions-cell">
                            <a href="{{ route('admin.incidencias.show', $incidencia->id) }}" class="btn-icon btn-view" title="Ver Detalles">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <button type="button" id="btn-edit-incidencia-{{ $incidencia->id }}" class="btn-icon btn-edit btn-editar-incidencia" data-id="{{ $incidencia->id }}" title="Editar">
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

        @if($incidencias->hasPages())
            <div class="pagination-wrapper" style="margin-top: 2rem;">
                {{ $incidencias->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>
