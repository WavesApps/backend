<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'user_google_id',
        'superstar_id',
        'status',
        'started_at',
        'ended_at'
    ];

    public function user()
    {
        return $this->belongsTo(UserGoogle::class, 'user_google_id');
    }

    public function superstar()
    {
        return $this->belongsTo(Superstar::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
