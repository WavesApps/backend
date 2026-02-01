<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscribe extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'subscribes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_google_id',
        'superstar_id',
    ];

    /**
     * Get the user google that owns the subscription.
     */
    public function userGoogle()
    {
        return $this->belongsTo(UserGoogle::class, 'user_google_id');
    }

    /**
     * Get the superstar that is subscribed to.
     */
    public function superstar()
    {
        return $this->belongsTo(Superstar::class, 'superstar_id');
    }
}
