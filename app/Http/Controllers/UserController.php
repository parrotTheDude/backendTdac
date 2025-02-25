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
            // 'password' => 'nullable|min:6', <-- removed or made optional
        ]);

        // Build your data array, no password
        $data = [
            'name'      => $request->name,
            'last_name' => $request->last_name,
            'email'     => $request->email,
            'user_type' => strtolower($request->input('user_type')),
            'gender'    => $request->filled('gender')
                            ? strtolower($request->input('gender'))
                            : null,
            'password'  => null, // No password for now
        ];

        // Create the user
        $user = User::create($data);

        // 1) Send verification link so they can confirm email & set password
        $this->sendVerificationEmail($user);

        // 2) Optionally auto-subscribe based on user_type or gender
        $this->autoSubscribeByTypeAndGender($user);

        return redirect()->route('users.index')
                         ->with('status', 'User created! Verification link sent.');
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
        // Example approach: create a random token, store in a 'verify_tokens' table or use 'password_resets' table
        $plainToken = Str::random(60);

        // Up to you if you want to hash it or store plain. 
        // For simplicity, store plain in a table 'verify_tokens'
        DB::table('verify_tokens')->insert([
            'email'      => $user->email,
            'token'      => $plainToken, // or hash it
            'created_at' => now(),
        ]);

        // Now send the user an email with the link
        // e.g. mysite.com/verify-and-set-password/{token}
        $verificationUrl = url("/verify-and-set-password/$plainToken");

        // If you have a Mailable:
        Mail::to($user->email)->send(new VerificationEmail($user, $verificationUrl));

        // Or if you want to do a raw mail:
        /*
        Mail::raw(\"Hello {$user->name},\\nClick here to verify: {$verificationUrl}\", function($message) use ($user){
            $message->to($user->email)
                    ->subject('Verify Your Email & Set Password');
        });
        */
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
}