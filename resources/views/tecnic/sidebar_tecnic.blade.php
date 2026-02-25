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
