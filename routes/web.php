<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SuperstarController;
use App\Http\Controllers\Admin\UserGoogleController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\AdminController;

Route::get('/', function () {
    return view('welcome');
});

// Public story file access
Route::get('/stories/{filename}', function ($filename) {
    $filePath = 'stories/' . $filename;
    
    if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($filePath)) {
        abort(404);
    }
    
    $file = \Illuminate\Support\Facades\Storage::disk('local')->get($filePath);
    $fullPath = storage_path('app/' . $filePath);
    $mimeType = \Illuminate\Support\Facades\File::mimeType($fullPath);
    
    return \Illuminate\Support\Facades\Response::make($file, 200, [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline; filename="' . $filename . '"'
    ]);
});

// Provide a global named login route so the `auth` middleware can redirect to it
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// Redirect accidental /admin/superstars/login to the admin login form to avoid matching the resource show route
Route::get('/admin/superstars/login', function () {
    return redirect()->route('admin.login');
});

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');
    
    // Protected Admin Routes
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::resource('superstars', SuperstarController::class, ['names' => [
            'index' => 'admin.superstars.index',
            'create' => 'admin.superstars.create',
            'store' => 'admin.superstars.store',
            'show' => 'admin.superstars.show',
            'edit' => 'admin.superstars.edit',
            'update' => 'admin.superstars.update',
            'destroy' => 'admin.superstars.destroy',
        ]]);
        
        Route::get('/usergoogles', [UserGoogleController::class, 'index'])->name('admin.usergoogles.index');
        Route::get('/usergoogles/{id}', [UserGoogleController::class, 'show'])->name('admin.usergoogles.show');
        
        Route::get('/finance', [FinanceController::class, 'index'])->name('admin.finance.index');
        Route::get('/finance/{payment}', [FinanceController::class, 'show'])->name('admin.finance.show');
        Route::get('/finance/statistics', [FinanceController::class, 'statistics'])->name('admin.finance.statistics');

        // Admin Management Routes
        Route::get('/admins', [AdminController::class, 'index'])->name('admin.admins.index');
        Route::get('/admins/create', [AdminController::class, 'create'])->name('admin.admins.create');
        Route::post('/admins', [AdminController::class, 'store'])->name('admin.admins.store');
        Route::get('/admins/{admin}', [AdminController::class, 'show'])->name('admin.admins.show');
        Route::get('/admins/{admin}/edit', [AdminController::class, 'edit'])->name('admin.admins.edit');
        Route::put('/admins/{admin}', [AdminController::class, 'update'])->name('admin.admins.update');
        Route::delete('/admins/{admin}', [AdminController::class, 'destroy'])->name('admin.admins.destroy');
    });
});

// Profile Routes (Protected)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
});
