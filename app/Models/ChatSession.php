<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    protected $fillable = [
        'conversation_id',
        'started_at',
        'ended_at',
        'total_minutes',
        'price_per_minute',
        'total_amount',
        'status',
        'session_message'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'total_minutes' => 'integer',
        'price_per_minute' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'chat_sessions_id');
    }
}
