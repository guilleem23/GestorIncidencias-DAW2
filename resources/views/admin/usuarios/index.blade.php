@extends('layouts.admin')

@section('title', 'Nexton Admin - Gestión de Usuarios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_usuario.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin_categorias.css') }}">
    <link rel="stylesheet" href="{{ asset('css/gestor_historial.css') }}">
    <link rel="stylesheet" href="{{ asset('css/gestor_incidencia_detail.css') }}">
@endpush

@section('content')
<div class="categorias-container">
    <div class="categorias-header">
        <h1><i class="fa-solid fa-users"></i> Gestión de Usuarios</h1>
        <div class="header-actions">
            <button type="button" class="btn-crear btn-crear-categoria" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
                <i class="fa-solid fa-plus"></i> Crear Usuario
            </button>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="filters-container">
        <div class="filters-grid">
            <div class="filter-group filter-search">
                <label class="filter-label"><i class="fa-solid fa-magnifying-glass"></i> Buscar</label>
                <input type="text" id="search-input" class="filter-input" placeholder="ID, nombre, username o correo...">
            </div>
            <div class="filter-group">
                <label class="filter-label"><i class="fa-solid fa-user-tag"></i> Rol</label>
                <select id="rol-filter" class="filter-select">
                    <option value="">Todos los roles</option>
                    @foreach ($roles as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label"><i class="fa-solid fa-building"></i> Sede</label>
                <select id="sede-filter" class="filter-select">
                    <option value="">Todas las sedes</option>
                    @foreach ($sedes as $sede)
                        <option value="{{ $sede->id }}">{{ $sede->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label"><i class="fa-solid fa-circle-check"></i> Estado</label>
                <select id="activo-filter" class="filter-select">
                    <option value="">Todos</option>
                    <option value="1">Sólo Activos</option>
                    <option value="0">Sólo Inactivos</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label"><i class="fa-solid fa-list-ol"></i> Mostrar</label>
                <select id="per-page-filter" class="filter-select">
                    <option value="5" selected>5 por pág.</option>
                    <option value="10">10 por pág.</option>
                    <option value="25">25 por pág.</option>
                    <option value="50">50 por pág.</option>
                </select>
            </div>
            <div class="filter-group filter-actions">
                <button type="button" id="btn-limpiar-filtros" class="btn-clear-filters" title="Limpiar filtros">
                    <i class="fa-solid fa-xmark"></i> Limpiar
                </button>
            </div>
        </div>
    </div>

    {{-- Mensajes --}}
    @php
        $erroresEditar = collect(['edit_name', 'edit_email', 'edit_sede_id', 'edit_rol', 'edit_password']);
        $hayErroresEditar = $errors->has('error_editar') || collect($errors->keys())->intersect($erroresEditar)->isNotEmpty();
    @endphp
        @if ($errors->any() && !$hayErroresEditar && !$errors->has('error_eliminar'))
            <script>
                window.modalUsuarioOpen = true;
            </script>
        @endif
        @if ($hayErroresEditar)
            <div class="alert-custom alert-error-custom" style="margin-bottom: 1.5rem;">
                <i class="fa-solid fa-circle-xmark"></i>
                <div style="display:flex; flex-direction:column; gap:0.25rem;">
                    <strong>Error al editar usuario:</strong>
                    @if ($errors->has('error_editar'))
                        <span>{{ $errors->first('error_editar') }}</span>
                    @endif
                    @foreach ($erroresEditar as $campo)
                        @if ($errors->has($campo))
                            <span>{{ $errors->first($campo) }}</span>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
        @if ($errors->has('error_eliminar'))
            <div class="alert-custom alert-error-custom" style="margin-bottom: 1.5rem;">
                <i class="fa-solid fa-circle-xmark"></i>
                <div style="display:flex; flex-direction:column; gap:0.25rem;">
                    <strong>Error al eliminar usuario</strong>
                    <span>{{ $errors->first('error_eliminar') }}</span>
                </div>
            </div>
        @endif

        <div id="usuarios-table-container">
            @include('admin.usuarios.partial.tabla_usuarios')
        </div>

        {{-- Contenedores para SweetAlert --}}
        @if (session('success'))
            <div id="swal-success" data-message="{{ session('success') }}"></div>
        @endif
    </div>


    </div>

    <!-- Modal Bootstrap para crear usuario -->
    <div class="modal fade usuarios-modal" id="modalCrearUsuario" tabindex="-1" aria-labelledby="modalCrearUsuarioLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalCrearUsuarioLabel">Crear Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body usuarios-modal-body bg-dark text-white" id="modal-crear-content">
                    @include('admin.usuarios.partial.crear_usuario')
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Bootstrap para editar usuario -->
    <div class="modal fade usuarios-modal" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalEditarUsuarioLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body usuarios-modal-body bg-dark text-white" id="modal-editar-content">
                    <!-- El contenido del formulario de edición se cargará dinámicamente con JavaScript -->
                </div>
            </div>
        </div>
    </div>
    <!-- Asegúrate de tener Bootstrap JS y CSS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/admin/usuarios/sweetAlerts.js') }}"></script>
    <script src="{{ asset('js/admin/usuarios/modal_usuario.js') }}"></script>
    <script src="{{ asset('js/admin/usuarios/validar_crear_usuario.js') }}"></script>
    <script src="{{ asset('js/admin/usuarios/validar_editar_usuario.js') }}"></script>
    <script src="{{ asset('js/admin/usuarios/filtros.js') }}"></script>
@endsection
