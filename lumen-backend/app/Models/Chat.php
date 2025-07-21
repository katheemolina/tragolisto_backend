<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    protected $table = 'chats';

    protected $fillable = [
        'user_id',
        'title',
    ];

    public $timestamps = true; // Usa created_at y updated_at automáticamente

    // Relación: un chat tiene muchos mensajes
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    // Opcional: un chat pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
