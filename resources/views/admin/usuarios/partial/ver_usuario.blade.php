<div class="card-premium" style="margin-top: 0; box-shadow: none;">
    <div class="card-header-premium">
        <div>
            <h2 class="incidencia-title-large">{{ $usuario->name }}</h2>
            <div class="incidencia-meta-top">
                <div class="meta-item">
                    <i class="fa-solid fa-at"></i>
                    {{ '@' . $usuario->username }}
                </div>
                <div class="meta-item">
                    <i class="fa-solid fa-envelope"></i>
                    {{ $usuario->email }}
                </div>
                <div class="meta-item">
                    <i class="fa-solid fa-building"></i>
                    {{ $usuario->sede?->nom ?? '-' }}
                </div>
            </div>
        </div>
        <div style="display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap; justify-content:flex-end;">
            @if($usuario->actiu)
                <span class="status-badge status-resolta"><i class="fa-solid fa-check"></i> Activo</span>
            @else
                <span class="status-badge badge-inactive"><i class="fa-solid fa-xmark"></i> Inactivo</span>
            @endif
        </div>
    </div>

    <div class="card-body-premium">
        <div class="info-grid">
            <div class="info-block">
                <span class="info-label">Rol</span>
                <div class="description-content" style="padding: 0.85rem 1rem;">
                    {{ ucfirst($usuario->rol) }}
                </div>
            </div>

            <div class="info-block">
                <span class="info-label">Fecha de registro</span>
                <div class="description-content" style="padding: 0.85rem 1rem;">
                    {{ $usuario->created_at?->format('d/m/Y H:i') ?? '-' }}
                </div>
            </div>

            <div class="info-block">
                <span class="info-label">Incidencias reportadas</span>
                <div class="description-content" style="padding: 0.85rem 1rem;">
                    {{ $totalIncidencias }}
                </div>
            </div>
        </div>
    </div>
</div>
