<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Subscription;

class AuthController extends Controller
{
    // Display login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

public function login(Request $request)
{
    // Validate user input
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    // Grab credentials
    $credentials = $request->only('email', 'password');

    // Remember me functionality
    $remember = $request->filled('remember');

    // Attempt login
    if (Auth::attempt($credentials, $remember)) {
        $user = Auth::user();

        // Redirect based on user role
        if ($user->can('access-backend')) {
            return redirect()->intended('/dashboard')->with('status', 'Welcome back, Admin!');
        }

        return redirect()->intended('/home')->with('status', 'Welcome back!');
    }

    // If login fails
    return back()->withErrors([
        'email' => 'Invalid credentials.',
    ]);
}

public function home()
{
    // By the time we reach here, user is definitely logged in (auth middleware).
    return view('account.home'); // A blade view in resources/views/account/home.blade.php
}

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
    
    public function showSetPasswordForm($token)
    {
        // Check if record for token exists in verify_tokens table
        $record = DB::table('verify_tokens')->where('token', $token)->first();

        if (!$record) {
            // Token not found or expired
            return redirect()->route('login')->withErrors('Invalid or expired token.');
        }

        // If found, show a form so user can choose a new password
        return view('auth.set-password', ['token' => $token]);
    }

    /**
     * Processes the new password from the form,
     * sets the user's password, verifies them, then cleans up the token.
     */
    public function storeSetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        // Look up token
        $record = DB::table('verify_tokens')->where('token', $request->token)->first();

        if (!$record) {
            return redirect()->route('login')->withErrors('Invalid or expired token.');
        }

        // Find user by email
        $user = User::where('email', $record->email)->firstOrFail();

        // Set password + mark email verified
        $user->password = Hash::make($request->password);
        $user->email_verified_at = now();
        $user->save();

        // Delete the token so it can’t be reused
        DB::table('verify_tokens')->where('email', $record->email)->delete();
        
        // **Subscribe if the box was checked**
        if ($request->has('subscribe')) {
            Subscription::updateOrCreate(
                ['user_id' => $user->id, 'list_name' => 'newsletter'],
                ['subscribed' => true]
            );
        }

        // Possibly log them in or redirect them
        return redirect()->route('login')->with('status', 'Password set! Your email is verified.');
    }
}