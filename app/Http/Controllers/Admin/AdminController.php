<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Display a listing of all admin users.
     */
    public function index(Request $request)
    {
        $admins = User::where('role', 'admin')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('adminpages.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin user.
     */
    public function create()
    {
        return view('adminpages.admins.create');
    }

    /**
     * Store a newly created admin user in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin user created successfully!');
    }

    /**
     * Display the specified admin user.
     */
    public function show(User $admin)
    {
        if ($admin->role !== 'admin') {
            abort(404);
        }

        return view('adminpages.admins.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified admin user.
     */
    public function edit(User $admin)
    {
        if ($admin->role !== 'admin') {
            abort(404);
        }

        return view('adminpages.admins.edit', compact('admin'));
    }

    /**
     * Update the specified admin user in storage.
     */
    public function update(Request $request, User $admin)
    {
        if ($admin->role !== 'admin') {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $admin->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin user updated successfully!');
    }

    /**
     * Remove the specified admin user from storage.
     */
    public function destroy(User $admin)
    {
        if ($admin->role !== 'admin') {
            abort(404);
        }

        // Prevent deletion of the current authenticated admin
        if ($admin->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own admin account!');
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin user deleted successfully!');
    }
}
