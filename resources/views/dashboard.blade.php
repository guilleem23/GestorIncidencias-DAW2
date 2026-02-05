<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Proyecto Incidencias</title>
</head>
<body>
    <h1>Bienvenido, {{ auth()->user()->name }}</h1>
    <p>Tu rol es: {{ auth()->user()->rol }}</p>
    
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
</body>
</html>