<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    protected $fillable = ['nom', 'responsable', 'imagen', 'descripcion'];

    // Relación: Una sede tiene muchos usuarios
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Relación: Una sede tiene muchas incidencias
    public function incidencies()
    {
        return $this->hasMany(Incidencia::class);
    }

    // Relación: Gestor asignado a la sede
    public function gestor()
    {
        return $this->hasOne(User::class)->where('rol', 'gestor');
    }
}
