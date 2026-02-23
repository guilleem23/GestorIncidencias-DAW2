<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = ['nom'];

    // Relación: Una categoría tiene muchas subcategorías
    public function subcategorias()
    {
        return $this->hasMany(Subcategoria::class, 'categoria_id');
    }
}
