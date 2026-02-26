@extends('layouts.admin')

@section('title', 'Nexton Admin - Gestión de Usuarios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_usuario.css') }}">
@endpush

@section('content')
    <div class="usuarios-container">
        <div class="usuarios-header-layout">
            <div class="usuarios-header-title">
                <h1><i class="fa-solid fa-users"></i> Gestión de Usuarios</h1>
            </div>
            <div class="usuarios-header-actions">
                <button type="button" class="usuarios-btn-crear btn-crear-usuario" data-bs-toggle="modal"
                    data-bs-target="#modalCrearUsuario">
                    Crear Usuario
                </button>
            </div>
        </div>
        <div class="usuarios-filters-bar">
            <input type="text" id="search-input" class="usuarios-search-input usuarios-search-input-large"
                placeholder="Buscar por ID, nombre, username o correo..." oninput="buscarUsuariosInput()">
            <select id="rol-filter" class="usuarios-filter-select" onchange="aplicarFiltrosCheck()">
                <option value="">Todos los roles</option>
                @foreach ($roles as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            <select id="sede-filter" class="usuarios-filter-select usuarios-filter-select-large" onchange="aplicarFiltrosCheck()">
                <option value="">Todas las sedes</option>
                @foreach ($sedes as $sede)
                    <option value="{{ $sede->id }}">{{ $sede->nom }}</option>
                @endforeach
            </select>
            <select id="activo-filter" class="usuarios-filter-select usuarios-filter-select-large" onchange="aplicarFiltrosCheck()">
                <option value="">Todos los estados</option>
                <option value="1">Sólo Activos</option>
                <option value="0">Sólo Inactivos</option>
            </select>
            <select id="per-page-filter" class="usuarios-filter-select" onchange="aplicarFiltrosCheck()">
                <option value="5" selected>5 por pág.</option>
                <option value="10">10 por pág.</option>
                <option value="25">25 por pág.</option>
                <option value="50">50 por pág.</option>
            </select>
            <button type="button" id="btn-limpiar-filtros" class="usuarios-btn-limpiar" title="Limpiar filtros" onclick="limpiarFiltrosClick()">
                <i class="fa-solid fa-eraser"></i>
            </button>
        </div>
        @php
            $erroresEditar = collect(['edit_name', 'edit_email', 'edit_sede_id', 'edit_rol', 'edit_password']);
            $hayErroresEditar =
                $errors->has('error_editar') || collect($errors->keys())->intersect($erroresEditar)->isNotEmpty();
        @endphp
        @if ($errors->any() && !$hayErroresEditar && !$errors->has('error_eliminar'))
            <script>
                window.modalUsuarioOpen = true;
            </script>
        @endif
        @if ($hayErroresEditar)
            <div class="alert alert-danger mt-1 mb-2">
                <strong>Error al editar usuario:</strong>
                <ul style="margin-bottom:0;">
                    @if ($errors->has('error_editar'))
                        <li>{{ $errors->first('error_editar') }}</li>
                    @endif
                    @foreach ($erroresEditar as $campo)
                        @if ($errors->has($campo))
                            <li>{{ $errors->first($campo) }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif
        @if ($errors->has('error_eliminar'))
            <div class="alert alert-danger mt-1 mb-2">
                <strong>Error al eliminar usuario</strong>
                <p style="margin-bottom:0;">{{ $errors->first('error_eliminar') }}</p>
            </div>
        @endif
        <div id="usuarios-table-container" class="usuarios-table-container">
            @include('admin.usuarios.partial.tabla_usuarios')
        </div>

        {{-- Contenedores para SweetAlert --}}
        @if (session('success'))
            <div id="swal-success" data-message="{{ session('success') }}"></div>
        @endif
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
