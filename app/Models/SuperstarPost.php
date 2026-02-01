<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuperstarPost extends Model
{
    protected $fillable = [
        'user_id',
        'media_type',
        'resource_type',
        'resource_url_path',
        'description',
        'is_pg',
        'is_disturbing'
    ];

    protected $casts = [
        'is_pg' => 'boolean',
        'is_disturbing' => 'boolean'
    ];

    public function superstar()
    {
        return $this->belongsTo(Superstar::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
