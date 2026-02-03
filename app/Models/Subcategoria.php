<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $fillable = ['categoria_id', 'nom']; // [cite: 66]

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
