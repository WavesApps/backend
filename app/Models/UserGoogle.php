<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class UserGoogle extends Authenticatable
{
    use HasFactory, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'email',
        'username',
        'image',
    ];

    /**
     * The table associated with the model.
     */
    protected $table = 'user_googles';

    /**
     * Get the superstars that the user is subscribed to.
     */
    public function superstars()
    {
        return $this->belongsToMany(Superstar::class, 'subscribes', 'user_google_id', 'superstar_id')
                    ->withTimestamps();
    }
}
