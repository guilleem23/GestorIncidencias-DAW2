@extends('layouts.admin')

@section('title', 'Nexton Admin - Gestión de Incidencias')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_incidencias.css') }}">
@endpush

@section('content')
<div class="header-actions">
    <div>
        <h1><i class="fa-solid fa-triangle-exclamation"></i> Gestión de Incidencias</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Administra las incidencias reportadas por los clientes.</p>
    </div>
    
    <div class="search-bar">
        <input type="text" class="search-input" placeholder="Buscar por ID, Cliente o Sede...">
        <button class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
    </div>
</div>

<div class="incidents-table-container">
    <table class="incidents-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Cliente</th>
                <th>Sede</th>
                <th>Fecha Creación</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>#INC-2024-004</td>
                <td>Problema con impresora en red</td>
                <td>Gilles Villeneuve</td>
                <td>Montreal</td>
                <td>05/02/2026 09:30</td>
                <td><span class="badge badge-assigned">Asignada</span></td>
                <td>
                    <a href="#" class="view-btn" title="Ver Detalle"><i class="fa-solid fa-eye"></i></a>
                    <button class="delete-btn" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                </td>
            </tr>
            <tr>
                <td>#INC-2024-005</td>
                <td>Wifi inestable en sala de juntas</td>
                <td>Ayrton Senna</td>
                <td>Sao Paulo</td>
                <td>05/02/2026 10:15</td>
                <td><span class="badge badge-pending">Sin Asignar</span></td>
                <td>
                    <a href="#" class="view-btn" title="Ver Detalle"><i class="fa-solid fa-eye"></i></a>
                    <button class="delete-btn" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                </td>
            </tr>
             <tr>
                <td>#INC-2024-006</td>
                <td>Actualización de software fallida</td>
                <td>Michael Schumacher</td>
                <td>Berlín</td>
                <td>04/02/2026 16:45</td>
                <td><span class="badge badge-assigned">Asignada</span></td>
                <td>
                    <a href="#" class="view-btn" title="Ver Detalle"><i class="fa-solid fa-eye"></i></a>
                    <button class="delete-btn" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                </td>
            </tr>
             <tr>
                <td>#INC-2024-007</td>
                <td>Pantalla parpadeante</td>
                <td>Fernando Alonso</td>
                <td>Oviedo</td>
                <td>05/02/2026 08:00</td>
                <td><span class="badge badge-pending">Sin Asignar</span></td>
                <td>
                    <a href="#" class="view-btn" title="Ver Detalle"><i class="fa-solid fa-eye"></i></a>
                    <button class="delete-btn" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection

