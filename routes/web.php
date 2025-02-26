<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\NewListController;
use App\Http\Controllers\BulkEmailController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\BookingsController;

Route::group([
    'domain' => 'accounts.thatdisabilityadventurecompany.com.au',
], function () {

    /*
    |--------------------------------------------------------------------------
    | Public (Unauthenticated) Auth Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');      // GET /
    Route::post('/', [AuthController::class, 'login'])->name('login.submit');     // POST /

    // Password Reset
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])
         ->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
         ->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
         ->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
         ->name('password.update');

    // Verify & Set Password (brand-new users)
    Route::get('/verify-and-set-password/{token}', [AuthController::class, 'showSetPasswordForm'])
         ->name('verify.setPassword');
    Route::post('/verify-and-set-password', [AuthController::class, 'storeSetPassword'])
         ->name('verify.savePassword');

    /*
    |--------------------------------------------------------------------------
    | Authenticated User Routes
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', [AuthController::class, 'logout'])
         ->middleware('auth')
         ->name('logout');

    Route::get('/home', [AuthController::class, 'home'])
         ->middleware('auth')
         ->name('home');

    // Update name (for incomplete user profiles)
    Route::post('/profile/updatename', [ProfileController::class, 'updateName'])
         ->middleware('auth')
         ->name('profile.updateName');

    // Resend verification link (only if user is logged in)
    Route::get('/{id}/resend-verification', [UserController::class, 'resendVerification'])
         ->middleware('auth')
         ->name('users.resendVerification');
         
         
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

    /*
    |--------------------------------------------------------------------------
    | Admin / Master-Only Routes
    |--------------------------------------------------------------------------
    | Protect these with 'auth' plus a gate or role check, e.g. 'can:access-backend'.
    | Alternatively, if you have a custom 'master' or 'superadmin' middleware, use that.
    */
    Route::group(['middleware' => ['auth','can:access-backend']], function () {

        Route::prefix('dashboard')->group(function () {

            // Main admin dashboard
            Route::get('/', function () {
                return view('dashboard');
            })->name('dashboard');

            // 1) User Management
            Route::prefix('users')->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('users.index');
                Route::get('/new-user', [UserController::class, 'create'])->name('users.create');
                Route::post('/', [UserController::class, 'store'])->name('users.store');
                Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
                Route::post('/{id}', [UserController::class, 'update'])->name('users.update');
            });

            // 2) Subscription Management
            Route::prefix('subscriptions')->group(function () {
                Route::get('/', [SubscriptionController::class, 'listIndex'])->name('subscriptions.index');
                Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
                Route::post('/unsubscribe', [SubscriptionController::class, 'unsubscribe'])->name('subscriptions.unsubscribe');
                Route::get('/create-list', [NewListController::class, 'create'])->name('subscriptions.create_list');
                Route::post('/create-list', [NewListController::class, 'store'])->name('subscriptions.store_list');
                Route::get('/{listName}', [SubscriptionController::class, 'listShow'])->name('subscriptions.show');
            });

            // 3) Bulk Email
            Route::prefix('bulk-emails')->group(function () {
                Route::get('/', [BulkEmailController::class, 'index'])->name('bulk-emails.index');
                Route::post('/send', [BulkEmailController::class, 'send'])->name('bulk-emails.send');
                Route::get('/progress/{id}', [BulkEmailController::class, 'progress'])->name('bulk-emails.progress');
                Route::get('/history', [BulkEmailController::class, 'history'])->name('bulk-emails.history');
                Route::get('/subscriber-count/{listName}', [BulkEmailController::class, 'subscriberCount'])->name('bulk-emails.subscriber-count');
                Route::get('/templates', [BulkEmailController::class, 'fetchTemplates'])->name('bulk-emails.templates');
            });

            // 4) Events
            Route::resource('events', EventController::class);
            
            Route::prefix('settings')->group(function () {
    Route::get('/schedule-pages', [SettingsController::class, 'schedulePages'])
         ->name('settings.schedulePages');
});


Route::get('/bookings', [BookingsController::class, 'index'])
     ->name('bookings.index');

        });
    });
});