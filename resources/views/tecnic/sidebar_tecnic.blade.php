<div class="sidebar">
    <div class="sidebar-header">
        <i class="fa-solid fa-cube"></i> Nexton
    </div>
    <ul class="nav-links">
        <li class="nav-item">
            <a href="{{ route('tecnic.index') }}" class="{{ request()->routeIs('tecnic.index') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-tasks"></i></span>
                Mis Tareas
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('tecnic.totes') }}" class="{{ request()->routeIs('tecnic.totes') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-clipboard-list"></i></span>
                Todas mis tareas
            </a>
        </li>
    </ul>

    <!-- Footer de Sidebar: Perfil de Usuario y Logout -->
    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar">
                {{ substr(Auth::user()->name ?? 'T', 0, 1) }}
            </div>
            <div class="user-info">
                <span class="user-name">{{ Auth::user()->name ?? 'Técnico' }}</span>
                <span class="user-role">Técnico</span>
                <span class="user-email" style="font-size: 0.75rem; opacity: 0.7;">{{ Auth::user()->email ?? '' }}</span>
            </div>
        </div>
        
        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="btn-logout" id="btn-logout" title="Cerrar sesión">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span class="logout-text">Salir</span>
            </button>
        </form>
    </div>
</div>
