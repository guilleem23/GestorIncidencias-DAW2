<div class="sidebar">
    <div class="sidebar-header">
        <i class="fa-solid fa-cube"></i> Nexton Gestor
    </div>
    <ul class="nav-links">
        <li class="nav-item">
            <a href="{{ route('gestor.incidencias') }}" class="{{ request()->routeIs('gestor.incidencias') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-folder-open"></i></span>
                Incidencias de la Sede
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('gestor.index') }}" class="{{ request()->routeIs('gestor.index') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-list-check"></i></span>
                Asignar Incidencias
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('gestor.usuarios') }}" class="{{ request()->routeIs('gestor.usuarios') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-users"></i></span>
                Usuarios (Técnicos)
            </a>
        </li>
        <!-- Add more gestor-specific links here in the future -->
    </ul>

    <!-- Footer de Sidebar: Perfil de Usuario y Logout -->
    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar">
                {{ substr(Auth::user()->name ?? 'G', 0, 1) }}
            </div>
            <div class="user-info">
                <span class="user-name">{{ Auth::user()->name ?? 'Gestor Equipo' }}</span>
                <span class="user-role">Gestor de Equipo</span>
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
