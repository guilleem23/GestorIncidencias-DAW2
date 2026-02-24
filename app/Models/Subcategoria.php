<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategoria extends Model
{
    protected $fillable = ['categoria_id', 'nom', 'descripcion'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
