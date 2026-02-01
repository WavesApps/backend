<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserApi\UserApiLogin;
use App\Http\Controllers\UserApi\SubscriptionController;
use App\Http\Controllers\UserApi\UserSuperStarController;
use App\Http\Controllers\UserApi\UserSuperstarInfoController;
use App\Http\Controllers\UserApi\ChatController;
use App\Http\Controllers\UserApi\UserConversationController;
use App\Http\Controllers\UserApi\PaymentController;
use App\Http\Controllers\UserApi\PaymentHistoryController;

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
|
| All routes here will be prefixed with /api/user
| Public routes do not require authentication
| Protected routes use Sanctum authentication
|
*/

Route::prefix('user')->group(function () {

    // Public routes (no authentication)
    Route::post('/google-login', [UserApiLogin::class, 'googleLogin'])->name('api.user.google-login');

    // Protected routes (Sanctum authentication required)
    Route::middleware('auth:sanctum')->group(function () {

        // Auth user routes
        Route::get('/me', [UserApiLogin::class, 'getAuthUser'])->name('api.user.me');
        Route::post('/logout', [UserApiLogin::class, 'logout'])->name('api.user.logout');

        // Subscription routes
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('api.user.subscriptions.index');
        Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('api.user.subscriptions.store');
        Route::post('/subscriptions/{superstarId}', [SubscriptionController::class, 'subscribe'])->name('api.user.subscriptions.subscribe');
        Route::delete('/subscriptions/{superstarId}', [SubscriptionController::class, 'destroy'])->name('api.user.subscriptions.destroy');

        // SuperStars: list, details, and posts
        Route::get('/superstars', [UserSuperStarController::class, 'index'])->name('api.user.superstars.index');
        Route::get('/superstars/{id}', [UserSuperStarController::class, 'show'])->name('api.user.superstars.show');
        Route::get('/superstars/{id}/posts', [UserSuperStarController::class, 'posts'])->name('api.user.superstars.posts');
        Route::get('/superstars/{id}/details', [UserSuperstarInfoController::class, 'superstarDetails'])->name('api.user.superstars.details');

        // SuperStars info with subscription status
        Route::get('/usersuperstar', [UserSuperstarInfoController::class, 'usersuperstar'])->name('api.user.usersuperstar');
        
        // Feed: Posts from subscribed superstars
        Route::get('/feed', [UserSuperstarInfoController::class, 'feed'])->name('api.user.feed');

        // Chat routes
        Route::prefix('chat')->group(function () {
            Route::get('/conversations', [ChatController::class, 'getConversations'])->name('api.user.chat.conversations');
            Route::get('/unread-count', [ChatController::class, 'getUnreadCount'])->name('api.user.chat.unread-count');
            Route::post('/start/{superstarId}', [ChatController::class, 'startChat'])->name('api.user.chat.start');
            Route::get('/messages/{conversationId}', [ChatController::class, 'getMessages'])->name('api.user.chat.messages');
            Route::post('/send/{conversationId}', [ChatController::class, 'sendMessage'])->name('api.user.chat.send');
            Route::post('/read/{conversationId}', [ChatController::class, 'markMessagesAsRead'])->name('api.user.chat.read');
            Route::put('/conversation/{conversationId}/status', [ChatController::class, 'updateConversationStatus'])->name('api.user.chat.update-status');
            Route::delete('/message/{messageId}', [ChatController::class, 'deleteMessage'])->name('api.user.chat.delete-message');
        });

        Route::get('/conversations', [UserConversationController::class, 'getConversations'])->name('api.user.conversations.index');
        Route::get('/conversations/{conversationId}/messages', [UserConversationController::class, 'getMessages'])->name('api.user.conversations.messages');
        Route::post('/conversations/{conversationId}/messages', [UserConversationController::class, 'sendMessage'])->name('api.user.conversations.send');
        Route::post('/conversations/{conversationId}/read', [UserConversationController::class, 'markMessagesAsRead'])->name('api.user.conversations.read');
        Route::get('/unread-count', [UserConversationController::class, 'getUnreadCount'])->name('api.user.unread-count');
        Route::put('/conversations/{conversationId}/status', [UserConversationController::class, 'updateConversationStatus'])->name('api.user.conversations.update-status');
        Route::delete('/messages/{messageId}', [UserConversationController::class, 'deleteMessage'])->name('api.user.messages.delete');

        // Payment routes
        Route::prefix('payments')->group(function () {
            Route::post('/process', [PaymentController::class, 'processPayment'])->name('api.user.payments.process');
            Route::get('/history', [PaymentController::class, 'getPaymentHistory'])->name('api.user.payments.history');
            Route::get('/{paymentId}', [PaymentController::class, 'getPaymentDetails'])->name('api.user.payments.details');
        });

        // Payment History routes
        Route::prefix('payment-history')->group(function () {
            Route::get('/user', [PaymentHistoryController::class, 'getUserPayments'])->name('api.user.payment-history.user');
            Route::get('/superstar/{superstarId}', [PaymentHistoryController::class, 'getPaymentsBySuperstar'])->name('api.user.payment-history.superstar');
            Route::get('/transaction/{transactionReference}', [PaymentHistoryController::class, 'getTransactionDetails'])->name('api.user.payment-history.transaction');
        });

    });

});
