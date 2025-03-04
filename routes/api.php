<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CalendarReleaseController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Users API
Route::prefix('users')->middleware('api')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('{id}', [UserController::class, 'show']);
    Route::put('{id}', [UserController::class, 'update']);
    Route::delete('{id}', [UserController::class, 'destroy']);
});

// Subscriptions API
Route::get('/subscriptions', [SubscriptionController::class, 'index']);
Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
Route::post('/unsubscribe', [SubscriptionController::class, 'unsubscribe']);

// Contact Form API
Route::post('/contact', [ContactController::class, 'store']);

// BeeFree Webhook → Postmark
Route::post('/beefree/webhook', function (Request $request) {
    Log::info('BeeFree Webhook Data:', $request->all());

    $templateName = $request->input('name') ?? 'Default Template';
    $htmlBody = $request->input('html');

    if (!$htmlBody || !$templateName) {
        Log::error('BeeFree Webhook Missing Data', ['data' => $request->all()]);
        return response()->json(['error' => 'Missing required fields'], 400);
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