<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trago extends Model
{
    protected $table = 'tragos';

    protected $fillable = [
        'nombre', 'descripcion', 'instrucciones', 'tips', 'historia',
        'es_alcoholico', 'imagen_url', 'dificultad', 'tiempo_preparacion_minutos'
    ];

    // Relación muchos a muchos con Ingrediente
    public function ingredientes()
    {
        return $this->belongsToMany(
            Ingrediente::class,
            'tragos_ingredientes',
            'trago_id',
            'ingrediente_id'
        )
        ->withPivot('cantidad', 'unidad', 'notas');
    }
}
