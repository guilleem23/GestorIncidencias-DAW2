<div class="sidebar">
    <div class="sidebar-header">
        <i class="fa-solid fa-cube"></i> Nexton
    </div>
    <ul class="nav-links">
        <li class="nav-item">
            <a href="{{ route('client.index') }}" class="{{ request()->routeIs('client.index') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-list"></i></span>
                Mis Incidencias
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('client.crear') }}" class="{{ request()->routeIs('client.crear') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-plus"></i></span>
                Nueva Incidencia
            </a>
        </li>
        <li class="nav-item" style="margin-top: auto;">
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="nav-logout-btn">
                    <span class="nav-icon"><i class="fa-solid fa-sign-out-alt"></i></span>
                    Cerrar Sesión
                </button>
            </form>
        </li>
    </ul>
</div>
