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
}
