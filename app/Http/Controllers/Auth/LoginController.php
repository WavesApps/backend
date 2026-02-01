<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(Request $request)
    {
        // If user is already authenticated, redirect to dashboard
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        // Attempt to authenticate
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Check if user is blocked
            if ($user->is_blocked) {
                Auth::logout();
                return redirect()->back()
                    ->withErrors(['email' => 'Your account has been blocked. Please contact support.'])
                    ->withInput($request->only('email'));
            }

            // Check if user is superstar - they should use the app, not web login
            if ($user->role === 'superstar') {
                Auth::logout();
                return redirect()->back()
                    ->with('superstar_error', 'Please use the app to login. Web login is only available for administrators.')
                    ->withInput($request->only('email'));
            }

            // Only admin role can proceed
            if ($user->role === 'admin') {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }

            // If role is neither admin nor superstar, logout
            Auth::logout();
            return redirect()->back()
                ->withErrors(['email' => 'Unauthorized access.'])
                ->withInput($request->only('email'));
        }

        // Authentication failed
        return redirect()->back()
            ->withErrors(['email' => 'These credentials do not match our records.'])
            ->withInput($request->only('email'));
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->withHeaders([
                'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
    }
}

