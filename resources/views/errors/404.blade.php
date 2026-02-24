<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada - Gestor de Incidencias</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <!-- Custom 404 CSS -->
    <link rel="stylesheet" href="{{ asset('css/404.css') }}">
</head>
<body>

    <!-- Ambient floating blobs -->
    <div class="bg-element bg-blue"></div>
    <div class="bg-element bg-purple"></div>

    <div class="error-container">
        <div class="glitch-wrapper">
            <h1 class="error-code">404</h1>
        </div>
        
        <h2 class="error-title">Oops! Te perdiste en el sistema</h2>
        
        <p class="error-message">
            La página a la que intentas acceder no existe, ha sido movida o la URL es incorrecta. 
            No te preocupes, puedes volver por donde viniste sin perder ningún dato.
        </p>
        
        <button onclick="window.history.back()" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Volver Atrás
        </button>
    </div>

</body>
</html>
