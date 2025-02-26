<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\NewListController;
use App\Http\Controllers\BulkEmailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;

// EVERYTHING on "backend.thatdisabilityadventurecompany.com.au"
Route::group([
    'domain' => 'backend.thatdisabilityadventurecompany.com.au',
], function () {

    /*
    |--------------------------------------------------------------------------
    | Public Auth Routes
    |--------------------------------------------------------------------------
    | Everyone hits these to log in, reset password, etc.
    */

    // 1) Show login form at the root GET "/"
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');

    // 2) Handle login form POST "/"
    Route::post('/', [AuthController::class, 'login'])->name('login.submit');

    // 3) Logout (POST "/logout")
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

    // 4) Password Reset
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

    // 5) Verify & Set Password (new users)
    Route::get('/verify-and-set-password/{token}', [AuthController::class, 'showSetPasswordForm'])->name('verify.setPassword');
    Route::post('/verify-and-set-password', [AuthController::class, 'storeSetPassword'])->name('verify.savePassword');

    // 6) Resend verification link
    Route::get('/{id}/resend-verification', [UserController::class, 'resendVerification'])
         ->middleware('auth')
         ->name('users.resendVerification');

    /*
    | After logging in, normal users can see a "/home" or "dashboard" page
    | You can rename it to what you like ("/home" vs. "/dashboard").
    | We'll do "/home" for a normal user landing page
    */
    Route::get('/home', [AuthController::class, 'home'])
         ->middleware('auth')
         ->name('home');
         
    Route::post('/profile/updatename', [ProfileController::class, 'updateName'])
    ->name('profile.updateName')
    ->middleware('auth');

    /*
    |--------------------------------------------------------------------------
    | Admin / Master-Only Routes
    |--------------------------------------------------------------------------
    | If you want "master" or "super-admin" to see certain pages, 
    | apply 'middleware' => ['auth','can:access-backend'] or your custom 'auth','master'.
    | We'll do 'can:access-backend' gate for example.
    */
    Route::group(['middleware' => ['auth','can:access-backend']], function () {

        // Could do Route::prefix('admin') or 'dashboard' if you want a tidy URL
        Route::prefix('dashboard')->group(function () {

            // e.g. GET "/dashboard" => main admin page
            Route::get('/', function () {
                return view('dashboard'); // your admin view
            })->name('dashboard');

            // (1) User Management
            Route::prefix('users')->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('users.index');
                Route::get('/new-user', [UserController::class, 'create'])->name('users.create');
                Route::post('/', [UserController::class, 'store'])->name('users.store');
                Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
                Route::post('/{id}', [UserController::class, 'update'])->name('users.update');
            });

            // (2) Subscription Management
            Route::prefix('subscriptions')->group(function () {
                Route::get('/', [SubscriptionController::class, 'listIndex'])->name('subscriptions.index');
                Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
                Route::post('/unsubscribe', [SubscriptionController::class, 'unsubscribe'])->name('subscriptions.unsubscribe');

                Route::get('/create-list', [NewListController::class, 'create'])->name('subscriptions.create_list');
                Route::post('/create-list', [NewListController::class, 'store'])->name('subscriptions.store_list');
                Route::get('/{listName}', [SubscriptionController::class, 'listShow'])->name('subscriptions.show');
            });

            // (3) Bulk Email
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

        });
    });

});