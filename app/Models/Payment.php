<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_google_id',
        'superstar_id',
        'total_amount',
        'chat_sessions_id',
        'payment_method',
        'payment_status',
        'transaction_reference',
        'paid_at'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(UserGoogle::class, 'user_google_id');
    }

    public function superstar()
    {
        return $this->belongsTo(Superstar::class, 'superstar_id');
    }

    public function chatSession()
    {
        return $this->belongsTo(ChatSession::class, 'chat_sessions_id');
    }

    public function paymentBreakdown()
    {
        return $this->hasOne(PaymentBreakdown::class);
    }
}
