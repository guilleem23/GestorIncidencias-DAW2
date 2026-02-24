@extends('layouts.admin')

@section('title', 'Gestionar Categorías')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_categorias.css') }}">
@endpush

@section('content')
<div class="categorias-container">
    <div class="categorias-header">
        <h1><i class="fa-solid fa-layer-group"></i> Gestión de Categorías</h1>
        <div class="header-actions">
            <button type="button" class="btn-crear btn-crear-categoria" data-bs-toggle="modal" data-bs-target="#modalCrearCategoria">
                <i class="fa-solid fa-plus"></i> Nueva Categoría
            </button>
            <button type="button" class="btn-crear btn-crear-subcategoria" data-bs-toggle="modal" data-bs-target="#modalCrearSubcategoria">
                <i class="fa-solid fa-plus"></i> Nueva Subcategoría
            </button>
        </div>
    </div>

    {{-- Mensajes de éxito/error --}}
    @if (session('success'))
        <div class="alert-custom alert-success-custom">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert-custom alert-error-custom">
            <i class="fa-solid fa-circle-xmark"></i>
            @foreach ($errors->all() as $error)
                <span>{{ $error }}</span>
            @endforeach
        </div>
    @endif

    {{-- Listado de categorías --}}
    <div class="categorias-grid">
        @forelse ($categorias as $categoria)
            <div class="categoria-card">
                <div class="categoria-header">
                    <div class="categoria-info">
                        <div class="categoria-title">
                            <button class="btn-toggle" onclick="toggleSubcategorias({{ $categoria->id }})">
                                <i class="fa-solid fa-chevron-right toggle-icon" id="toggle-icon-{{ $categoria->id }}"></i>
                            </button>
                            <h2>{{ $categoria->nom }}</h2>
                            @if ($categoria->descripcion)
                                <button type="button" class="btn-info-desc btn-show-desc" 
                                    data-descripcion="{{ $categoria->descripcion }}" 
                                    data-nombre="{{ $categoria->nom }}"
                                    title="Ver descripción">
                                    <i class="fa-solid fa-circle-question"></i>
                                </button>
                            @endif
                        </div>
                        <div style="margin-top: 0.5rem; padding-left: 2.25rem;">
                            <span class="badge-count">{{ $categoria->subcategorias->count() }} subcategorías</span>
                        </div>
                    </div>
                    <div class="categoria-actions">
                        <button type="button" class="btn-action btn-editar btn-editar-categoria" data-id="{{ $categoria->id }}" title="Editar categoría">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <form action="{{ route('admin.categorias.destroy', $categoria->id) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn-action btn-eliminar btn-eliminar-categoria"
                                data-id="{{ $categoria->id }}"
                                data-nombre="{{ $categoria->nom }}"
                                data-subcategorias="{{ $categoria->subcategorias->count() }}"
                                title="Eliminar categoría">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Subcategorías --}}
                <div class="subcategorias-container" id="subcategorias-{{ $categoria->id }}" style="display: none;">
                    @if ($categoria->subcategorias->count() > 0)
                        <table class="subcategorias-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categoria->subcategorias as $sub)
                                    <tr>
                                        <td>
                                            <div class="subcategoria-nombre-container">
                                                <span class="subcategoria-nombre">
                                                    <i class="fa-solid fa-tag"></i> {{ $sub->nom }}
                                                </span>
                                                @if ($sub->descripcion)
                                                    <button type="button" class="btn-info-desc-sm btn-show-desc" 
                                                        data-descripcion="{{ $sub->descripcion }}" 
                                                        data-nombre="{{ $sub->nom }}"
                                                        title="Ver descripción">
                                                        <i class="fa-solid fa-circle-question"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="subcategoria-actions">
                                                <button type="button" class="btn-action-sm btn-editar btn-editar-subcategoria" data-id="{{ $sub->id }}" title="Editar subcategoría">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <form action="{{ route('admin.subcategorias.destroy', $sub->id) }}" method="POST" style="display:inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn-action-sm btn-eliminar btn-eliminar-subcategoria"
                                                        data-id="{{ $sub->id }}"
                                                        data-nombre="{{ $sub->nom }}"
                                                        title="Eliminar subcategoría">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-subcategorias">
                            <i class="fa-solid fa-inbox"></i>
                            <p>No hay subcategorías en esta categoría</p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fa-solid fa-folder-open"></i>
                <h3>No hay categorías</h3>
                <p>Crea la primera categoría para empezar</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Modal Crear Categoría --}}
<div class="modal fade" id="modalCrearCategoria" tabindex="-1" aria-labelledby="modalCrearCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCrearCategoriaLabel">
                    <i class="fa-solid fa-layer-group"></i> Crear Categoría
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                @include('admin.categorias.partial.crear_categoria')
            </div>
        </div>
    </div>
</div>

{{-- Modal Crear Subcategoría --}}
<div class="modal fade" id="modalCrearSubcategoria" tabindex="-1" aria-labelledby="modalCrearSubcategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCrearSubcategoriaLabel">
                    <i class="fa-solid fa-tag"></i> Crear Subcategoría
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                @include('admin.categorias.partial.crear_subcategoria', ['categorias' => $categorias])
            </div>
        </div>
    </div>
</div>

{{-- Modal Editar Categoría --}}
<div class="modal fade" id="modalEditarCategoria" tabindex="-1" aria-labelledby="modalEditarCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarCategoriaLabel">
                    <i class="fa-solid fa-pen-to-square"></i> Editar Categoría
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="modal-editar-categoria-content">
                {{-- Contenido cargado dinámicamente --}}
            </div>
        </div>
    </div>
</div>

{{-- Modal Editar Subcategoría --}}
<div class="modal fade" id="modalEditarSubcategoria" tabindex="-1" aria-labelledby="modalEditarSubcategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarSubcategoriaLabel">
                    <i class="fa-solid fa-pen-to-square"></i> Editar Subcategoría
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="modal-editar-subcategoria-content">
                {{-- Contenido cargado dinámicamente --}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/categorias/sweetAlerts.js') }}"></script>
    <script src="{{ asset('js/admin/categorias/modal_categoria.js') }}"></script>
    <script>
        // Toggle subcategorías
        function toggleSubcategorias(id) {
            const container = document.getElementById('subcategorias-' + id);
            const icon = document.getElementById('toggle-icon-' + id);
            if (container.style.display === 'none') {
                container.style.display = 'block';
                icon.classList.add('rotated');
            } else {
                container.style.display = 'none';
                icon.classList.remove('rotated');
            }
        }
    </script>
@endpush

