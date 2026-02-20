<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Nexton</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo-header">
            <i class="fas fa-cube logo-icon"></i>
            <span class="logo-text">Nexton</span>
        </div>
        <div class="user-info">
            <span class="user-name">
                <i class="fas fa-user-shield"></i> {{ auth()->user()->name }}
            </span>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </header>

    <!-- Contenido Principal -->
    <div class="container">
        <a href="{{ route('admin.usuarios.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Volver al Panel
        </a>

        <h1 class="page-title">Gestión de Usuarios</h1>
        <p class="page-subtitle">Ver y filtrar usuarios del sistema por sede y rol</p>

        @if(session('success'))
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filtros -->
        <div class="filters-container">
            <form method="GET" action="{{ route('admin.usuarios.listado') }}">
                <div class="filters-grid">
                    <!-- Filtro por Sede -->
                    <div class="filter-group">
                        <label><i class="fas fa-building"></i> Sede</label>
                        <select name="sede" class="filter-select">
                            <option value="">Todas las sedes</option>
                            @foreach($sedes as $sede)
                                <option value="{{ $sede->id }}" {{ $sedeFilter == $sede->id ? 'selected' : '' }}>
                                    {{ $sede->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro por Rol -->
                    <div class="filter-group">
                        <label><i class="fas fa-id-badge"></i> Rol</label>
                        <select name="rol" class="filter-select">
                            <option value="">Todos los roles</option>
                            @foreach($roles as $value => $label)
                                <option value="{{ $value }}" {{ $rolFilter == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro por Estado -->
                    <div class="filter-group">
                        <label><i class="fas fa-toggle-on"></i> Estado</label>
                        <select name="estado" class="filter-select">
                            <option value="activos" {{ $estadoFilter == 'activos' ? 'selected' : '' }}>Solo activos</option>
                            <option value="inactivos" {{ $estadoFilter == 'inactivos' ? 'selected' : '' }}>Solo inactivos</option>
                            <option value="todos" {{ $estadoFilter == 'todos' ? 'selected' : '' }}>Todos</option>
                        </select>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="filter-actions">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.usuarios.listado') }}" class="btn-clear">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla de Usuarios -->
        <div class="table-container">
            <div class="table-header">
                <div class="table-title">
                    <i class="fas fa-users"></i> Usuarios
                </div>
                <div class="table-count">
                    {{ $usuarios->count() }} resultado(s)
                </div>
            </div>

            @if($usuarios->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Sede</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $usuario)
                            <tr>
                                <td>
                                    <strong>{{ $usuario->name }}</strong>
                                </td>
                                <td>{{ $usuario->email }}</td>
                                <td>
                                    <i class="fas fa-building" style="color: var(--color-primario); margin-right: 0.5rem;"></i>
                                    {{ $usuario->sede->nom }}
                                </td>
                                <td>
                                    @if($usuario->rol === 'administrador')
                                        <span class="badge badge-admin">Administrador</span>
                                    @elseif($usuario->rol === 'client')
                                        <span class="badge badge-client">Cliente</span>
                                    @elseif($usuario->rol === 'gestor')
                                        <span class="badge badge-gestor">Gestor</span>
                                    @elseif($usuario->rol === 'tecnic')
                                        <span class="badge badge-tecnic">Técnico</span>
                                    @endif
                                </td>
                                <td>
                                    @if($usuario->actiu)
                                        <span class="badge badge-active">
                                            <i class="fas fa-check"></i> Activo
                                        </span>
                                    @else
                                        <span class="badge badge-inactive">
                                            <i class="fas fa-times"></i> Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($usuario->actiu)
                                        <form method="POST" action="{{ route('admin.usuarios.baja', $usuario->id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn-action" onclick="return confirm('¿Dar de baja a {{ $usuario->name }}?')">
                                                <i class="fas fa-user-times"></i> Dar de baja
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.usuarios.alta', $usuario->id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn-action" onclick="return confirm('¿Reactivar a {{ $usuario->name }}?')">
                                                <i class="fas fa-user-check"></i> Reactivar
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <i class="fas fa-users-slash"></i>
                    <p>No se encontraron usuarios con los filtros seleccionados</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
