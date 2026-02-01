<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Superstar extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'display_name',
        'bio',
        'price_per_hour',
        'is_available',
        'rating',
        'total_followers',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_available' => 'boolean',
        'price_per_hour' => 'decimal:2',
        'rating' => 'decimal:2',
        'total_followers' => 'integer',
    ];

    /**
     * Get the user that owns the superstar profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user googles that are subscribed to this superstar.
     */
    public function subscribers()
    {
        return $this->belongsToMany(UserGoogle::class, 'subscribes', 'superstar_id', 'user_google_id')
                    ->withTimestamps();
    }

    /**
     * Get the payments for this superstar.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'superstar_id');
    }
}
