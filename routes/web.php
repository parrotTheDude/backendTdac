<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, PasswordResetController, UserController, SubscriptionController,
    NewListController, BulkEmailController, EventController, ProfileController,
    SettingsController, BookingsController, WebhookController
};

Route::group(['domain' => 'accounts.thatdisabilityadventurecompany.com.au'], function () {
    /*
    |--------------------------------------------------------------------------
    | Public (Unauthenticated) Auth Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/', [AuthController::class, 'login'])->name('login.submit');

    // Password Reset
    Route::prefix('password')->group(function () {
        Route::get('/forgot', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('/forgot', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
        Route::get('/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset', [PasswordResetController::class, 'resetPassword'])->name('password.update');
    });

    // Verify & Set Password
    Route::prefix('verify-and-set-password')->group(function () {
        Route::get('/{token}', [AuthController::class, 'showSetPasswordForm'])->name('verify.setPassword');
        Route::post('/', [AuthController::class, 'storeSetPassword'])->name('verify.savePassword');
    });
    
    // Subscribe and unsubscribe
    Route::post('/webhook/subscription-change', [WebhookController::class, 'handleSubscriptionChange'])
     ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
     ->name('webhook.subscriptionChange');

    /*
    |--------------------------------------------------------------------------
    | Authenticated User Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/home', function () { return view('home'); })->name('home');
        
        // Profile Management
        Route::prefix('profile')->group(function () {
            Route::get('/update-name', [ProfileController::class, 'updateNameForm'])->name('profile.updateName');
            Route::post('/update-name', [ProfileController::class, 'updateName'])->name('profile.saveName');
            Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
            Route::post('/', [ProfileController::class, 'update'])->name('profile.update');
        });
    });

    // Verification & Reset Link
    Route::middleware('auth')->group(function () {
        Route::get('/{id}/resend-verification', [UserController::class, 'resendVerification'])->name('users.resendVerification');
        Route::post('/send-reset-link', [PasswordResetController::class, 'sendResetLink'])->name('password.sendResetLink');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin / Master-Only Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'can:access-backend'])->group(function () {
        Route::prefix('dashboard')->group(function () {
            Route::get('/', function () { return view('dashboard'); })->name('dashboard');

            // User Management
            Route::prefix('users')->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('users.index');
                Route::get('/new-user', [UserController::class, 'create'])->name('users.create');
                Route::post('/', [UserController::class, 'store'])->name('users.store');
                Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
                Route::post('/{id}', [UserController::class, 'update'])->name('users.update');
                Route::get('/export', [UserController::class, 'export'])->name('users.export');
            });

            // Subscription Management
            Route::prefix('subscriptions')->group(function () {
                Route::get('/', [SubscriptionController::class, 'listIndex'])->name('subscriptions.index');
                Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
                Route::post('/unsubscribe', [SubscriptionController::class, 'unsubscribe'])->name('subscriptions.unsubscribe');
                Route::get('/create-list', [NewListController::class, 'create'])->name('subscriptions.create_list');
                Route::post('/create-list', [NewListController::class, 'store'])->name('subscriptions.store_list');
                Route::get('/{listName}', [SubscriptionController::class, 'listShow'])->name('subscriptions.show');
            });

            // Bulk Email
            Route::prefix('bulk-emails')->group(function () {
                Route::get('/', [BulkEmailController::class, 'index'])->name('bulk-emails.index');
                Route::post('/send', [BulkEmailController::class, 'send'])->name('bulk-emails.send');
                Route::get('/progress/{id}', [BulkEmailController::class, 'progress'])->name('bulk-emails.progress');
                Route::get('/history', [BulkEmailController::class, 'history'])->name('bulk-emails.history');
                Route::get('/subscriber-count/{listName}', [BulkEmailController::class, 'subscriberCount'])->name('bulk-emails.subscriber-count');
                Route::get('/templates', [BulkEmailController::class, 'fetchTemplates'])->name('bulk-emails.templates');
            });

            // Events
            Route::resource('events', EventController::class);

            // Settings
            Route::prefix('settings')->group(function () {
                Route::get('/schedule-pages', [SettingsController::class, 'schedulePages'])->name('settings.schedulePages');
            });

            // Bookings
            Route::get('/bookings', [BookingsController::class, 'index'])->name('bookings.index');
        });
    });
});

