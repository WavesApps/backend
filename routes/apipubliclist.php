<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicAuthList;

/*
|--------------------------------------------------------------------------
| Protected API Routes
|--------------------------------------------------------------------------
|
| These routes require authentication via sanctum tokens
| Used for fetching superstar stories and posts
|
*/

Route::middleware('auth:sanctum')->prefix('public')->group(function () {
    
    // SuperStar Stories routes (protected)
    Route::get('/superstar-stories', [PublicAuthList::class, 'getSuperstarStories'])->name('api.public.superstar-stories');
    
    // SuperStar Posts routes (protected)
    Route::get('/superstar-posts', [PublicAuthList::class, 'getSuperstarPosts'])->name('api.public.superstar-posts');
    
});