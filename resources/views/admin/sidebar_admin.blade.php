<div class="sidebar">
    <div class="sidebar-header">
        <i class="fa-solid fa-cube"></i> Nexton Admin
    </div>
    <ul class="nav-links">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-house"></i></span>
                Dashboard Global
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.incidencias') }}" class="{{ request()->routeIs('admin.incidencias') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                Incidencias
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.categorias.index') }}" class="{{ request()->routeIs('admin.categorias.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-layer-group"></i></span>
                Categorías
            </a>
        </li>
        <li class="nav-item">
            <a href="#">
                <span class="nav-icon"><i class="fa-solid fa-users"></i></span>
                Usuarios
            </a>
        </li>
        <li class="nav-item">
            <a href="#">
                <span class="nav-icon"><i class="fa-solid fa-chart-pie"></i></span>
                Estadísticas
            </a>
        </li>
        <li class="nav-item" style="margin-top: auto;">
            <a href="#">
                <span class="nav-icon"><i class="fa-solid fa-gear"></i></span>
                Configuración
            </a>
        </li>
    </ul>
</div>
