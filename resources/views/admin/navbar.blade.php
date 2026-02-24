<nav style="background-color: #333; padding: 1rem; display: flex; gap: 1rem;">
    <a href="{{ route('admin.usuarios.index') }}" style="color: #fff; text-decoration: none;">Usuarios</a>
    <a href="{{ url('/admin') }}" style="color: #fff; text-decoration: none;">Panel Admin</a>
    <a href="{{ url('/dashboard') }}" style="color: #fff; text-decoration: none;">Dashboard</a>
    <a href="{{ route('logout') }}" id="enlace-cerrar-sesion"
        style="background: #c00; color: #fff; border: none; padding: 0.5rem 1rem; cursor: pointer; margin-left:auto; text-decoration:none; display:inline-block;">Cerrar
        sesión</a>
</nav>
