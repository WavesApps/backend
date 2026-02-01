<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperstarStory extends Model
{
    use HasFactory;

    protected $table = 'superstarstories';

    protected $fillable = [
        'postedby_userid',
        'file_type',
        'url_path',
        'timestap',
    ];

    protected $casts = [
        'timestap' => 'datetime',
    ];

    /**
     * Get the user who posted the story
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'postedby_userid');
    }

    /**
     * Scope to get only active stories (less than 24 hours old)
     */
    public function scopeActive($query)
    {
        return $query->where('timestap', '>=', now()->subHours(24));
    }

    /**
     * Check if story is still active (less than 24 hours old)
     */
    public function isActive()
    {
        return $this->timestap && $this->timestap->greaterThanOrEqualTo(now()->subHours(24));
    }
}
