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
            <a href="{{ route('admin.sedes.index') }}" class="{{ request()->routeIs('admin.sedes.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-building"></i></span>
                Sedes
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.usuarios.index') }}" class="{{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
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

    <!-- Footer de Sidebar: Perfil de Usuario y Logout -->
    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar">
                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
            </div>
            <div class="user-info">
                <span class="user-name">{{ Auth::user()->name ?? 'Administrador' }}</span>
                <span class="user-role">Administrador</span>
            </div>
        </div>
        
        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="btn-logout" title="Cerrar sesión">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span class="logout-text">Salir</span>
            </button>
        </form>
    </div>
</div>