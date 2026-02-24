<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Gestionar Usuarios</title>
</head>

<body>
    @include('admin.navbar')
    <div style="max-width: 900px; margin: 2rem auto;">
        <h1>Gestión de Usuarios</h1>
        <!-- Botón para abrir el modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
            Crear Usuario
        </button>
        <table border="1" cellpadding="8" cellspacing="0" style="width:100%; margin-top:1rem;">
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
                            <button type="button" class="btn btn-secondary btn-editar-usuario" data-id="{{ $usuario->id }}">Editar</button>
                            @if ($usuario->rol !== 'administrador')
                                <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" method="POST"
                                    style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-eliminar" data-id="{{ $usuario->id }}">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Modal Bootstrap para crear usuario -->
    <div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-labelledby="modalCrearUsuarioLabel" aria-hidden="true">
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
    <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
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
</html>
