<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\NewListController;
use App\Http\Controllers\BulkEmailController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// Protected Dashboard Routes
Route::middleware('auth')->prefix('dashboard')->group(function () {

    // Dashboard Home
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    // User Management
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/new-user', [UserController::class, 'create'])->name('users.create');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
        Route::post('/{id}', [UserController::class, 'update'])->name('users.update');
    });

    // Subscription Management
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [SubscriptionController::class, 'listIndex'])->name('subscriptions.index');
        Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
        Route::post('/unsubscribe', [SubscriptionController::class, 'unsubscribe'])->name('subscriptions.unsubscribe');

        // Create Subscription List
        Route::get('/create-list', [NewListController::class, 'create'])->name('subscriptions.create_list');
        Route::post('/create-list', [NewListController::class, 'store'])->name('subscriptions.store_list');

        // Subscription List Details
        Route::get('/{listName}', [SubscriptionController::class, 'listShow'])->name('subscriptions.show');
    });

    // Bulk Email Management
    Route::prefix('bulk-emails')->group(function () {
        // Bulk email index (shows the form & partial history)
        Route::get('/', [BulkEmailController::class, 'index'])->name('bulk-emails.index');

        // Sending endpoint
        Route::post('/send', [BulkEmailController::class, 'send'])->name('bulk-emails.send');

        // Optional real-time progress
        Route::get('/progress/{id}', [BulkEmailController::class, 'progress'])->name('bulk-emails.progress');

        // Full history page
        Route::get('/history', [BulkEmailController::class, 'history'])->name('bulk-emails.history');

        // If you want a route to get the subscriber count
        Route::get('/subscriber-count/{listName}', [BulkEmailController::class, 'subscriberCount'])
         ->name('bulk-emails.subscriber-count');
         
         Route::get('/bulk-emails/templates', [BulkEmailController::class, 'fetchTemplates'])
            ->name('bulk-emails.templates');
    });
});