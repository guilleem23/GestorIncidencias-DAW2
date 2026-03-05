<div class="sidebar">
    <div class="sidebar-header">
        <i class="fa-solid fa-cube"></i> Nexton Cliente
    </div>
    <ul class="nav-links">
        <li class="nav-item">
            <a href="{{ route('client.index') }}" class="{{ request()->routeIs('client.index') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-folder-open"></i></span>
                Mis Incidencias
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('client.crear') }}" class="{{ request()->routeIs('client.crear') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-plus"></i></span>
                Nueva Incidencia
            </a>
        </li>
        <!-- Add more client-specific links here in the future -->
    </ul>

    <!-- Footer de Sidebar: Perfil de Usuario y Logout -->
    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar">
                {{ substr(Auth::user()->name ?? 'C', 0, 1) }}
            </div>
            <div class="user-info">
                <span class="user-name">{{ Auth::user()->name ?? 'Cliente' }}</span>
                <span class="user-role">Cliente</span>
                <span class="user-email" style="font-size: 0.75rem; opacity: 0.7;">{{ Auth::user()->email ?? '' }}</span>
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
