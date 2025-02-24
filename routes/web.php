<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

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
