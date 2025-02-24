<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\NewListController;

// Display login form as homepage
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');

// Handle login form submission
Route::post('/', [AuthController::class, 'login'])->name('login.submit');

// Display form to request password reset
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');

// Handle sending reset email
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');

// Display password reset form
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');

// Handle password reset form submission
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// Everything inside this group requires authentication
Route::middleware('auth')->group(function () {
    
    // Dashboard Home
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // User Management
    Route::get('/dashboard/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/dashboard/users/new-user', [UserController::class, 'create'])->name('users.create');
    Route::post('/dashboard/users', [UserController::class, 'store'])->name('users.store');

    Route::get('/dashboard/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::post('/dashboard/users/{id}', [UserController::class, 'update'])->name('users.update');

    // Logout route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard/subscriptions', [SubscriptionController::class, 'listIndex'])
     ->name('subscriptions.index');
     
     Route::post('/dashboard/subscriptions/subscribe', [SubscriptionController::class, 'subscribe'])
     ->name('subscriptions.subscribe');
     
Route::post('/dashboard/subscriptions/unsubscribe', [SubscriptionController::class, 'unsubscribe'])
     ->name('subscriptions.unsubscribe');
     
// Must go first so 'create-list' isn't captured by {listName}
Route::get('/dashboard/subscriptions/create-list', [NewListController::class, 'create'])
     ->name('subscriptions.create_list');

Route::post('/dashboard/subscriptions/create-list', [NewListController::class, 'store'])
     ->name('subscriptions.store_list');

// Then the wildcard
Route::get('/dashboard/subscriptions/{listName}', [SubscriptionController::class, 'listShow'])
     ->name('subscriptions.show');
});