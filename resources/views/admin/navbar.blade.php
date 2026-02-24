<nav style="background-color: #333; padding: 1rem; display: flex; gap: 1rem;">
    <a href="{{ url('/admin/usuarios') }}" style="color: #fff; text-decoration: none;">Usuarios</a>
    <a href="{{ route('admin.categorias.index') }}" style="color: #fff; text-decoration: none;">Categorías</a>
    <a href="{{ url('/admin') }}" style="color: #fff; text-decoration: none;">Panel Admin</a>
    <a href="{{ url('/dashboard') }}" style="color: #fff; text-decoration: none;">Dashboard</a>
    <form action="{{ route('logout') }}" method="POST" style="display:inline; margin-left:auto;">
        @csrf
        <button type="submit" style="background: #c00; color: #fff; border: none; padding: 0.5rem 1rem; cursor: pointer;">Cerrar sesión</button>
    </form>
</nav>
