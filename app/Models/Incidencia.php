<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    // Esto permite que el controlador pueda guardar datos en estas columnas
    protected $fillable = [
        'titol', 
        'descripcio', 
        'estat', 
        'sede_id', 
        'categoria_id', 
        'tecnic_id', 
        'client_id'
    ];

    // Relación para saber de qué sede es la incidencia
    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
}
