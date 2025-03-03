<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CalendarReleaseController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

// BeeFree Webhook to Postmark
Route::post('/beefree/webhook', function (Request $request) {
    Log::info('BeeFree Webhook Data:', $request->all());

    $templateName = $request->input('name') ?? 'Default Template';
    $htmlBody = $request->input('html');

    if (!$htmlBody) {
        return response()->json(['error' => 'Invalid data received'], 400);
    }

    // Make the API request using Laravel's HTTP client
    $response = Http::withHeaders([
        'X-Postmark-Server-Token' => config('services.postmark.token'),
        'Content-Type' => 'application/json',
    ])->post('https://api.postmarkapp.com/templates', [
        'Name' => $templateName,
        'Subject' => '{{subject}}',
        'HtmlBody' => $htmlBody,
        'TextBody' => strip_tags($htmlBody),
        'TemplateType' => 'standard',
    ]);

    // Log the Postmark response
    Log::info('Postmark Response:', $response->json());

    return response()->json($response->json(), $response->status());
});