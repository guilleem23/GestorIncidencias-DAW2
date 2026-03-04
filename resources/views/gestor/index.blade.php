@extends('layouts.gestor')

@section('title', 'Asignar Incidencias - Gestor')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/gestor_historial.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="gestor-header">
        <h1>Asignar Incidencias - Sede: {{ auth()->user()->sede->nom ?? 'General' }}</h1>
    </div>

    <div class="table-container">
        @if($incidencies->isEmpty())
            <div class="empty-state-box">
                <i class="fa-solid fa-circle-check fa-3x"></i>
                <p>No hay incidencias pendientes de asignar en esta sede. ¡Todo bajo control!</p>
            </div>
        @else
            <table class="historial-table">
                <thead>
                    <tr>
                        <th>Incidencia</th>
                        <th>Cliente</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Asignar Técnico</th>
                    </tr>
                </thead>
                <tbody id="assign-table-body">
                    @foreach($incidencies as $incidencia)
                    <tr>
                        <td>
                            <span class="info-title">{{ $incidencia->titol }}</span>
                        </td>
                        <td>
                            @if($incidencia->cliente)
                                <span class="info-title">{{ $incidencia->cliente->name }}</span>
                            @else
                                <span class="text-secondary">-</span>
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
                            @else
                                <span class="status-badge badge-active">{{ $incidencia->estat }}</span>
                            @endif
                        </td>
                        <td class="date-cell">
                            {{ $incidencia->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <form id="form-assign-{{ $incidencia->id }}" action="{{ route('gestor.assignar', $incidencia->id) }}" method="POST" class="assign-form">
                                @csrf
                                <select id="select-tecnic-{{ $incidencia->id }}" name="tecnic_id" class="filter-select assign-select">
                                    <option value="" disabled selected>Seleccionar técnico...</option>
                                    @foreach($tecnics as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" id="btn-assign-{{ $incidencia->id }}" class="btn-assign btn-confirm-assign btn-assign-tecnic" data-id="{{ $incidencia->id }}">
                                    <i class="fa-solid fa-user-check"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($incidencies->hasPages())
                <div class="pagination-wrapper">
                    {{ $incidencies->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/gestor/asignar.js') }}"></script>
@endpush