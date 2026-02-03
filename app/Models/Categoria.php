<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = ['nom']; // [cite: 66]

    // Relación: Una categoría tiene muchas subcategorías
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'categoria_id');
    }
}
