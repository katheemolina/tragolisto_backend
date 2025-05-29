<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Juego extends Model
{
    protected $table = 'juegos';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria',
        'materiales',
        'min_jugadores',
        'max_jugadores',
        'es_para_beber',
    ];
}
