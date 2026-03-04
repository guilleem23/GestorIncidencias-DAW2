@extends('layouts.admin')

@section('title', 'Gestionar Sedes')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_categorias.css') }}">
@endpush

@section('content')
<div class="categorias-container">
    <div class="categorias-header">
        <h1><i class="fa-solid fa-building"></i> Gestión de Sedes</h1>
        <div class="header-actions">
            <button type="button" class="btn-crear btn-crear-sede" data-bs-toggle="modal" data-bs-target="#modalCrearSede">
                <i class="fa-solid fa-plus"></i> Nueva Sede
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

    {{-- Listado de sedes --}}
    {{-- Listado de sedes --}}
    <div class="sedes-grid">
        @forelse ($sedes as $sede)
            <div class="sede-card">
                <div class="sede-image-wrapper-banner">
                    @if($sede->imagen)
                        <img src="{{ asset($sede->imagen) }}" alt="Imagen Sede" class="sede-img-banner">
                    @else
                        <img src="{{ asset('img/sede_default.jpg') }}" alt="Sede Default" class="sede-img-banner">
                    @endif
                    
                    <form action="{{ route('admin.sedes.destroy', $sede->id) }}" method="POST" class="form-eliminar-sede">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn-eliminar-float btn-eliminar-sede"
                            data-id="{{ $sede->id }}"
                            data-nombre="{{ $sede->nom }}"
                            title="Eliminar sede">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
                
                <div class="sede-card-body">
                    <h3 class="sede-title">{{ $sede->nom }}</h3>
                    
                    <div class="sede-details">
                        <div class="sede-category">
                            {{ Str::limit($sede->descripcion ?? 'Sin descripción disponible', 20) }}
                            <button type="button" class="btn-info-desc"
                                data-nombre="{{ $sede->nom }}"
                                data-descripcion="{{ $sede->descripcion ?? 'Sin descripción disponible' }}"
                                data-gestor="{{ $sede->gestor ? $sede->gestor->name . ' (@' . $sede->gestor->username . ')' : 'Sin gestor asignado' }}"
                                title="Ver descripción completa">
                                <i class="fa-solid fa-circle-info"></i>
                            </button>
                        </div>
                        <div class="sede-responsable-info">
                            <i class="fa-solid fa-user-tie"></i> 
                            {{ $sede->gestor ? '@' . $sede->gestor->username : 'Sin gestor' }}
                        </div>
                    </div>
                    
                    <div class="sede-footer">
                        <span class="sede-incidencias-badge {{ $sede->incidencies_obertes_count > 0 ? 'badge-warning' : 'badge-ok' }}">
                            <i class="fa-solid fa-{{ $sede->incidencies_obertes_count > 0 ? 'triangle-exclamation' : 'circle-check' }}"></i>
                            {{ $sede->incidencies_obertes_count }} abierta{{ $sede->incidencies_obertes_count !== 1 ? 's' : '' }}
                        </span>
                        <button type="button" class="btn-ver-detalles-sede btn-editar-sede" data-id="{{ $sede->id }}">
                            Editar Sede
                        </button>
                    </div>
                </div>
            </div>
    @empty
        <div class="empty-state">
            <i class="fa-solid fa-building-flag"></i>
            <h3>No hay sedes</h3>
            <p>Crea la primera sede para empezar</p>
        </div>
    @endforelse
</div>

{{-- Modal Crear Sede --}}
<div class="modal fade" id="modalCrearSede" tabindex="-1" aria-labelledby="modalCrearSedeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCrearSedeLabel">
                    <i class="fa-solid fa-building"></i> Crear Sede
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                @include('admin.sedes.partial.crear_sede')
            </div>
        </div>
    </div>
</div>

{{-- Modal Editar Sede --}}
<div class="modal fade" id="modalEditarSede" tabindex="-1" aria-labelledby="modalEditarSedeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarSedeLabel">
                    <i class="fa-solid fa-pen-to-square"></i> Editar Sede
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="modal-editar-sede-content">
                {{-- Contenido cargado dinámicamente --}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/sedes/sweetAlerts.js') }}"></script>
    <script src="{{ asset('js/admin/sedes/modal_sede.js') }}"></script>
    <script src="{{ asset('js/admin/sedes/validar_crear_sede.js') }}"></script>
    <script src="{{ asset('js/admin/sedes/validar_editar_sede.js') }}"></script>
@endpush
