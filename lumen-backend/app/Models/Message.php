<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $table = 'messages';

    protected $fillable = [
        'chat_id',
        'sender',
        'content',
        'is_deleted',
    ];

    public $timestamps = ['created_at'];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }
}
