@extends('layouts.gestor')

@section('title', 'Historial de Incidencias - Gestor')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/gestor_historial.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="gestor-header">
        <h1>Incidencias de la Sede: {{ auth()->user()->sede->nom ?? 'General' }}</h1>
    </div>

    <div class="table-container">
        @if($incidencies->isEmpty())
            <div class="text-center py-5">
                <i class="fa-solid fa-folder-open fa-3x text-secondary mb-3"></i>
                <p class="text-secondary" style="font-size: 1.1rem;">No hay incidencias registradas en esta sede todavía.</p>
            </div>
        @else
            <table class="historial-table">
                <thead>
                    <tr>
                        <th>Incidencia</th>
                        <th>Técnico Asignado</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Fecha de Creación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incidencies as $incidencia)
                    <tr>
                        <td>
                            <div class="info-text">
                                <span class="info-title">{{ $incidencia->titol }}</span>
                                <span class="info-sub">{{ Str::limit($incidencia->descripcio, 50) }}</span>
                            </div>
                        </td>
                        <td>
                            @if($incidencia->tecnico)
                                <div class="info-text">
                                    <span class="info-title">{{ $incidencia->tecnico->name }}</span>
                                    <span class="info-sub">{{ $incidencia->tecnico->email }}</span>
                                </div>
                            @else
                                <span class="text-secondary"><i class="fa-solid fa-user-minus"></i> Sin asignar</span>
                            @endif
                        </td>
                        <td>
                            @if($incidencia->prioritat)
                                @if($incidencia->prioritat === 'alta')
                                    <span class="priority-badge priority-alta">
                                        <i class="fa-solid fa-arrow-up"></i> Alta
                                    </span>
                                @elseif($incidencia->prioritat === 'mitjana')
                                    <span class="priority-badge priority-mitjana">
                                        <i class="fa-solid fa-minus"></i> Media
                                    </span>
                                @else
                                    <span class="priority-badge priority-baixa">
                                        <i class="fa-solid fa-arrow-down"></i> Baja
                                    </span>
                                @endif
                            @else
                                <span class="text-secondary">-</span>
                            @endif
                        </td>
                        <td>
                            @if($incidencia->estat === 'Sense assignar')
                                <span class="status-badge badge-inactive">Sense assignar</span>
                            @elseif($incidencia->estat === 'Assignada')
                                <span class="status-badge status-assignada">Assignada</span>
                            @elseif($incidencia->estat === 'En treball')
                                <span class="status-badge status-treball">En treball</span>
                            @elseif($incidencia->estat === 'Resolta')
                                <span class="status-badge status-resolta">Resolta</span>
                            @elseif($incidencia->estat === 'Tancada')
                                <span class="status-badge badge-active">Tancada</span>
                            @else
                                <span class="status-badge badge-active">{{ $incidencia->estat }}</span>
                            @endif
                        </td>
                        <td style="color: var(--text-secondary); font-size: 0.9rem;">
                            {{ $incidencia->created_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
