<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
// If you have a Subscription model or something similar:
use App\Models\Subscription;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Postmark\PostmarkClient;
// Example mailable (you’d create it via php artisan make:mail VerificationEmail)
use App\Mail\VerificationEmail;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');

        $query = User::query();

        // Existing search logic
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('user_type', 'like', "%{$search}%");
            });
        }

        // Allowed sorts
        $allowedSorts = ['name', 'last_name', 'email', 'user_type', 'gender'];
        if (! in_array($sort, $allowedSorts)) {
            $sort = 'name';
        }
        $direction = ($direction === 'desc') ? 'desc' : 'asc';

        $query->orderBy($sort, $direction);

        $users = $query->get();

        return view('users.index', [
            'users'     => $users,
            'search'    => $search,
            'sort'      => $sort,
            'direction' => $direction,
        ]);
    }
    
    public function create()
    {
        $userTypes = User::whereNotNull('user_type')
                         ->distinct()
                         ->pluck('user_type')
                         ->filter(function ($type) {
                             return strtolower($type) !== 'master';
                         });

        $genders = User::whereNotNull('gender')
                       ->distinct()
                       ->pluck('gender');

        return view('users.create', compact('userTypes', 'genders'));
    }

    /**
     * Store a newly created user WITHOUT a password,
     * then send them a verification email to set their password.
     */
    public function store(Request $request)
    {
        // Remove 'password' from validation since we'll do it via verification link
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'user_type' => 'required|string|max:50',
            'gender' => 'nullable|string|max:10',
        ]);

        // Create user
        $data = [
        'name'       => $request->name,
        'last_name'  => $request->last_name,
        'email'      => $request->email,
        'user_type'  => strtolower($request->input('user_type')),
        'gender'     => $request->filled('gender')
                        ? strtolower($request->input('gender'))
                        : null,
        ];

        // Create the user
        $user = User::create($data);

        // 1) Send verification link so they can confirm email & set password
        $this->sendVerificationEmail($user);

        // 2) Optionally auto-subscribe based on user_type or gender
        //$this->autoSubscribeByTypeAndGender($user);

        return redirect()
        ->route('users.create')
        ->with('newUserCreated', [
            'name'      => $user->name,
            'last_name' => $user->last_name,
            'email'     => $user->email,
            'id'        => $user->id,
        ]);
    }

    public function show($id, Request $request)
    {
        $user = User::findOrFail($id);

        $userTypes = User::whereNotNull('user_type')
                         ->distinct()
                         ->pluck('user_type')
                         ->filter(fn($type) => strtolower($type) !== 'master');

        $genders = User::whereNotNull('gender')
                       ->distinct()
                       ->pluck('gender');

        if ($request->wantsJson()) {
            return response()->json($user);
        }

        return view('users.show', compact('user', 'userTypes', 'genders'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'email' => 'email|unique:users,email,' . $id,
            'name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'user_type' => 'string|max:50',
            'gender' => 'nullable|string|max:10',
            'password' => 'nullable|min:6',
        ]);

        $data = $request->only(['name', 'last_name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $data['user_type'] = strtolower($request->input('user_type'));
        $data['gender']    = $request->filled('gender')
            ? strtolower($request->input('gender'))
            : null;

        $user->update($data);

        return redirect()->route('users.show', $user->id)
                         ->with('status', 'User updated successfully!');
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * PRIVATE METHODS
     */

    /**
     * Sends an email verification link so the user can confirm email & set password.
     */
    private function sendVerificationEmail(User $user)
{
    // 1) Generate random token & store in DB
    $plainToken = Str::random(60);

    DB::table('verify_tokens')->insert([
        'email'      => $user->email,
        'token'      => $plainToken,
        'created_at' => now(),
    ]);

    // 2) Build the verification URL
    $verificationUrl = url('/verify-and-set-password/' . $plainToken);
    
    \Log::info('sendVerificationEmail called', ['email' => $user->email]);

    // 3) Postmark client
    $client = new PostmarkClient(config('services.postmark.token'));
    $fromEmail = config('services.postmark.from_email'); // must be verified in Postmark

    // 4) Postmark template ID & model
    //    If you have a postmark template set up specifically for verification:
    $templateId = 39165532; // your numeric template ID
    $templateModel = [
        "accountCreationUrl" => $verificationUrl
    ];

    // 5) Actually send
    try {
        \Log::info('Attempting to send Postmark verification email', ['email' => $user->email]);
        $client->sendEmailWithTemplate(
            $fromEmail,
            $user->email,
            $templateId,
            $templateModel,
            true,              // inline css
            'Account Creation', // Tag
            true,              // track opens
            null,              // replyTo
            null,              // cc
            null,              // bcc
            null,              // headers
            null,              // attachments
            'None',            // trackLinks
            null,              // metaData
            'admin' // messageStream
        );
    } catch (\Exception $e) {
        \Log::error('Postmark verification email failed: ' . $e->getMessage());
        // handle or rethrow if needed
    }
}

    /**
     * Automatically subscribes the user based on user_type or gender.
     */
    private function autoSubscribeByTypeAndGender(User $user)
    {
        // Example logic:
        if ($user->user_type === 'mentor') {
            // subscribe to a 'mentor_newsletter'
            Subscription::updateOrCreate(
                ['user_id' => $user->id, 'list_name' => 'mentor_newsletter'],
                ['subscribed' => true]
            );
        }

        // If gender-based:
        if ($user->gender === 'male') {
            Subscription::updateOrCreate(
                ['user_id' => $user->id, 'list_name' => 'male_events'],
                ['subscribed' => true]
            );
        }
    }
    
    public function showSetPasswordForm($token)
{
    // check if record for token exists:
    $record = DB::table('verify_tokens')->where('token', $token)->first();
    if (!$record) {
        return redirect()->route('login')->withErrors('Invalid or expired token.');
    }

    // If valid, show a form so user can choose a password
    return view('auth.set-password', ['token' => $token]);
}

public function storeSetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'password' => 'required|min:6|confirmed',
    ]);

    // Find token
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

    // Delete the token
    DB::table('verify_tokens')->where('email', $record->email)->delete();

    // If the user ticked "Subscribe to newsletter"
    if ($request->has('subscribe')) {
        // subscribe logic, e.g.
        Subscription::updateOrCreate(
            ['user_id' => $user->id, 'list_name' => 'newsletter'],
            ['subscribed' => true]
        );
    }

    // redirect or log in
    return redirect()->route('login')->with('status', 'Password set! You are verified and subscribed if you selected that option.');
}

public function resendVerification($id)
{
    // Check if the logged-in user's ID matches $id
    if (auth()->id() != $id) {
        // If you also want to allow master/admin to do it for others, you can add a condition like:
        // if (auth()->user()->user_type !== 'master')
        //    abort(403, 'You cannot resend verification for someone else!');

        abort(403, 'You cannot resend verification for someone else!');
    }

    $user = User::findOrFail($id);

    // If the user is already verified, skip or show an error
    if ($user->email_verified_at) {
        return redirect()->back()->withErrors('User is already verified.');
    }

    // Reuse your verification logic
    $this->sendVerificationEmail($user);

    return redirect()->back()->with('status', 'Verification email resent to ' . $user->email);
}
}