<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RespuestaIndividual extends Model
{
    protected $table = 'respuestas_individuales';

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $fillable = [
        'respuesta_id',
        'pregunta_id',
        'opcion_id',
        'texto_respuesta',
        'valor_numerico',
        'valor_fecha',
        'valor_hora'
    ];

    public function respuesta()
    {
        return $this->belongsTo(Respuesta::class, 'respuesta_id');
    }

    public function pregunta()
    {
        return $this->belongsTo(Pregunta::class, 'pregunta_id');
    }

    public function opcion()
    {
        return $this->belongsTo(Opcion::class, 'opcion_id');
    }
}
