<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Formulario extends Model
{
    use HasFactory;

    protected $table = 'formularios';

    protected $fillable = [
        'titulo',
        'descripcion',
        'creador_id',
        'permitir_anonimo',
        'requiere_correo',
        'una_respuesta',
        'fecha_inicio',
        'fecha_fin',
        'token', // importante incluirlo
    ];

    protected $dates = [
        'fecha_inicio',
        'fecha_fin',
        'creado_en',
        'actualizado_en'
    ];

    // >>> ¡IMPORTANTE! <<<
    // Usar los nombres REALES de tu base de datos
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    // Generar token automáticamente al crear un formulario
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($formulario) {
            if (empty($formulario->token)) {
                $formulario->token = Str::random(16);
            }
        });
    }

    public function respuestas()
    {
        return $this->hasMany(Respuesta::class, 'formulario_id');
    }

    public function secciones()
    {
        return $this->hasMany(Seccion::class, 'formulario_id')->orderBy('orden');
    }
}