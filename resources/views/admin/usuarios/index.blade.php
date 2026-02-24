@extends('layouts.admin')

@section('title', 'Nexton Admin - Gestión de Usuarios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_usuario.css') }}">
@endpush

@section('content')
    <div class="categorias-container">
        <div class="categorias-header">
            <h1><i class="fa-solid fa-building"></i> Gestión de Sedes</h1>
            <div class="header-actions">
                <button type="button" class="btn-crear btn-crear-usuario" data-bs-toggle="modal"
                    data-bs-target="#modalCrearUsuario">
                    Crear Usuario
                </button>
            </div>

            <div class="usuarios-filters-bar"
                style="display: flex; gap: 1rem; margin-bottom: 2rem; background: #232323; padding: 1rem; border-radius: 8px; align-items: center;">
                <input type="text" class="search-input" placeholder="Buscar por nombre, username o email..."
                    style="flex: 2; background: #181818; color: #fff; border: 1px solid #374151; border-radius: 6px; padding: 0.5rem;">
                <select class="filter-select"
                    style="flex: 1; background: #181818; color: #fff; border: 1px solid #374151; border-radius: 6px; padding: 0.5rem;">
                    <option value="">Rol</option>
                    @foreach ($roles as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select class="filter-select"
                    style="flex: 1; background: #181818; color: #fff; border: 1px solid #374151; border-radius: 6px; padding: 0.5rem;">
                    <option value="">Sede</option>
                    @foreach ($sedes as $sede)
                        <option value="{{ $sede->id }}">{{ $sede->nom }}</option>
                    @endforeach
                </select>
                <select class="filter-select"
                    style="flex: 1; background: #181818; color: #fff; border: 1px solid #374151; border-radius: 6px; padding: 0.5rem;">
                    <option value="">Activo</option>
                    <option value="1">Sí</option>
                    <option value="0">No</option>
                </select>
                <button class="search-btn"
                    style="background: #2563eb; color: #fff; border: none; border-radius: 6px; padding: 0.5rem 1rem; cursor: pointer;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>

            <div class="incidents-table-container">
                <table class="incidents-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
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
                                <td>{{ $usuario->email }}</td>
                                <td>{{ $usuario->rol }}</td>
                                <td>{{ $usuario->sede->nom ?? '-' }}</td>
                                <td>{{ $usuario->actiu ? 'Sí' : 'No' }}</td>
                                <td>
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
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Bootstrap para crear usuario -->
        <div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-labelledby="modalCrearUsuarioLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCrearUsuarioLabel">Crear Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body" id="modal-crear-content">
                        @include('admin.usuarios.partial.crear_usuario')
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Bootstrap para editar usuario -->
        <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditarUsuarioLabel">Editar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body" id="modal-editar-content">
                        <!-- El contenido del formulario de edición se cargará dinámicamente con JavaScript -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Asegúrate de tener Bootstrap JS y CSS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ asset('js/admin/usuarios/sweetAlerts.js') }}"></script>
        <script src="{{ asset('js/admin/usuarios/modal_usuario.js') }}"></script>
        <script src="{{ asset('js/admin/usuarios/validacion_crear_usuario.js') }}"></script>
    @endsection
