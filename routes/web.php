<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\UserController;

// Display login form as homepage
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');

// Handle login form submission
Route::post('/', [AuthController::class, 'login'])->name('login.submit');

// Protected route after login (example)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Display form
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');

// Handle form submission
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');

// Display password reset form
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');

// Handle password reset form submission
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// Gets users
Route::get('/dashboard/users', [UserController::class, 'index'])->name('users.index');

Route::get('/dashboard/users/{id}', [UserController::class, 'show'])->name('users.show');
Route::post('/dashboard/users/{id}', [UserController::class, 'update'])->name('users.update');

Route::get('/dashboard/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/dashboard/users', [UserController::class, 'store'])->name('users.store');