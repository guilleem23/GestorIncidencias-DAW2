@extends('layouts.gestor')

@section('title', 'Gestor de Equipo - Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/gestor_incidencias.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <h1>Panel de Gestión - Sede: {{ auth()->user()->sede->nom ?? 'General' }}</h1>

    <div class="table-container mt-4">
        @if($incidencies->isEmpty())
            <p class="text-secondary">No hay incidencias pendientes de asignar en esta sede.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="text-align: left; color: var(--text-secondary);">Incidencia</th>
                        <th style="text-align: right; color: var(--text-secondary);">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incidencies as $i)
                    <tr>
                        <td>{{ $i->titol }}</td>
                        <td style="text-align: right;">
                            <form action="{{ route('gestor.assignar', $i->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <select name="tecnic_id" required>
                                    <option value="" disabled selected>Seleccione un técnico...</option>
                                    @foreach($tecnics as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn-asignar-tecnic">Asignar Técnico</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection