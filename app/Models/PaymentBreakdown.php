<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentBreakdown extends Model
{
    protected $fillable = [
        'payment_id',
        'superstar_amount',
        'system_amount',
        'superstar_percentage',
        'system_percentage'
    ];

    protected $casts = [
        'superstar_amount' => 'decimal:2',
        'system_amount' => 'decimal:2',
        'superstar_percentage' => 'decimal:2',
        'system_percentage' => 'decimal:2'
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
