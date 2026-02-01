<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Superstar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuperstarController extends Controller
{
    /**
     * Display a listing of all superstars.
     */
    public function index()
    {
        $superstars = Superstar::with('user')->paginate(15);
        return view('admin.superstars.index', compact('superstars'));
    }

    /**
     * Show the form for creating a new superstar.
     */
    public function create()
    {
        return view('admin.superstars.create');
    }

    /**
     * Store a newly created superstar in storage.
     */
    public function store(Request $request)
    {
        // Validate user data
        $userValidated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the user
        $user = User::create([
            'name' => $userValidated['name'],
            'username' => $userValidated['username'],
            'email' => $userValidated['email'],
            'phone' => $userValidated['phone'],
            'password' => Hash::make($userValidated['password']),
            'role' => 'superstar',
            'is_verified' => true,
        ]);

        // Validate superstar data
        $superstarValidated = $request->validate([
            'display_name' => 'required|string|max:100',
            'bio' => 'nullable|string',
            'price_per_hour' => 'nullable|numeric|min:0|max:9999.99',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        // Create superstar with user_id
        $superstarValidated['user_id'] = $user->id;
        $superstarValidated['is_available'] = true;
        $superstarValidated['total_followers'] = 0;
        $superstarValidated['status'] = 'active';
        $superstarValidated['rating'] = $superstarValidated['rating'] ?? 0;

        Superstar::create($superstarValidated);

        return redirect()->route('admin.superstars.index')
                        ->with('success', 'Superstar and user created successfully!');
    }

    /**
     * Display the specified superstar.
     */
    public function show(Superstar $superstar)
    {
        $superstar->load('user');
        return view('admin.superstars.show', compact('superstar'));
    }

    /**
     * Show the form for editing the specified superstar.
     */
    public function edit(Superstar $superstar)
    {
        $assignedUserIds = Superstar::where('id', '!=', $superstar->id)->pluck('user_id');
        $users = User::where('id', $superstar->user_id)
                    ->orWhereNotIn('id', $assignedUserIds)
                    ->get();
        return view('admin.superstars.edit', compact('superstar', 'users'));
    }

    /**
     * Update the specified superstar in storage.
     */
    public function update(Request $request, Superstar $superstar)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:superstars,user_id,' . $superstar->id,
            'display_name' => 'required|string|max:100',
            'bio' => 'nullable|string',
            'price_per_hour' => 'nullable|numeric|min:0|max:9999.99',
            'is_available' => 'boolean',
            'rating' => 'nullable|numeric|min:0|max:5',
            'total_followers' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $validated['is_available'] = $request->has('is_available');

        $superstar->update($validated);

        return redirect()->route('admin.superstars.show', $superstar)
                        ->with('success', 'Superstar updated successfully!');
    }

    /**
     * Remove the specified superstar from storage.
     */
    public function destroy(Superstar $superstar)
    {
        $superstar->delete();

        return redirect()->route('admin.superstars.index')
                        ->with('success', 'Superstar deleted successfully!');
    }
}
