@extends('layouts.gestor')

@section('title', 'Técnicos de la Sede - Gestor')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/gestor_usuarios.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="gestor-header">
        <h1>Gestor de Sede: {{ auth()->user()->sede->nom ?? 'General' }}</h1>
    </div>

    <div class="table-container">
        @if($tecnicos->isEmpty())
            <div class="text-center py-4">
                <i class="fa-solid fa-users-slash fa-3x text-secondary mb-3"></i>
                <p class="text-secondary" style="font-size: 1.1rem;">No hay técnicos asignados a esta sede.</p>
            </div>
        @else
            <table class="usuarios-table">
                <thead>
                    <tr>
                        <th>Técnico</th>
                        <th>Estado</th>
                        <th>Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tecnicos as $tecnico)
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar-small">
                                    {{ substr($tecnico->name, 0, 1) }}
                                </div>
                                <div class="user-details">
                                    <span class="user-name">{{ $tecnico->name }}</span>
                                    <span class="user-email">{{ $tecnico->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($tecnico->actiu)
                                <span class="status-badge status-active">
                                    <i class="fa-solid fa-check"></i> Activo
                                </span>
                            @else
                                <span class="status-badge status-inactive">
                                    <i class="fa-solid fa-xmark"></i> Inactivo
                                </span>
                            @endif
                        </td>
                        <td style="color: var(--text-secondary); font-size: 0.9rem;">
                            {{ $tecnico->created_at->format('d/m/Y') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
