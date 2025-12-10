<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    use HasFactory;

    protected $table = 'secciones';

    protected $fillable = [
        'formulario_id',
        'titulo',
        'descripcion',
        'orden'
    ];

    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'formulario_id');
    }

    public function preguntas()
    {
        return $this->hasMany(Pregunta::class, 'seccion_id')->orderBy('orden');
    }
}
