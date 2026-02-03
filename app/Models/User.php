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
        'email',
        'password',
        'rol',     // [cite: 18]
        'sede_id', // 
        'actiu',   // [cite: 29]
    ];

    // Relación: Un usuario pertenece a una sede
    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
}
