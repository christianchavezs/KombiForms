<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    use HasFactory;

    protected $table = 'preguntas';

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $fillable = [
        'seccion_id','tipo','texto','obligatorio','orden','escala_min','escala_max'
    ];

    protected $casts = [
        'obligatorio' => 'boolean'
    ];

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    public function opciones()
    {
        return $this->hasMany(Opcion::class, 'pregunta_id')->orderBy('id','asc');
    }
}
