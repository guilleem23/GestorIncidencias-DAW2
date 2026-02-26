<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    protected $table = 'comentarios';

    protected $fillable = [
        'incidencia_id',
        'usuario_id',
        'missatge',
    ];

    public function incidencia()
    {
        return $this->belongsTo(Incidencia::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
