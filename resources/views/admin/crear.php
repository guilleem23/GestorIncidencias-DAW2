<form method="POST" action="{{ route('login') }}">
    @csrf
    <div>
        <label>Email:</label>
        <input type="email" name="email">
    </div>
    <div>
        <label>Password:</label>
        <input type="password" name="password">
    </div>
    <button type="submit">Crear Usuario</button>
    
    @if ($errors->any())
        <div style="color:red;">{{ $errors->first() }}</div>
    @endif
</form>