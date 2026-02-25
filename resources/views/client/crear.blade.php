@extends('layouts.client')

@section('title', 'Crear Incidencia - Nexton')

@section('content')
    <a href="{{ route('client.index') }}" class="back-link" style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--color-primario); text-decoration: none; margin-bottom: 1.5rem; font-size: 0.9rem;">
        <i class="fas fa-arrow-left"></i> Volver a mis incidencias
    </a>

    <h1 class="page-title">Crear Nueva Incidencia</h1>
    <p class="page-subtitle">Introduce los datos de la nueva incidencia</p>

    <!-- Mensajes de error de validación -->
    @if ($errors->any())
          <div class="error-message" style="flex-direction: column; align-items: flex-start;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-exclamation-circle"></i>
                <strong>Hay errores en el formulario:</strong>
            </div>
            <ul style="margin: 0.5rem 0 0 1.5rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulario -->
    <div class="form-container">
        <form method="POST" action="{{ route('client.store') }}">
            @csrf

            <!-- Título -->
            <div class="form-group">
                <label for="titol">
                    <i class="fas fa-heading"></i> Título de la incidencia *
                </label>
                <input 
                    type="text" 
                    id="titol" 
                    name="titol" 
                    class="form-input @error('titol') error @enderror"
                    value="{{ old('titol') }}"
                    placeholder="Escribe un título descriptivo..."
                >
                @error('titol')
                    <span class="error-message">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </span>
                @enderror
            </div>

            <!-- Descripción -->
            <div class="form-group">
                <label for="descripcio">
                    <i class="fas fa-file-alt"></i> Descripción *
                </label>
                <textarea 
                    id="descripcio" 
                    name="descripcio" 
                    class="form-textarea @error('descripcio') error @enderror"
                    rows="5"
                    placeholder="Describe el problema con detalle..."
                >{{ old('descripcio') }}</textarea>
                @error('descripcio')
                    <span class="error-message">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </span>
                @enderror
            </div>

            <!-- Sede -->
            <div class="form-group">
                <label for="sede_id">
                    <i class="fas fa-building"></i> Sede *
                </label>
                <select 
                    id="sede_id" 
                    name="sede_id" 
                    class="form-select @error('sede_id') error @enderror"
                >
                    <option value="">Selecciona una sede...</option>
                    @foreach($sedes as $sede)
                        <option value="{{ $sede->id }}" {{ old('sede_id') == $sede->id ? 'selected' : '' }}>
                            {{ $sede->nom }}
                        </option>
                    @endforeach
                </select>
                @error('sede_id')
                    <span class="error-message">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </span>
                @enderror
            </div>

            <!-- Categoría -->
            <div class="form-group">
                <label for="categoria_id">
                    <i class="fas fa-tag"></i> Categoría *
                </label>
                <select 
                    id="categoria_id" 
                    name="categoria_id" 
                    class="form-select @error('categoria_id') error @enderror"
                >
                    <option value="">Selecciona una categoría...</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}" data-subcategorias="{{ json_encode($categoria->subcategorias) }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nom }}
                        </option>
                    @endforeach
                </select>
                @error('categoria_id')
                    <span class="error-message">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </span>
                @enderror
            </div>

            <!-- Subcategoría -->
            <div class="form-group">
                <label for="subcategoria_id">
                    <i class="fas fa-tags"></i> Subcategoría *
                </label>
                <select 
                    id="subcategoria_id" 
                    name="subcategoria_id" 
                    class="form-select @error('subcategoria_id') error @enderror"
                    data-old-value="{{ old('subcategoria_id') }}"
                    disabled
                >
                    <option value="">Primero selecciona una categoría...</option>
                </select>
                @error('subcategoria_id')
                    <span class="error-message">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </span>
                @enderror
            </div>

            <!-- Botones -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Crear Incidencia
                </button>
                <a href="{{ route('client.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/client-form.js') }}?v={{ time() }}"></script>
@endpush