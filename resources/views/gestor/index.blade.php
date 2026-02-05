<h1>Panel de Gestión - Sede: {{ auth()->user()->sede->nom }}</h1>

<table>
    @foreach($incidencies as $i)
    <tr>
        <td>{{ $i->titol }}</td>
        <td>
            <form action="{{ route('gestor.assignar', $i->id) }}" method="POST">
                @csrf
                <select name="tecnic_id">
                    @foreach($tecnics as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
                <button type="submit">Asignar Técnico</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>