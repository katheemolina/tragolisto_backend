<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'usuarios'; // o el nombre real de tu tabla

    protected $fillable = ['google_id', 'email', 'nombre', 'fecha_nacimiento'];

    // Si no usás timestamps automáticos o querés otro formato:
    public $timestamps = true;
}
