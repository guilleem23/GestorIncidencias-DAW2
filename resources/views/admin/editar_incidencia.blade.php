@extends('layouts.admin')

@section('title', 'Editar Incidencia - Admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/gestor_historial.css') }}">
    <link rel="stylesheet" href="{{ asset('css/gestor_incidencia_detail.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="detail-container">
        <div class="detail-header">
            <h1>Editar Incidencia #{{ $incidencia->id }}</h1>
            <a href="{{ route('admin.incidencias') }}" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Volver al Listado
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #f87171; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card-premium">
            <div class="card-body-premium">
                <form id="form-editar-incidencia" action="{{ route('admin.incidencias.update', $incidencia->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="info-grid">
                        <div class="form-group">
                            <label class="form-label" for="titol">Título de la Incidencia</label>
                            <input type="text" id="titol" name="titol" class="form-control" value="{{ old('titol', $incidencia->titol) }}">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="tecnic_id">Técnico Asignado</label>
                            <select id="tecnic_id" name="tecnic_id" class="form-select">
                                <option value="">-- Sin Asignar --</option>
                                @foreach($tecnicos as $tecnico)
                                    <option value="{{ $tecnico->id }}" {{ (old('tecnic_id', $incidencia->tecnic_id) == $tecnico->id) ? 'selected' : '' }}>
                                        {{ $tecnico->name }} ({{ $tecnico->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="info-grid">
                        <div class="form-group">
                            <label class="form-label" for="categoria_id">Categoría</label>
                            <select id="categoria_id" name="categoria_id" class="form-select">
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}" {{ (old('categoria_id', $incidencia->categoria_id) == $cat->id) ? 'selected' : '' }}>
                                        {{ $cat->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="subcategoria_id">Subcategoría</label>
                            <select id="subcategoria_id" name="subcategoria_id" class="form-select">
                                @foreach($categorias->firstWhere('id', old('categoria_id', $incidencia->categoria_id))->subcategorias as $sub)
                                    <option value="{{ $sub->id }}" {{ (old('subcategoria_id', $incidencia->subcategoria_id) == $sub->id) ? 'selected' : '' }}>
                                        {{ $sub->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="info-grid">
                        <div class="form-group">
                            <label class="form-label" for="estat">Estado</label>
                            <select id="estat" name="estat" class="form-select">
                                <option value="Sense assignar" {{ (old('estat', $incidencia->estat) === 'Sense assignar') ? 'selected' : '' }}>Sin asignar</option>
                                <option value="Assignada" {{ (old('estat', $incidencia->estat) === 'Assignada') ? 'selected' : '' }}>Asignada</option>
                                <option value="En treball" {{ (old('estat', $incidencia->estat) === 'En treball') ? 'selected' : '' }}>En trabajo</option>
                                <option value="Resolta" {{ (old('estat', $incidencia->estat) === 'Resolta') ? 'selected' : '' }}>Resuelta</option>
                                <option value="Tancada" {{ (old('estat', $incidencia->estat) === 'Tancada') ? 'selected' : '' }}>Cerrada</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="prioritat">Prioridad</label>
                            <select id="prioritat" name="prioritat" class="form-select">
                                <option value="alta" {{ (old('prioritat', $incidencia->prioritat) === 'alta') ? 'selected' : '' }}>Alta</option>
                                <option value="mitjana" {{ (old('prioritat', $incidencia->prioritat) === 'mitjana') ? 'selected' : '' }}>Media</option>
                                <option value="baixa" {{ (old('prioritat', $incidencia->prioritat) === 'baixa') ? 'selected' : '' }}>Baja</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="descripcio">Descripción de la Incidencia</label>
                        <textarea id="descripcio" name="descripcio" class="form-control">{{ old('descripcio', $incidencia->descripcio) }}</textarea>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.incidencias.show', $incidencia->id) }}" class="btn-back">Cancelar</a>
                        <button type="button" class="btn-primary" id="btn-submit-edit">
                            <i class="fa-solid fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Category/Subcategory Logic Data -->
<script>
    const categoriasData = @json($categorias);
</script>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/admin/editar_incidencia.js') }}"></script>
@endpush
