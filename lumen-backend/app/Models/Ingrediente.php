<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingrediente extends Model
{
    protected $table = 'ingredientes';

    protected $fillable = [
        'nombre', 'es_alcohol', 'categoria'
    ];

    // Relación muchos a muchos con Trago
    public function tragos()
    {
        return $this->belongsToMany(
            Trago::class,
            'tragos_ingredientes',
            'ingrediente_id',
            'trago_id'
        )
        ->withPivot('cantidad', 'unidad', 'notas');
    }
}
