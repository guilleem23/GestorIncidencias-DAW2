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
        'prioritat',
        'sede_id', 
        'categoria_id',
        'subcategoria_id', 
        'tecnic_id', 
        'client_id'
    ];

    // Relación para saber de qué sede es la incidencia
    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    // Relación para saber a qué usuario pertenece la incidencia
    public function cliente() {
        return $this->belongsTo(User::class, 'client_id');
    }

    // Relación para saber qué técnico tiene asignada la incidencia
    public function tecnico() {
        return $this->belongsTo(User::class, 'tecnic_id');
    }

    // Relación con categoría
    public function categoria() {
        return $this->belongsTo(Categoria::class);
    }

    // Relación con subcategoría
    public function subcategoria() {
        return $this->belongsTo(Subcategoria::class);
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'incidencia_id')->orderBy('created_at', 'asc');
    }
}
