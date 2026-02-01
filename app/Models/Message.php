<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_id',
        'message_type',
        'message',
        'file_path',
        'file_name',
        'file_size',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean'
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
