<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CalendarReleaseController;

// Force Laravel to load the file
require_once app_path('Http/Controllers/SubscriptionController.php');

// Define the API routes for users
Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [UserController::class, 'index']); // Get all users
    Route::post('/', [UserController::class, 'store']); // Create a new user
    Route::get('{id}', [UserController::class, 'show']); // Get a single user by ID
    Route::put('{id}', [UserController::class, 'update']); // Update a user by ID
    Route::delete('{id}', [UserController::class, 'destroy']); // Delete a user by ID
});

// Define the API routes for subscriptions
Route::middleware('api')->group(function () {
    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::post('/unsubscribe', [SubscriptionController::class, 'unsubscribe']);
});

// Other authenticated routes
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});