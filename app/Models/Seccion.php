<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    use HasFactory;

    protected $table = 'secciones';

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

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
