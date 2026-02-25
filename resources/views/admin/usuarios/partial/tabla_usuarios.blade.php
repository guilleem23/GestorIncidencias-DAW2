<table class="usuarios-table">
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
                <td>{{ $usuario->name }}</td>
                <td>{{ $usuario->username }}</td>
                <td>{{ $usuario->email }}</td>
                <td><span class="usuarios-badge usuarios-badge-{{ $usuario->rol }}">{{ $usuario->rol }}</span>
                </td>
                <td>{{ $usuario->sede->nom ?? '-' }}</td>
                <td>
                    <span class="usuarios-badge usuarios-badge-{{ $usuario->actiu ? 'activo' : 'inactivo' }}">
                        {{ $usuario->actiu ? 'Sí' : 'No' }}
                    </span>
                </td>
                <td>
                    <div class="usuarios-actions-flex">
                        <button type="button" class="btn btn-secondary btn-editar-usuario"
                            name="editar_usuario" value="{{ $usuario->id }}"><i
                                class="fa-solid fa-pen-to-square"></i></button>
                        @if ($usuario->rol !== 'administrador')
                            <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" method="POST"
                                style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" name="boton_eliminar" class="btn btn-danger btn-eliminar"
                                    value="{{ $usuario->id }}"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
        @if($usuarios->isEmpty())
            <tr>
                <td colspan="8" style="text-align: center; padding: 2rem;">No se encontraron usuarios con los filtros aplicados.</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="usuarios-pagination-wrapper">
    {{-- Links de paginación (usamos bootstrap-4 para evitar el texto de info integrado de bootstrap-5 de Laravel si es muy intrusivo, o simplemente lo envolvemos) --}}
    {{ $usuarios->links('pagination::bootstrap-4') }}

    {{-- Mensaje de información en español debajo --}}
    <div class="usuarios-pagination-info">
        @if ($usuarios->total() > 0)
            Mostrando {{ $usuarios->firstItem() }} a {{ $usuarios->lastItem() }} de {{ $usuarios->total() }} usuarios
        @else
            No se encontraron usuarios
        @endif
    </div>
</div>
