<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'rol',
        'sede_id',
        'actiu',
    ];

    // Relación: Un usuario pertenece a una sede
    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    // Relación: Incidencias donde el usuario es el cliente
    public function incidenciasComoCliente()
    {
        return $this->hasMany(Incidencia::class, 'client_id');
    }

    // Relación: Incidencias donde el usuario es el técnico
    public function incidenciasComoTecnico()
    {
        return $this->hasMany(Incidencia::class, 'tecnic_id');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'usuario_id');
    }
}
