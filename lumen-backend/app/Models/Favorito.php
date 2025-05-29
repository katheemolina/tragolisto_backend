<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Favorito extends Model
{
    use SoftDeletes;

    protected $table = 'favoritos';

    protected $fillable = [
        'user_id',
        'trago_id',
    ];

    protected $dates = ['deleted_at'];

    public $timestamps = true;

    // Relación opcional con modelo Trago (si lo tenés)
    public function trago()
    {
        return $this->belongsTo('App\Models\Trago', 'trago_id');
    }
}
