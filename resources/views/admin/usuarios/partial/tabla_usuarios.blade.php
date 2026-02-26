<div class="table-container">
    @if($usuarios->isEmpty())
        <div class="empty-state-box">
            <i class="fa-solid fa-users fa-3x"></i>
            <p>No se encontraron usuarios con los filtros aplicados.</p>
        </div>
    @else
        <table class="historial-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Sede</th>
                    <th>Activo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->id }}</td>
                        <td class="cell-truncate">{{ Str::limit($usuario->name, 35) }}</td>
                        <td><span class="username-tag">{{ '@' . $usuario->username }}</span></td>
                        <td class="cell-truncate">{{ $usuario->email }}</td>
                        <td>
                            @if($usuario->rol === 'administrador')
                                <span class="priority-badge priority-alta"><i class="fa-solid fa-shield-halved"></i> Admin</span>
                            @elseif($usuario->rol === 'gestor')
                                <span class="priority-badge priority-mitjana"><i class="fa-solid fa-user-tie"></i> Gestor</span>
                            @else
                                <span class="priority-badge priority-baixa"><i class="fa-solid fa-wrench"></i> Técnico</span>
                            @endif
                        </td>
                        <td>{{ $usuario->sede?->nom ?? '-' }}</td>
                        <td>
                            @if($usuario->actiu)
                                <span class="status-badge status-resolta"><i class="fa-solid fa-check"></i> Sí</span>
                            @else
                                <span class="status-badge badge-inactive"><i class="fa-solid fa-xmark"></i> No</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions-cell">
                                <button type="button" class="btn-icon btn-edit btn-editar-usuario"
                                    name="editar_usuario" title="Editar Usuario" value="{{ $usuario->id }}">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                @if ($usuario->rol !== 'administrador' && $usuario->actiu)
                                    <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" method="POST"
                                        style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" name="boton_eliminar" class="btn-icon btn-delete btn-eliminar"
                                            title="Eliminar" value="{{ $usuario->id }}">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($usuarios->hasPages())
            <div class="pagination-wrapper" style="margin-top: 2rem;">
                {{ $usuarios->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>
