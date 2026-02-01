<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperStar\SuperStarAuth;
use App\Http\Controllers\SuperStar\SuperstarPostController;
use App\Http\Controllers\SuperStar\ChatController;
use App\Http\Controllers\SuperStar\PaymentController;
use App\Http\Controllers\SuperStar\SuperstarStoryController;

/*
|--------------------------------------------------------------------------
| SuperStar API Routes
|--------------------------------------------------------------------------
|
| All routes here will be prefixed with /api/superstar
| Public routes do not require authentication
| Protected routes use Sanctum authentication
|
*/

Route::prefix('superstar')->group(function () {

    // Public routes (no authentication)
    Route::post('/login', [SuperStarAuth::class, 'login'])->name('api.superstar.login');

    // Protected routes (Sanctum authentication required)
    Route::middleware('auth:sanctum')->group(function () {

        // SuperStar profile routes
        Route::get('/me', [SuperStarAuth::class, 'me'])->name('api.superstar.me');
        Route::post('/logout', [SuperStarAuth::class, 'logout'])->name('api.superstar.logout');
Route::match(['PUT', 'POST'], '/profile', [SuperStarAuth::class, 'updateProfile'])
    ->name('api.superstar.profile.update');
        Route::post('/change-password', [SuperStarAuth::class, 'changePassword'])->name('api.superstar.password.change');

        // SuperStar Posts CRUD routes
        Route::prefix('posts')->group(function () {
            Route::get('/', [SuperstarPostController::class, 'index'])->name('api.superstar.posts.index');
            Route::post('/', [SuperstarPostController::class, 'store'])->name('api.superstar.posts.store');
            Route::get('/{id}', [SuperstarPostController::class, 'show'])->name('api.superstar.posts.show');
            Route::put('/{id}', [SuperstarPostController::class, 'update'])->name('api.superstar.posts.update');
            Route::delete('/{id}', [SuperstarPostController::class, 'destroy'])->name('api.superstar.posts.destroy');
        });

        // SuperStar Chat routes
        Route::prefix('chat')->group(function () {
            Route::get('/conversations', [ChatController::class, 'getConversations'])->name('api.superstar.chat.conversations');
            Route::get('/unread-count', [ChatController::class, 'getUnreadCount'])->name('api.superstar.chat.unread-count');
            Route::get('/messages/{conversationId}', [ChatController::class, 'getMessages'])->name('api.superstar.chat.messages');
            Route::post('/send/{conversationId}', [ChatController::class, 'sendMessage'])->name('api.superstar.chat.send');
            Route::post('/read/{conversationId}', [ChatController::class, 'markMessagesAsRead'])->name('api.superstar.chat.read');
            Route::put('/conversation/{conversationId}/status', [ChatController::class, 'updateConversationStatus'])->name('api.superstar.chat.update-status');
            Route::delete('/message/{messageId}', [ChatController::class, 'deleteMessage'])->name('api.superstar.chat.delete-message');
        });

        // SuperStar Payment routes
        Route::prefix('payments')->group(function () {
            Route::get('/history', [PaymentController::class, 'getPaymentHistory'])->name('api.superstar.payments.history');
            Route::get('/system-revenue', [PaymentController::class, 'getSystemRevenue'])->name('api.superstar.payments.system-revenue');
            Route::get('/user/{userId}', [PaymentController::class, 'getUserPaymentHistory'])->name('api.superstar.payments.user-history');
        });

        // SuperStar Stories routes
        Route::prefix('stories')->group(function () {
            Route::get('/', [SuperstarStoryController::class, 'index'])->name('api.superstar.stories.index');
            Route::post('/', [SuperstarStoryController::class, 'store'])->name('api.superstar.stories.store');
            Route::get('/{id}', [SuperstarStoryController::class, 'show'])->name('api.superstar.stories.show');
            Route::delete('/{id}', [SuperstarStoryController::class, 'destroy'])->name('api.superstar.stories.destroy');
        });

    });

    // Public routes (no authentication)
    Route::get('/stories/file/{filename}', [SuperstarStoryController::class, 'getFile'])->name('api.superstar.stories.file');

});
