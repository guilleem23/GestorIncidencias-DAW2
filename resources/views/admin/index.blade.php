<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador - Nexton</title>
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
                <button type="submit" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</button>
            </form>
        </div>
    </header>

    <!-- Contenido Principal -->
    <div class="container">
        <h1 class="page-title">Panel de Administrador</h1>
        <p class="page-subtitle">Gestiona usuarios, incidencias y configuración del sistema</p>

        <!-- Grid de Tarjetas -->
        <div class="cards-grid">
            <!-- Gestión de Usuarios -->
            <a href="{{ route('admin.usuarios.listado') }}" class="card">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="card-title">Gestión de Usuarios</h3>
                <p class="card-description">Ver, filtrar y gestionar todos los usuarios del sistema. Dar de alta o baja empleados.</p>
            </a>

            <!-- Crear Nuevo Usuario -->
            <a href="{{ route('admin.usuarios.create') }}" class="card">
                <div class="card-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3 class="card-title">Alta de Usuario</h3>
                <p class="card-description">Registrar nuevos empleados en el sistema con el rol y sede correspondiente.</p>
            </a>

            <!-- Gestión de Categorías -->
            <a href="{{ route('admin.categorias.index') }}" class="card">
                <div class="card-icon">
                    <i class="fas fa-tags"></i>
                </div>
                <h3 class="card-title">Tipos de Incidencias</h3>
                <p class="card-description">
                    Crear y gestionar categorías y subcategorías de incidencias.
                </p>
            </a>

            <!-- Vista General (Estadísticas - Opcional) -->
            <a href="#" class="card" style="opacity: 0.6; cursor: not-allowed;">
                <div class="card-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3 class="card-title">Estadísticas</h3>
                <p class="card-description">
                    Vista general de las sedes, usuarios activos e incidencias del sistema.
                </p>
            </a>
        </div>
    </div>
</body>
</html>
