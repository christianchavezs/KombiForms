<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    protected $table = 'respuestas';

    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'formulario_id');
    }
}
