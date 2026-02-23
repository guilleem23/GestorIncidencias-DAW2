<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear incidència - Nexton</title>
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
                <i class="fas fa-user"></i> {{ auth()->user()->name }}
            </span>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Tancar Sessió
                </button>
            </form>
        </div>
    </header>

    <!-- Contenido Principal -->
    <div class="container">
        <a href="{{ route('client.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Tornar a les meves incidències
        </a>

        <h1 class="page-title">Crear nova incidència</h1>
        <p class="page-subtitle">Introdueix les dades de la nova incidència...</p>

        <!-- Mensajes de error de validación -->
        @if ($errors->any())
            <div class="error-message" style="margin-bottom: 1.5rem; background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; padding: 1rem; border-radius: 0.5rem;">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Hi ha errors en el formulari:</strong>
                    <ul style="margin: 0.5rem 0 0 1.5rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Formulario -->
        <div class="form-container">
            <form method="POST" action="{{ route('client.store') }}">
                @csrf

                <!-- Título -->
                <div class="form-group">
                    <label for="titol">
                        <i class="fas fa-heading"></i> Títol de la incidència *
                    </label>
                    <input 
                        type="text" 
                        id="titol" 
                        name="titol" 
                        class="form-input @error('titol') error @enderror"
                        value="{{ old('titol') }}"
                        placeholder="Escriu un títol descriptiu..."
                        required
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
                        <i class="fas fa-file-alt"></i> Descripció *
                    </label>
                    <textarea 
                        id="descripcio" 
                        name="descripcio" 
                        class="form-input @error('descripcio') error @enderror"
                        rows="5"
                        placeholder="Descriu el problema amb detall..."
                        required
                        style="resize: vertical; min-height: 120px;"
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
                        <i class="fas fa-building"></i> Seu *
                    </label>
                    <select 
                        id="sede_id" 
                        name="sede_id" 
                        class="form-select @error('sede_id') error @enderror"
                        required
                    >
                        <option value="">Selecciona una seu...</option>
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
                        <i class="fas fa-tag"></i> Categoria *
                    </label>
                    <select 
                        id="categoria_id" 
                        name="categoria_id" 
                        class="form-select @error('categoria_id') error @enderror"
                        required
                    >
                        <option value="">Selecciona una categoria...</option>
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
                        <i class="fas fa-tags"></i> Subcategoria *
                    </label>
                    <select 
                        id="subcategoria_id" 
                        name="subcategoria_id" 
                        class="form-select @error('subcategoria_id') error @enderror"
                        data-old-value="{{ old('subcategoria_id') }}"
                        required
                        disabled
                    >
                        <option value="">Primer selecciona una categoria...</option>
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
                        <i class="fas fa-check"></i> Crear Incidència
                    </button>
                    <a href="{{ route('client.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel·lar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/client-form.js') }}"></script>
</body>
</html>
