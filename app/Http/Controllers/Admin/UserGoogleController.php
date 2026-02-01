<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserGoogle;
use Illuminate\Http\Request;

class UserGoogleController extends Controller
{
    /**
     * Display a listing of all UserGoogle users.
     */
    public function index()
    {
        $users = UserGoogle::withCount('superstars')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.usergoogles.index', compact('users'));
    }

    /**
     * Display the specified user's subscriptions.
     */
    public function show($id)
    {
        $user = UserGoogle::with(['superstars' => function($query) {
            $query->select([
                'superstars.id',
                'superstars.user_id',
                'superstars.display_name',
                'superstars.bio',
                'superstars.price_per_hour',
                'superstars.is_available',
                'superstars.rating',
                'superstars.total_followers',
                'superstars.status',
            ])
            ->orderBy('subscribes.created_at', 'desc');
        }])->findOrFail($id);

        return view('admin.usergoogles.show', compact('user'));
    }
}

