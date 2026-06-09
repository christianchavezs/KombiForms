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
        'token',
        'activo', // 👈 faltaba aquí
    ];

    protected $dates = [
        'fecha_inicio',
        'fecha_fin',
        'creado_en',
        'actualizado_en'
    ];

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

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

    public function getEstadoAttribute()
    {
        $ahora = now();

        if ($this->fecha_fin && $this->fecha_fin <= $ahora) {
            return 'Inactivo';
        }

        if ($this->fecha_inicio && $this->fecha_inicio > $ahora) {
            return 'Programado';
        }

        if ($this->fecha_inicio && $this->fecha_inicio <= $ahora &&
            (!$this->fecha_fin || $this->fecha_fin > $ahora)) {
            return 'Activo';
        }

        return $this->activo ? 'Activo' : 'Inactivo';
    }

}