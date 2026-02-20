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
        <p class="page-subtitle">Formulari en construcció...</p>

        <div class="empty-state">
            <i class="fas fa-tools"></i>
            <p>Aquesta funcionalitat està en desenvolupament</p>
        </div>
    </div>
</body>
</html>
